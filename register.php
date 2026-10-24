<?php

session_start();

$message = '';

// Initialize email verification session state
if (!isset($_SESSION['email_verification'])) {
    $_SESSION['email_verification'] = [
        'email' => null,
        'code' => null,
        'expires_at' => null,
        'verified' => false
    ];
}

// Preserve in-progress registration fields across verification submits
if (!isset($_SESSION['pending_registration'])) {
    $_SESSION['pending_registration'] = [
        'username' => '',
        'password' => ''
    ];
}

function generate_verification_code($length = 6)
{
    $digits = '0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $digits[random_int(0, strlen($digits) - 1)];
    }
    return $code;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'register';

    // Capture fields from any submit to keep them sticky
    if (isset($_POST['username'])) {
        $_SESSION['pending_registration']['username'] = (string) $_POST['username'];
    }
    if (isset($_POST['password'])) {
        $_SESSION['pending_registration']['password'] = (string) $_POST['password'];
    }

    if ($action === 'send_code') {
        $rawEmail = trim($_POST['email'] ?? '');
        if (!filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
        } else {
            $code = generate_verification_code();
            $_SESSION['email_verification']['email'] = $rawEmail;
            $_SESSION['email_verification']['code'] = $code;
            $_SESSION['email_verification']['expires_at'] = time() + 600; // 10 minutes
            $_SESSION['email_verification']['verified'] = false;

            // Load SMTP config and pass to Python mailer
            $smtp = @include __DIR__ . '/smtp_config.php';
            if (!is_array($smtp)) {
                $smtp = [];
            }
            $host = escapeshellarg($smtp['SMTP_HOST'] ?? 'smtp.gmail.com');
            $port = escapeshellarg((string) ($smtp['SMTP_PORT'] ?? '587'));
            $user = escapeshellarg($smtp['SMTP_USER'] ?? '');
            $pass = escapeshellarg($smtp['SMTP_PASS'] ?? '');
            $fromEmail = escapeshellarg($smtp['FROM_EMAIL'] ?? '');
            $fromName = escapeshellarg($smtp['FROM_NAME'] ?? 'Account Verification');

            $emailArg = escapeshellarg($rawEmail);
            $codeArg = escapeshellarg($code);

            // Try different Python executable paths
            $pythonPaths = [
                'C:\\Users\\raymo\\AppData\\Local\\Programs\\Python\\Python313\\python.exe',
                'python',
                'py',
                'C:\\Python\\python.exe'
            ];
            $sendOutput = '';
            $success = false;
            $debugInfo = [];

            foreach ($pythonPaths as $pythonPath) {
                // Change to the script directory and run the command
                $scriptDir = __DIR__;
                $cmd = "cd /d \"$scriptDir\" && $pythonPath send_code.py $emailArg $codeArg --smtp-host $host --smtp-port $port --smtp-user $user --smtp-pass $pass --from-email $fromEmail --from-name $fromName 2>&1";
                $sendOutput = shell_exec($cmd);
                $debugInfo[] = "Command: $cmd | Output: " . ($sendOutput ?: 'No output');

                if ($sendOutput && strpos($sendOutput, 'sent') !== false) {
                    $success = true;
                    break;
                }
            }

            if ($success) {
                $message = 'Verification code sent. Please check your email.';
            } else {
                $message = 'Failed to send email. Please try again or contact support.';
                // Log debug info for troubleshooting
                error_log("Email sending failed. Debug info: " . implode(' | ', $debugInfo));
            }
        }
    } elseif ($action === 'verify_code') {
        $inputCode = trim($_POST['verify_code'] ?? '');
        $state = $_SESSION['email_verification'];
        if (!$state['code'] || !$state['expires_at'] || time() > $state['expires_at']) {
            $message = 'Verification code expired. Please resend.';
        } elseif ($inputCode !== $state['code']) {
            $message = 'Invalid verification code.';
        } else {
            $_SESSION['email_verification']['verified'] = true;
            $message = 'Email verified. You can now complete registration.';
        }
    } elseif ($action === 'register') {
        $rawUsername = trim($_POST['username'] ?? '');
        $rawPassword = trim((string) ($_POST['password'] ?? ''));
        $rawEmail = trim($_POST['email'] ?? '');

        if (strlen($rawUsername) < 6) {
            $message = 'Username must be at least 6 characters.';
        } elseif (strlen($rawPassword) < 8) {
            $message = 'Password must be at least 8 characters.';
        } elseif (!preg_match('/\d/', $rawPassword) || !preg_match('/[^A-Za-z0-9]/', $rawPassword)) {
            $message = 'Password must include at least one number and one special character (min 8).';
        } elseif (!$_SESSION['email_verification']['verified'] || $_SESSION['email_verification']['email'] !== $rawEmail) {
            $message = 'Please verify your email first.';
        } else {
            // Check if email or username already exists in the database
            $conn = new mysqli('localhost', 'root', '', 'ems');
            if ($conn->connect_error) {
                die("Database connection failed: " . $conn->connect_error);
            }
            $checkStmt = $conn->prepare("SELECT studentacc_id FROM studentaccount WHERE studentacc_email = ? OR studentacc_username = ?");
            $checkStmt->bind_param("ss", $rawEmail, $rawUsername);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows > 0) {
                // Check which field is duplicated
                $checkStmt->close();
                $dupStmt = $conn->prepare("SELECT studentacc_id FROM studentaccount WHERE studentacc_email = ?");
                $dupStmt->bind_param("s", $rawEmail);
                $dupStmt->execute();
                $dupStmt->store_result();
                if ($dupStmt->num_rows > 0) {
                    $message = 'This email is already registered. Please use a different email.';
                } else {
                    $message = 'This username is already taken. Please choose a different username.';
                }
                $dupStmt->close();
                $conn->close();
            } else {
                $checkStmt->close();
                // Call the Python script
                $username = escapeshellarg($rawUsername);
                $password = escapeshellarg($rawPassword);
                $email = escapeshellarg($rawEmail);

                $output = shell_exec("C:\\Users\\raymo\\AppData\\Local\\Programs\\Python\\Python313\\python.exe register.py $username $password $email 2>&1");
                $message = nl2br(htmlspecialchars($output));

                // Extract QR code base64 from output
                $qr_base64 = '';
                if (preg_match('/QR_BASE64:(.+)/', $output, $matches)) {
                    $qr_base64 = trim($matches[1]);
                }

                if (strpos($output, "registered successfully") !== false) {
                    // Hash password before storing in MySQL
                    $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare("INSERT INTO studentaccount (studentacc_username, studentacc_password, studentacc_email, enrollmentstatus) VALUES (?, ?, ?, ?)");
                    $enrollmentStatus = "pending";
                    $stmt->bind_param("ssss", $rawUsername, $hashedPassword, $rawEmail, $enrollmentStatus);

                    if ($stmt->execute()) {
                        $_SESSION['email_verification'] = [
                            'email' => null,
                            'code' => null,
                            'expires_at' => null,
                            'verified' => false
                        ];
                        $_SESSION['pending_registration'] = [
                            'username' => '',
                            'password' => ''
                        ];
                        $stmt->close();
                        $conn->close();

                        // Store QR code in session for login page modal
                        $_SESSION['show_qr_modal'] = true;
                        $_SESSION['qr_base64'] = $qr_base64;
                        $_SESSION['new_username'] = $rawUsername;

                        header('Location: login.php');
                        exit;
                    } else {
                        $message .= "<br>Database error: " . $stmt->error;
                        $stmt->close();
                        $conn->close();
                    }
                } else {
                    $conn->close();
                }
            }
        }
    }
}

// Add this before your HTML output (after PHP logic, before <!DOCTYPE html>)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register' && strpos($message, "registered successfully") !== false) {
    // Connect to MySQL
    $conn = new mysqli('localhost', 'root', '', 'ems');
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Hash password before storing
    $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO studentaccount (studentacc_username, studentacc_password, studentacc_email, enrollmentstatus) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $rawUsername, $hashedPassword, $rawEmail, $enrollmentStatus);

    if ($stmt->execute()) {
        // Registration successful, redirect or show message
        $stmt->close();
        $conn->close();
        header('Location: login.php');
        exit;
    } else {
        $message .= "<br>Database error: " . $stmt->error;
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register</title>
    <link rel="icon" href="adminicons/logomnhs.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            overflow-y: auto;
            font-family: 'Poppins', sans-serif;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        /* ========== GLASSMORPHISM WRAPPER ========== */
        .glass-wrapper {
            position: relative;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 20px;
            backdrop-filter: blur(25px);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
            color: #fff;
        }

        .glass-wrapper .form-box {
            position: relative;
            z-index: 1;
        }

        .glass-wrapper h3 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .glass-wrapper h5 {
            font-size: 1.1rem;
            margin-bottom: 15px;
        }

        /* ========== INPUT STYLING ========== */
        .form-control {
            background-color: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            border-radius: 8px;
            font-size: 16px;
            padding: 12px 15px;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.25);
            color: #fff;
            box-shadow: none;
            border: none;
            outline: none;
        }

        .form-label {
            color: #fff;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #00c6ff);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 16px;
            padding: 12px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0069d9, #00aaff);
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .btn-outline-primary, .btn-outline-success {
            font-size: 16px;
            padding: 10px 15px;
        }

        .form-check-label {
            color: #ccc;
            font-size: 14px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 0.25rem;
        }

        .alert {
            font-size: 14px;
            padding: 12px 15px;
            border-radius: 8px;
        }

        .input-group {
            margin-bottom: 1rem;
        }

        hr {
            border-color: rgba(255, 255, 255, 0.2);
            margin: 1.5rem 0;
        }

        a {
            color: #fff;
            text-decoration: underline;
            font-size: 14px;
        }

        a:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Consistent container size across all devices */
        .container-sm {
            max-width: 450px !important;
            width: 100%;
            padding-left: 15px;
            padding-right: 15px;
        }

        @media (max-width: 576px) {
            .container-sm {
                max-width: 450px !important;
                padding-left: 15px;
                padding-right: 15px;
            }
        }
    </style>
    <script>
        // Keep hidden fields in sync for the verification forms
        function syncHidden(formId, userIdSrc, passIdSrc, userIdDst, passIdDst) {
            var f = document.getElementById(formId);
            if (!f) return;
            f.addEventListener('submit', function () {
                var u = document.getElementById(userIdSrc);
                var p = document.getElementById(passIdSrc);
                var hu = document.getElementById(userIdDst);
                var hp = document.getElementById(passIdDst);
                if (u && hu) hu.value = u.value;
                if (p && hp) hp.value = p.value;
            });
        }
        window.addEventListener('DOMContentLoaded', function () {
            syncHidden('send_code_form', 'reg_username', 'reg_password', 'send_code_username', 'send_code_password');
            syncHidden('verify_code_form', 'reg_username', 'reg_password', 'verify_code_username', 'verify_code_password');
        });
    </script>
    <?php include 'navbar.php'; ?>
</head>

<body>

<video autoplay loop muted style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
    <source src="emsbg/bidyowbg.mp4" type="video/mp4">
</video>

<div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="container-sm">
        <div class="glass-wrapper p-4 p-md-5">
            <div class="form-box">
        <h3>Register an Account</h3>
        <?php if ($message): ?>
            <div class="alert alert-info" role="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post" class="mb-3">
            <input type="hidden" name="action" value="register">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" id="reg_username" name="username" class="form-control" minlength="6"
                    value="<?php echo htmlspecialchars($_SESSION['pending_registration']['username'] ?? ''); ?>"
                    required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="reg_password" name="password" class="form-control"
                    minlength="8" pattern="(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}"
                    title="At least 8 characters, including a number and a special character"
                    value="<?php echo htmlspecialchars($_SESSION['pending_registration']['password'] ?? ''); ?>"
                    required>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="toggle_reg_password"
                        onclick="(function(cb){var i=document.getElementById('reg_password'); if(i){ i.type = cb.checked ? 'text' : 'password'; }})(this)">
                    <label class="form-check-label" for="toggle_reg_password">Show password</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" id="reg_email" class="form-control"
                    value="<?php echo htmlspecialchars($_SESSION['email_verification']['email'] ?? ''); ?>"
                    required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <hr class="my-4">
        <h5 class="text-center mb-3">Email Verification</h5>
        <form method="post" id="send_code_form" class="mb-2">
            <input type="hidden" name="action" value="send_code">
            <input type="hidden" name="username" id="send_code_username"
                value="<?php echo htmlspecialchars($_SESSION['pending_registration']['username'] ?? ''); ?>">
            <input type="hidden" name="password" id="send_code_password"
                value="<?php echo htmlspecialchars($_SESSION['pending_registration']['password'] ?? ''); ?>">
            <div class="input-group">
                <input type="email" name="email" id="reg_email2" class="form-control"
                    placeholder="Email"
                    value="<?php echo htmlspecialchars($_SESSION['email_verification']['email'] ?? ''); ?>"
                    required>
                <button type="submit" class="btn btn-outline-primary">Send Code</button>
            </div>
        </form>

        <form method="post" id="verify_code_form">
            <input type="hidden" name="action" value="verify_code">
            <input type="hidden" name="username" id="verify_code_username"
                value="<?php echo htmlspecialchars($_SESSION['pending_registration']['username'] ?? ''); ?>">
            <input type="hidden" name="password" id="verify_code_password"
                value="<?php echo htmlspecialchars($_SESSION['pending_registration']['password'] ?? ''); ?>">
            <div class="input-group">
                <input type="text" name="verify_code" id="reg_verify" class="form-control"
                    placeholder="Enter verification code" required>
                <button type="submit" class="btn btn-outline-success">Verify</button>
            </div>
        </form>

        <p class="text-center mt-3 mb-0">Already have an Account?<a href="login.php" class="ms-1">Sign
                in</a></p>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
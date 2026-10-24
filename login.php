<?php
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawUsername = trim($_POST['username'] ?? '');
    $rawPassword = (string)($_POST['password'] ?? '');
    $rawOtp = (string)($_POST['otp'] ?? '');

    if (strlen($rawUsername) < 6) {
        $message = 'Username must be at least 6 characters.';
    } elseif (strlen($rawPassword) < 8) {
        $message = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $rawPassword)) {
        $message = 'Password must include letters, numbers, and special characters (min 8).';
    } else {
        $username = escapeshellarg($rawUsername);
        $password = escapeshellarg($rawPassword);
        $otp = escapeshellarg($rawOtp);

        // Ensure we run Python from the project directory with an absolute Python path
        $scriptDir = __DIR__;
        $pythonPath = 'C:\\Users\\raymo\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';
        $cmd = "cd /d \"$scriptDir\" && $pythonPath login.py $username $password $otp 2>&1";
        $output = shell_exec($cmd);
        $output = trim($output);

        if ($output === "success") {
            // Include database connection
            require_once 'db_connection.php';

            // Prepare and execute the query using PDO
            $stmt = $pdo->prepare("SELECT studentacc_id FROM studentaccount WHERE studentacc_username = ?");
            $stmt->bindParam(1, $_POST['username']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['studentacc_id'] = $user['studentacc_id'];

                header('Location: dashboard.php');
                exit;
            } else {
                $message = "User not found in database";
            }

            $stmt = null;
        } else {
            $message = $output;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="icon" href="adminicons/logomnhs.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
</head>
<body>
<?php include 'navbar.php'; ?>

<video autoplay loop muted style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
    <source src="emsbg/bidyowbg.mp4" type="video/mp4">
</video>

<div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="container-sm">
        <div class="glass-wrapper p-4 p-md-5">
            <div class="form-box">
        <h3>Welcome Student!</h3>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['show_qr_modal']) && $_SESSION['show_qr_modal']): ?>
            <div class="alert alert-success">
                <strong>Registration successful!</strong> Please scan the QR code below with your Google Authenticator app to set up 2FA.
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" minlength="6" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="login_password" name="password" class="form-control"
       minlength="8"
       pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}"
       title="At least 8 characters, including letters, numbers, and a special character"
       required>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="toggle_login_password" onclick="(function(cb){var i=document.getElementById('login_password'); if(i){ i.type = cb.checked ? 'text' : 'password'; }})(this)">
                    <label class="form-check-label" for="toggle_login_password">Show password</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">OTP Code</label>
                <input type="text" id="otp" name="otp" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="mt-3 text-center">
            Not registered yet?&nbsp;<a href="register.php">Register here</a>
        </p>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<?php if (isset($_SESSION['show_qr_modal']) && $_SESSION['show_qr_modal'] && isset($_SESSION['qr_base64'])): ?>
<div class="modal fade show" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="false" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="margin: 15px;">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel" style="font-size: 1.1rem;">Setup 2FA - <?php echo htmlspecialchars($_SESSION['new_username'] ?? ''); ?></h5>
                <button type="button" class="btn-close" onclick="closeQRModal()" style="min-width: 44px; min-height: 44px;"></button>
            </div>
            <div class="modal-body text-center">
                <p style="font-size: 14px; margin-bottom: 15px;">Scan this QR code with your Google Authenticator app:</p>
                <img src="data:image/png;base64,<?php echo $_SESSION['qr_base64']; ?>" alt="QR Code" class="img-fluid" style="max-width: 100%; max-height: 250px; width: auto; height: auto;">
                <p class="mt-3 text-muted small" style="font-size: 12px; padding: 0 10px;">Keep this QR code secure. You can now use your username and the OTP code from your authenticator app to login.</p>
            </div>
            <div class="modal-footer" style="padding: 10px 15px;">
                <button type="button" class="btn btn-primary w-100" onclick="closeQRModal()" style="min-height: 44px; font-size: 16px;">I have scanned the QR code</button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function closeQRModal() {
    document.getElementById('qrModal').style.display = 'none';
    document.querySelector('.modal-backdrop').style.display = 'none';
    // Clear session variables via AJAX
    fetch('clear_qr_session.php', { method: 'POST' });
}
</script>
</body>
</html>

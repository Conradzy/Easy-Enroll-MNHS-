<?php
session_start();
$message = '';

require_once 'db_connection.php'; // provides $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $message = 'Please enter both username and password.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT admin_id, admin_username, admin_password FROM admin WHERE admin_username = ? LIMIT 1');
            $stmt->bindParam(1, $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $admin_id = $user['admin_id'];
                $admin_username = $user['admin_username'];
                $stored = $user['admin_password'];

                $valid = false;
                if (password_verify($password, $stored)) {
                    $valid = true; // hashed password match
                } elseif (hash_equals((string)$stored, (string)$password)) {
                    $valid = true; // plain text match (fallback)
                }

                if ($valid) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin_id;
                    $_SESSION['admin_username'] = $admin_username;
                    header('Location: admindashboard.php');
                    exit;
                }
            }

            $message = 'Invalid username or password.';
            $stmt = null;
        } catch (Exception $e) {
            $message = 'Login failed. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Login</title>
    <link rel="icon" href="adminicons/logomnhs.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

<video autoplay loop muted>
    <source src="emsbg/bidyowbg.mp4" type="video/mp4">
</video>

<div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="container-sm">
        <div class="glass-wrapper p-4 p-md-5">
            <div class="form-box">
        <h3>Welcome Admin!</h3>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="login_password" name="password" class="form-control" required>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="toggle_login_password" onclick="togglePassword()">
                    <label class="form-check-label" for="toggle_login_password">Show password</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('login_password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

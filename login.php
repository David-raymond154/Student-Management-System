<?php
session_start();

require_once 'config/db.php';

if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim(htmlspecialchars($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $safeUser = mysqli_real_escape_string($conn, $username);
        $result   = mysqli_query($conn, "SELECT * FROM users WHERE username = '$safeUser'");

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {

                $_SESSION['username'] = $user['username'];

                if (isset($_POST['remember_me']) && $_POST['remember_me'] === 'on') {
                    setcookie('remember_username', $user['username'], time() + 604800, '/');
                }

                header('Location: dashboard.php');
                exit;

            } else {
                $error = 'Invalid credentials. Please try again.';
            }
        } else {
            $error = 'Invalid credentials. Please try again.';
        }
    }
}

$cookieUsername = isset($_COOKIE['remember_username'])
    ? htmlspecialchars($_COOKIE['remember_username'])
    : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .card { max-width: 420px; margin: 100px auto; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.1); }
        .card-header { background: #0d6efd; color: #fff; border-radius: 12px 12px 0 0 !important; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header text-center py-3">
            <h4 class="mb-0">Student Management System</h4>
            <small>Please log in to continue</small>
        </div>
        <div class="card-body p-4">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username"
                           class="form-control"
                           value="<?= $cookieUsername ?>"
                           placeholder="Enter username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control"
                           placeholder="Enter password" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember_me" id="remember_me"
                           class="form-check-input"
                           <?= $cookieUsername ? 'checked' : '' ?>>
                    <label for="remember_me" class="form-check-label">Remember Me (7 days)</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>
</body>


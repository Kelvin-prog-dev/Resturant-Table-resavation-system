<?php
// admin/login.php
session_start();

// Already logged in — go straight to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if (isset($_POST['login'])) {
    require_once '../config.php';

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Zest Restaurant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="login-wrap">

    <!-- Branding hero -->
    <div class="login-hero">
        <div class="login-logo"><span>Z</span>est</div>
        <p>Admin Portal</p>
    </div>

    <!-- Login card -->
    <div class="login-card">

        <?php if ($error): ?>
        <div class="flash flash-error">
            <i class="ti ti-alert-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="login-field">
                <label for="username"><i class="ti ti-user" style="font-size:11px"></i> Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Enter your username" required autocomplete="username">
            </div>
            <div class="login-field">
                <label for="password"><i class="ti ti-lock" style="font-size:11px"></i> Password</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required autocomplete="current-password">
            </div>
            <button type="submit" name="login" class="login-submit">
                Sign in &rarr;
            </button>
        </form>
    </div>

    <div class="login-back">
        <a href="../index.php"><i class="ti ti-arrow-left" style="font-size:11px"></i> Back to Zest</a>
    </div>

</div>
</body>
</html>
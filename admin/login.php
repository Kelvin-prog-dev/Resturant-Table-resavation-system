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

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $conn = getDBConnection();

    // Query the database for the user
    $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Check password (supports both plain-text and hashed passwords for flexibility)
        $isValid = false;
        if (password_verify($password, $user['password'])) {
            $isValid = true;
        } elseif ($password === $user['password']) {
            $isValid = true;
        }

        if ($isValid) {
            // Check if the user has an admin role
            if (strtolower($user['role']) === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['user_id'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Access denied. Admin privileges required.";
            }
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    } else {
        $error = "Invalid email or password. Please try again.";
    }

    $stmt->close();
    $conn->close();
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
                <label for="email"><i class="ti ti-mail" style="font-size:11px"></i> Email</label>
                <input type="email" id="email" name="email"
                       placeholder="Enter your email" required autocomplete="email">
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
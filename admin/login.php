<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo "Zest Restaurant"; ?></title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .login-form {
            max-width: 400px;
            margin: 50px auto;
        }
        .login-form h2 {
            text-align: center;
            color: #333;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>Admin Login</h2>

            <?php
            session_start();

            if (isset($_POST['login'])) {
                require_once '../config.php';

                $username = trim($_POST['username'] ?? '');
                $password = trim($_POST['password'] ?? '');

                if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
                    $_SESSION['admin_logged_in'] = true;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo '<div class="error-message">Invalid username or password</div>';
                }
            }
            ?>

            <form action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
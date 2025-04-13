<?php
session_start();
require 'inc/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        // Check if profile is set in user_profile table
        $profile_stmt = $conn->prepare("SELECT is_profile_set FROM user_profile WHERE user_id = ?");
        $profile_stmt->execute([$user['id']]);
        $profile = $profile_stmt->fetch();

        if (!$profile || $profile['is_profile_set'] == 0) {
            echo "<script>
                if (confirm('Set up your profile now?')) {
                    window.location.href = 'profile_form.php';
                } else {
                    window.location.href = 'index.php';
                }
            </script>";
        } else {
            echo "<script>window.location.href = 'index.php';</script>";
        }
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Forum</title>
    <link rel="stylesheet" href="css/auth.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p><a href="register.php">Don't have an account? Register</a></p>
    </div>
</body>
</html>

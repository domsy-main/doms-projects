<?php
require 'inc/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insert user into DB
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $password])) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Registration failed!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Forum</title>
    <link rel="stylesheet" href="css/auth.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
    <div class="container">
        <h2>Create an Account</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Register">
        </form>
        <p><a href="login.php">Already have an account? Login here</a></p>
    </div>
</body>
</html>


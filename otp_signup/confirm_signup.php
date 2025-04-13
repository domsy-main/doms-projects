<?php
session_start();
require 'includes/db_connection.php';

$user_otp = $_POST['otp'];
$stored = $_SESSION['pending_user'];

if ($user_otp == $stored['otp']) {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$stored['name'], $stored['email'], $stored['password']]);

    unset($_SESSION['pending_user']);
    header("Location: success.php");
} else {
    echo "Invalid OTP. <a href='verify_otp.php'>Try again</a>";
}
?>

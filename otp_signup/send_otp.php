<?php
session_start();
require 'vendor/autoload.php';
include 'includes/db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// User data
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$otp = rand(100000, 999999);

// Store temporarily in session
$_SESSION['pending_user'] = [
    'name' => $name,
    'email' => $email,
    'password' => $password,
    'otp' => $otp
];

// Email content
$subject = "Your OTP Code";
$body = "Hello $name,<br><br>Your OTP code is: <b>$otp</b><br><br>Thank you.";

// PHPMailer setup
$mail = new PHPMailer(true);

try {
    // Gmail SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'schooldoms21@gmail.com';  
    $mail->Password = 'endf qroy riix zwgi';      
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('schooldoms21@gmail.com', 'smodoms'); // 🔐 Same Gmail address or custom name
    $mail->addAddress($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    header("Location: verify_otp.php");
    exit;
} catch (Exception $e) {
    echo "Failed to send OTP. Mailer Error: {$mail->ErrorInfo}";
}
?>

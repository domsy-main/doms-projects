<?php
session_start();
require '../inc/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE user_profile SET is_anonymous = ? WHERE user_id = ?");
    $stmt->execute([$is_anonymous, $user_id]);

    header("Location: ../profile.php");
    exit();
}

<?php
session_start();
require '../inc/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'];

    $username = $_POST['username'];
    $email    = $_POST['email'];
    $name     = $_POST['name'];
    $age      = $_POST['age'];
    $gender   = $_POST['gender'];
    $address  = $_POST['address'];
    $motto    = $_POST['motto'];
    $is_anon  = isset($_POST['is_anonymous']) ? 1 : 0;

    // Update users table
    $update_user = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $update_user->execute([$username, $email, $user_id]);

    // Check if profile exists
    $check_profile = $conn->prepare("SELECT id FROM user_profile WHERE user_id = ?");
    $check_profile->execute([$user_id]);
    $existing = $check_profile->fetch();

    // Update or Insert profile
    if ($existing) {
        $update_profile = $conn->prepare("UPDATE user_profile SET name = ?, age = ?, gender = ?, address = ?, motto = ?, is_anonymous = ? WHERE user_id = ?");
        $update_profile->execute([$name, $age, $gender, $address, $motto, $is_anon, $user_id]);
    } else {
        $insert_profile = $conn->prepare("INSERT INTO user_profile (user_id, name, age, gender, address, motto, is_anonymous) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert_profile->execute([$user_id, $name, $age, $gender, $address, $motto, $is_anon]);
    }

    // Redirect
    header("Location: ../profile.php");
    exit;
}
?>

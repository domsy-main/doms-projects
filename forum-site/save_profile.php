<?php
require 'inc/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$name = $_POST['name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$motto = $_POST['motto'];
$is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

// Check if profile exists
$stmt = $conn->prepare("SELECT id FROM user_profile WHERE user_id = ?");
$stmt->execute([$user_id]);
$existing = $stmt->fetch();

if ($existing) {
    // Update profile
    $update = $conn->prepare("UPDATE user_profile SET name = ?, age = ?, gender = ?, address = ?, motto = ?, is_anonymous = ?, is_profile_set = 1 WHERE user_id = ?");
    $update->execute([$name, $age, $gender, $address, $motto, $is_anonymous, $user_id]);
} else {
    // Insert profile
    $insert = $conn->prepare("INSERT INTO user_profile (user_id, name, age, gender, address, motto, is_anonymous, is_profile_set) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $insert->execute([$user_id, $name, $age, $gender, $address, $motto, $is_anonymous]);
}

// Redirect to profile or homepage
header("Location: profile.php");
exit;

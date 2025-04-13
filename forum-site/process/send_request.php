<?php
require '../inc/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$sender_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'])) {
    $receiver_id = (int) $_POST['receiver_id'];

    // Prevent sending request to self
    if ($receiver_id === $sender_id) {
        header("Location: index.php?error=cannot_add_self");
        exit;
    }

    // Check if a request already exists
    $check = $conn->prepare("SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ?");
    $check->execute([$sender_id, $receiver_id]);

    if ($check->rowCount() === 0) {
        // Insert friend request
        $stmt = $conn->prepare("INSERT INTO friend_requests (sender_id, receiver_id) VALUES (?, ?)");
        $stmt->execute([$sender_id, $receiver_id]);

        header("Location: /forum-site/friends/index.php?success=request_sent");
    } else {
        header("Location: /forum-site/friends/index.php?info=already_requested");
    }
    exit;
}

header("Location: index.php");

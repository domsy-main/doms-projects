<?php
require '../inc/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$request_id = $_POST['request_id'];
$action = $_POST['action']; // 'accept' or 'decline'

// Fetch the request to validate
$stmt = $conn->prepare("SELECT * FROM friend_requests WHERE id = ? AND receiver_id = ?");
$stmt->execute([$request_id, $_SESSION['user']['id']]);
$request = $stmt->fetch();

if (!$request) {
    header("Location: ../friends/index.php?error=invalid");
    exit;
}

if ($action === 'accept') {
    // 1. Update request status
    $update = $conn->prepare("UPDATE friend_requests SET status = 'accepted' WHERE id = ?");
    $update->execute([$request_id]);

    // 2. Insert into 'friends' table
    $insert = $conn->prepare("INSERT INTO friends (user1_id, user2_id) VALUES (?, ?)");
    $insert->execute([$request['sender_id'], $request['receiver_id']]);

    header("Location: ../friends/index.php?success=accepted");
} elseif ($action === 'decline') {
    // Just delete the request (optional: update status to 'declined')
    $delete = $conn->prepare("DELETE FROM friend_requests WHERE id = ?");
    $delete->execute([$request_id]);

    header("Location: ../friends/index.php?info=declined");
} else {
    header("Location: ../friends/index.php?error=unknown");
}

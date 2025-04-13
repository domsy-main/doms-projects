<?php
require 'inc/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $thread_id = $_POST['thread_id'];
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO posts (thread_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$thread_id, $user_id, $content]);

    $thread_stmt = $conn->prepare("
        SELECT threads.title, threads.user_id, users.username 
        FROM threads 
        JOIN users ON threads.user_id = users.id 
        WHERE threads.id = ?
    ");
    $thread_stmt->execute([$thread_id]);
    $thread = $thread_stmt->fetch();
    $thread_title = $thread['title'];
    $thread_creator_id = $thread['user_id'];
    $thread_creator_name = $thread['username'];


    $users_stmt = $conn->prepare("SELECT DISTINCT user_id FROM posts WHERE thread_id = ? AND user_id != ?");
    $users_stmt->execute([$thread_id, $user_id]);
    $involved_users = $users_stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($thread_creator_id != $user_id) {
        $involved_users[] = $thread_creator_id;
    }

    $involved_users = array_unique($involved_users);

    $message = "Someone replied to $thread_creator_name's thread: \"$thread_title\"";
    $link = "thread.php?id=$thread_id";
    $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)");

    foreach ($involved_users as $recipient_id) {
        $notif_stmt->execute([$recipient_id, $message, $link]);
    }

    header("Location: thread.php?id=" . $thread_id);
    exit();
}

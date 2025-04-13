<?php
require 'inc/db.php';
include 'inc/header.php';

$thread_id = $_GET['id'];
$stmt = $conn->prepare("SELECT threads.*, users.username FROM threads JOIN users ON threads.user_id = users.id WHERE threads.id = ?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

$post_stmt = $conn->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE thread_id = ? ORDER BY created_at");
$post_stmt->execute([$thread_id]);
$posts = $post_stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title><?= htmlspecialchars($thread['title']) ?></title>
<link rel="stylesheet" href="css/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h2><?= htmlspecialchars($thread['title']) ?></h2>
<p>Started by <?= htmlspecialchars($thread['username']) ?></p>
<hr>

<?php foreach ($posts as $post): ?>
    <p><b><?= htmlspecialchars($post['username']) ?>:</b> <?= nl2br(htmlspecialchars($post['content'])) ?></p>
<?php endforeach; ?>

<?php if (isset($_SESSION['user'])): ?>
    <form method="POST" action="reply.php">
        <textarea name="content" placeholder="Write a reply..." required></textarea><br>
        <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
        <button type="submit">Post Reply</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Login</a> to reply.</p>
<?php endif; ?>
</body>
</html>

<?php
include 'inc/auth.php';
require 'inc/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO threads (user_id, title) VALUES (?, ?)");
    $stmt->execute([$user_id, $title]);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Create Thread</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css"></head>
<body>
<h2>Start a New Thread</h2>
<form method="POST">
    <input type="text" name="title" placeholder="Thread Title" required><br><br>
    <button type="submit">Create Thread</button>
</form>
</body>
</html>

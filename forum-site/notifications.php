<?php
require 'inc/db.php';
include 'inc/header.php';
$user_id = $_SESSION['user']['id']; // assuming you're storing user info in session

$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY timestamp DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications - Forum</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
        }

        .notifications-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .notification {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }

        .notification:last-child {
            border-bottom: none;
        }

        .timestamp {
            color: #888;
            font-size: 0.9rem;
        }

        .notification a {
            text-decoration: none;
            color: #333;
        }

        .notification a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="notifications-container">
        <h2>Notifications</h2>

        <?php foreach ($notifications as $notif): ?>
            <div class="notification">
                <p><?= htmlspecialchars($notif['message']) ?> 
                    <?php if (!empty($notif['link'])): ?>
                        <a href="<?= htmlspecialchars($notif['link']) ?>">[View]</a>
                    <?php endif; ?>
                </p>
                <p class="timestamp"><?= date('F j, Y, g:i a', strtotime($notif['timestamp'])) ?></p>
            </div>
        <?php endforeach; ?>

    </div>
</body>
</html>

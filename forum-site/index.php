<?php
require 'inc/db.php';
include 'inc/header.php';
$stmt = $conn->query("SELECT threads.*, users.username FROM threads JOIN users ON threads.user_id = users.id ORDER BY created_at DESC");
$threads = $stmt->fetchAll();

$trending_stmt = $conn->query("
    SELECT t.*, u.username, COUNT(p.id) AS comment_count
    FROM threads t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN posts p ON t.id = p.thread_id
    GROUP BY t.id
    ORDER BY comment_count DESC
    LIMIT 3
");
$trending_stmt->execute();
$trending_threads = $trending_stmt->fetchAll();

// Fetch threads posted by friends
$friend_stmt = $conn->prepare("
    SELECT t.*, u.username
    FROM threads t
    JOIN users u ON t.user_id = u.id
    WHERE t.user_id IN (
        SELECT CASE
            WHEN f.user1_id = :id THEN f.user2_id
            ELSE f.user1_id
        END
        FROM friends f
        WHERE f.user1_id = :id OR f.user2_id = :id
    )
    ORDER BY t.created_at DESC
");
$friend_stmt->execute(['id' => $_SESSION['user']['id']]);
$friend_threads = $friend_stmt->fetchAll();

// Fetch all threads except those already in trending
$exclude_ids = implode(',', array_map(fn($t) => $t['id'], $trending_threads));
$all_stmt = $conn->query("
    SELECT t.*, u.username
    FROM threads t
    JOIN users u ON t.user_id = u.id
    " . ($exclude_ids ? "WHERE t.id NOT IN ($exclude_ids)" : "") . "
    ORDER BY t.created_at DESC
");
$all_threads = $all_stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Forum</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div id="splash">
    <div class="loader-wrapper">
        <div class="loader"></div>
        <img src="img/doms.png" alt="Logo" class="loader-img">
        <h1>Welcome to Simple Forum</h1>
    </div>
</div>

<div id="main-content">
    <div class="filter-bar">
        <button onclick="filterThreads('trending')">Trending</button>
        <button onclick="filterThreads('friends')">Friends</button>
        <button onclick="filterThreads('latest')">Latest</button>
        <button onclick="filterThreads('all')">All</button>
    </div>

    <div id="trending" class="thread-section">
        <h3>🔥 Trending Threads</h3>
        <ul>
            <?php foreach ($trending_threads as $thread): ?>
                <li>
                    <a href="thread.php?id=<?= $thread['id'] ?>">
                        <?= htmlspecialchars($thread['title']) ?>
                    </a>
                    <span>by <?= htmlspecialchars($thread['username']) ?> (<?= $thread['comment_count'] ?> comments)</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="friends" class="thread-section" style="display:none;">
        <h3>👥 Threads by Friends</h3>
        <ul>
            <?php foreach ($friend_threads as $thread): ?>
                <li>
                    <a href="thread.php?id=<?= $thread['id'] ?>">
                        <?= htmlspecialchars($thread['title']) ?>
                    </a>
                    <span>by <?= htmlspecialchars($thread['username']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="latest" class="thread-section" style="display:none;">
        <h3>🕓 Latest Threads</h3>
        <ul>
            <?php foreach ($threads as $thread): ?>
                <li>
                    <a href="thread.php?id=<?= $thread['id'] ?>">
                        <?= htmlspecialchars($thread['title']) ?>
                    </a>
                    <span>by <?= htmlspecialchars($thread['username']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="all" class="thread-section" style="display:none;">
        <h3>📚 All Threads</h3>
        <ul>
            <?php foreach ($all_threads as $thread): ?>
                <li>
                    <a href="thread.php?id=<?= $thread['id'] ?>">
                        <?= htmlspecialchars($thread['title']) ?>
                    </a>
                    <span>by <?= htmlspecialchars($thread['username']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    window.onload = function () {
        setTimeout(() => {
            document.getElementById('splash').style.opacity = '0';
            setTimeout(() => {
                document.getElementById('splash').style.display = 'none';
                document.getElementById('main-content').style.display = 'block';
            }, 400);
        }, 500);
    };
    function filterThreads(type) {
        const sections = ['trending', 'friends', 'latest', 'all'];
        sections.forEach(id => {
            document.getElementById(id).style.display = id === type ? 'block' : 'none';
        });
    }
</script>

</body>
</html>


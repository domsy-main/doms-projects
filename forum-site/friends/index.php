<?php
require '../inc/db.php';
include '../inc/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$current_user_id = $_SESSION['user']['id'];

// Fetch all users except the currently logged-in user
$stmt = $conn->prepare("
    SELECT u.id, u.username, p.profile_image 
    FROM users u 
    LEFT JOIN user_profile p ON u.id = p.user_id 
    WHERE u.id != ?
    ORDER BY u.username
");
$stmt->execute([$current_user_id]);
$users = $stmt->fetchAll();

// Fetch friend requests received by the current user
$requests_stmt = $conn->prepare("
    SELECT fr.id AS request_id, u.id AS sender_id, u.username, p.profile_image
    FROM friend_requests fr
    JOIN users u ON fr.sender_id = u.id
    LEFT JOIN user_profile p ON u.id = p.user_id
    WHERE fr.receiver_id = ? AND fr.status = 'pending'
    ORDER BY fr.created_at DESC
");
$requests_stmt->execute([$current_user_id]);
$friend_requests = $requests_stmt->fetchAll();

// Fetch all friends of the current user
$friends_stmt = $conn->prepare("
    SELECT u.id, u.username, p.profile_image
    FROM friends f
    JOIN users u ON (u.id = f.user1_id OR u.id = f.user2_id)
    LEFT JOIN user_profile p ON u.id = p.user_id
    WHERE (f.user1_id = :uid OR f.user2_id = :uid) AND u.id != :uid
");
$friends_stmt->execute(['uid' => $current_user_id]);
$friends = $friends_stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Users - Friends</title>
    <link rel="stylesheet" href="../css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .friends-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .user-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .user-card:last-child {
            border-bottom: none;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #ccc;
            margin-right: 20px;
            cursor: pointer;
        }

        .user-info h3 {
            margin: 0;
            font-size: 18px;
            color: #222;
        }

        .user-info h3 a {
            text-decoration: none;
            color: #222;
        }

        .user-info h3 a:hover {
            text-decoration: underline;
        }

        .add-friend-btn {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .add-friend-btn:hover {
            background-color: #0056b3;
        }

        .friend-requests-container, .my-friends-container {
            margin-bottom: 40px;
        }

        .my-friends-container h2,
        .friend-requests-container h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="friends-container">

    <?php if (isset($_GET['success'])): ?>
        <p style="color: green;">Friend request sent!</p>
    <?php elseif (isset($_GET['info'])): ?>
        <p style="color: orange;">Friend request already sent.</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p style="color: red;">You can't send a request to yourself.</p>
    <?php endif; ?>

    <!-- FRIEND REQUESTS -->
    <?php if (count($friend_requests) > 0): ?>
        <div class="friend-requests-container">
            <h2>Friend Requests</h2>
            <?php foreach ($friend_requests as $request): ?>
                <div class="user-card">
                    <a href="../profile.php?id=<?= $request['sender_id'] ?>">
                        <img src="<?= $request['profile_image'] ? '../uploads/' . $request['profile_image'] : '../img/doms.png' ?>" alt="Avatar" class="user-avatar">
                    </a>
                    <div class="user-info">
                        <h3><a href="../profile.php?id=<?= $request['sender_id'] ?>"><?= htmlspecialchars($request['username']) ?></a></h3>
                        <form method="POST" action="../process/respond_request.php" style="margin-top: 8px;">
                            <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                            <button type="submit" name="action" value="accept" class="add-friend-btn" style="background-color: #28a745;">Accept</button>
                            <button type="submit" name="action" value="decline" class="add-friend-btn" style="background-color: #dc3545;">Decline</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- FRIENDS SECTION -->
    <?php if (count($friends) > 0): ?>
        <div class="my-friends-container">
            <h2>My Friends</h2>
            <?php foreach ($friends as $friend): ?>
                <div class="user-card">
                    <div class="user-info">
                        <a href="../profile.php?id=<?= $friend['id'] ?>">
                            <img src="<?= $friend['profile_image'] ? '../uploads/' . $friend['profile_image'] : '../img/doms.png' ?>" alt="Avatar" class="user-avatar">
                        </a>
                        <h3><a href="../profile.php?id=<?= $friend['id'] ?>"><?= htmlspecialchars($friend['username']) ?></a></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ALL USERS SECTION -->
    <h2>All Users</h2>

    <?php if (count($users) === 0): ?>
        <p>No other users found.</p>
    <?php else: ?>
        <?php
        $friend_ids = array_map(fn($f) => $f['id'], $friends);
        ?>

        <?php foreach ($users as $user): ?>
            <?php if (!in_array($user['id'], $friend_ids)): ?>
                <div class="user-card">
                    <div class="user-info">
                        <a href="../profile.php?id=<?= $user['id'] ?>">
                            <img src="<?= $user['profile_image'] ? '../uploads/' . $user['profile_image'] : '../img/doms.png' ?>" alt="Avatar" class="user-avatar">
                        </a>
                        <h3><a href="../profile.php?id=<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></a></h3>
                    </div>
                    <form method="POST" action="../process/send_request.php">
                        <input type="hidden" name="receiver_id" value="<?= $user['id'] ?>">
                        <button type="submit" class="add-friend-btn">Add Friend</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

</body>
</html>

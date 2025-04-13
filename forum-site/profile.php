<?php
require 'inc/db.php';
include 'inc/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$logged_user = $_SESSION['user'];
$logged_user_id = $logged_user['id'];

// Check if viewing own profile or someone else's
$viewing_user_id = isset($_GET['id']) ? (int) $_GET['id'] : $logged_user_id;
$is_own_profile = $viewing_user_id === $logged_user_id;

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$viewing_user_id]);
$user = $stmt->fetch();

// If user doesn't exist
if (!$user) {
    echo "<p>User not found.</p>";
    exit;
}

// Get user profile
$profile_stmt = $conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
$profile_stmt->execute([$viewing_user_id]);
$profile = $profile_stmt->fetch();

$default_image = 'img/doms.png';
$profile_img_path = isset($profile['profile_image']) && !empty($profile['profile_image'])
    ? 'uploads/' . htmlspecialchars($profile['profile_image'])
    : $default_image;
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $is_own_profile ? 'My Profile' : $user['username'] . "'s Profile" ?></title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .profile-container h2 {
            text-align: center;
        }
        .profile-item { margin-bottom: 15px; }
        .profile-label { font-weight: bold; }
        .switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
        }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #bbb;
            transition: 0.4s;
            border-radius: 24px;
        }
        .slider:before {
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            position: absolute;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: #333; }
        input:checked + .slider:before { transform: translateX(24px); }

        .profile-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
            cursor: pointer;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 999;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.85);
            justify-content: center;
            align-items: center;
        }
        .modal-img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 10px;
            box-shadow: 0 0 20px #000;
        }
        .close-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 32px;
            color: #fff;
            cursor: pointer;
            z-index: 1000;
        }
        .user-threads-container {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}

.user-threads-container h3 {
    text-align: center;
    font-size: 20px;
    margin-bottom: 20px;
    color: #333;
}

.user-thread-list {
    list-style: none;
    padding-left: 0;
}

.user-thread-item {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 10px;
    background-color: #fdfdfd;
    border: 1px solid #e0e0e0;
    transition: background 0.2s;
}

.user-thread-item:hover {
    background-color: #f4f4f4;
}

.user-thread-item a {
    font-size: 16px;
    color: #0066cc;
    font-weight: bold;
    text-decoration: none;
}

.user-thread-item a:hover {
    text-decoration: underline;
}

.user-thread-date {
    font-size: 13px;
    color: #666;
    margin-top: 5px;
}

    </style>
</head>
<body>

<div class="profile-container">
    <div class="image-wrapper" style="text-align: center; margin-bottom: 20px;">
        <img src="<?= $profile_img_path ?>" alt="Profile Image" class="profile-img" onclick="openImageModal()">
    </div>

    <!-- Modal -->
    <div id="imageModal" class="modal-overlay" onclick="closeImageModal()">
        <span class="close-btn" onclick="closeImageModal()">✖</span>
        <img src="<?= $profile_img_path ?>" class="modal-img" alt="Zoomed Image">
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2><?= $is_own_profile ? 'Welcome, ' . htmlspecialchars($user['username']) : htmlspecialchars($user['username']) . "'s Profile" ?></h2>

        <?php if ($is_own_profile && $profile): ?>
            <form method="POST" action="process/toggle_anonymous.php" style="margin: 0;">
                <span class="profile-label">Anonymous:</span>
                <input type="hidden" name="user_id" value="<?= $logged_user_id ?>">
                <label class="switch">
                    <input type="checkbox" name="is_anonymous" onchange="this.form.submit()" <?= $profile['is_anonymous'] ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
            </form>
        <?php endif; ?>
    </div>

    <div class="profile-item">
        <span class="profile-label">Username:</span> <?= htmlspecialchars($user['username']) ?>
    </div>

    <?php if ($is_own_profile): ?>
    <div class="profile-item">
        <span class="profile-label">Email:</span> <?= htmlspecialchars($user['email']) ?>
    </div>
    <?php endif; ?>

    <?php if ($profile): ?>
        <div class="profile-item">
            <span class="profile-label">Full Name:</span> <?= $profile['is_anonymous'] && !$is_own_profile ? 'Hidden' : htmlspecialchars($profile['name']) ?>
        </div>
        <div class="profile-item">
            <span class="profile-label">Age:</span> <?= $profile['is_anonymous'] && !$is_own_profile ? 'Hidden' : htmlspecialchars($profile['age']) ?>
        </div>
        <div class="profile-item">
            <span class="profile-label">Gender:</span> <?= $profile['is_anonymous'] && !$is_own_profile ? 'Hidden' : htmlspecialchars($profile['gender']) ?>
        </div>
        <div class="profile-item">
            <span class="profile-label">Address:</span> <?= $profile['is_anonymous'] && !$is_own_profile ? 'Hidden' : htmlspecialchars($profile['address']) ?>
        </div>
        <div class="profile-item">
            <span class="profile-label">Motto:</span> <?= $profile['is_anonymous'] && !$is_own_profile ? 'Hidden' : htmlspecialchars($profile['motto']) ?>
        </div>
        <div class="profile-item">
            <span class="profile-label">Anonymous:</span> <?= $profile['is_anonymous'] ? 'Yes' : 'No' ?>
        </div>
    <?php endif; ?>

    <?php if ($is_own_profile): ?>
        <div class="profile-item">
            <a href="edit_profile.php">Edit Profile</a>
        </div>
    <?php endif; ?>
</div>
<?php
// Fetch threads created by the user
$thread_stmt = $conn->prepare("SELECT id, title, created_at FROM threads WHERE user_id = ? ORDER BY created_at DESC");
$thread_stmt->execute([$viewing_user_id]);
$user_threads = $thread_stmt->fetchAll();
?>

<div class="user-threads-container">
    <h3>Threads by <?= htmlspecialchars($user['username']) ?></h3>

    <?php if (count($user_threads) > 0): ?>
        <ul class="user-thread-list">
            <?php foreach ($user_threads as $thread): ?>
                <li class="user-thread-item">
                    <a href="thread.php?id=<?= $thread['id'] ?>">
                        <?= htmlspecialchars($thread['title']) ?>
                    </a>
                    <div class="user-thread-date">
                        Created on <?= date("F j, Y", strtotime($thread['created_at'])) ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p style="text-align: center; color: #666;">No threads created yet.</p>
    <?php endif; ?>
</div>

<script>
function openImageModal() {
    document.getElementById('imageModal').style.display = 'flex';
}
function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}
</script>

</body>
</html>

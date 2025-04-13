<?php
require 'inc/db.php';
include 'inc/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch user basic info
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();

// Fetch profile info (if exists)
$profile_stmt = $conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
$profile_stmt->execute([$user_id]);
$profile = $profile_stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .edit-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: #f4f4f4;
            border-radius: 10px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        button {
            margin-top: 20px;
            padding: 12px;
            background: #222;
            color: #fff;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #444;
        }
        .upload-image-link {
    text-align: center;
    margin-bottom: 15px;
}

.upload-image-link a {
    background-color: #000;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.2s ease;
}

.upload-image-link a:hover {
    background-color: #333;
}

    </style>
</head>
<body>
<div class="edit-container">
    <h2>Edit Your Profile</h2>
    <!-- Upload Image Link -->
    <div class="upload-image-link">
        <a href="image_fun/upload_image.php">Upload Profile Image</a>
    </div>

    <form method="POST" action="process/edit_profile_process.php">
        <!-- Account Info -->
        <label for="username">Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label for="email">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <!-- Profile Info -->
        <label for="name">Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required>

        <label for="age">Age</label>
        <input type="number" name="age" min="1" value="<?= htmlspecialchars($profile['age'] ?? '') ?>" required>

        <label for="gender">Gender</label>
        <select name="gender" required>
            <option value="">Select</option>
            <option value="Male" <?= (isset($profile['gender']) && $profile['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= (isset($profile['gender']) && $profile['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= (isset($profile['gender']) && $profile['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
        </select>

        <label for="address">Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>" required>

        <label for="motto">Motto</label>
        <textarea name="motto"><?= htmlspecialchars($profile['motto'] ?? '') ?></textarea>

        <label>
            <input type="checkbox" name="is_anonymous" <?= (isset($profile['is_anonymous']) && $profile['is_anonymous']) ? 'checked' : '' ?>>
            Keep my profile private (anonymous)
        </label>

        <button type="submit">Update Profile</button>
    </form>
</div>
</body>
</html>

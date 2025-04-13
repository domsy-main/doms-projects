<?php
require '../inc/db.php';
include '../inc/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$message = '';

// Fetch profile image if it exists
$stmt = $conn->prepare("SELECT profile_image FROM user_profile WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();
$profile_image = !empty($profile['profile_image']) ? "../uploads/" . $profile['profile_image'] : "../img/doms.png";

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $upload_dir = '../uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file = $_FILES['profile_image'];
    $allowed = ['image/jpeg', 'image/png', 'image/jpg'];

    if (in_array($file['type'], $allowed) && $file['size'] <= 5 * 1024 * 1024) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'user_' . $user_id . '.' . $ext;
        $destination = $upload_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $stmt = $conn->prepare("UPDATE user_profile SET profile_image = ? WHERE user_id = ?");
            $stmt->execute([$new_filename, $user_id]);

            $message = "✅ Profile image uploaded successfully.";
            $profile_image = "../uploads/" . $new_filename;
        } else {
            $message = "❌ Failed to move uploaded image.";
        }
    } else {
        $message = "⚠️ Only JPG and PNG images under 2MB are allowed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Profile Image</title>
    <link rel="stylesheet" href="../css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .upload-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            background: #f4f4f4;
            border-radius: 10px;
            text-align: center;
        }

        .upload-container img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ccc;
            margin-bottom: 15px;
        }

        .upload-container input[type="file"] {
            margin: 20px 0;
        }

        .upload-container button {
            padding: 10px 20px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .upload-container button:hover {
            background: #333;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 6px;
            background-color: #e1ffe1;
            color: #155724;
            font-weight: 500;
        }

        .back-link {
            margin-top: 20px;
            display: block;
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="upload-container">
    <h2>Upload Profile Image</h2>

    <!-- Display Current Image -->
    <img src="<?= htmlspecialchars($profile_image) ?>" alt="Current Profile Image">

    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="profile_image" accept=".jpg, .jpeg, .png" required><br>
        <button type="submit">Upload</button>
    </form>

    <a href="../profile.php" class="back-link">← Back to Profile</a>
</div>
</body>
</html>

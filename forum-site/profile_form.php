<?php
require 'inc/db.php';
include 'inc/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Check if user already has a profile
$stmt = $conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
$stmt->execute([$user_id]);
$existing = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            margin-top: 15px;
        }
        input, select, textarea {
            padding: 8px;
            font-size: 16px;
            margin-top: 5px;
        }
        button {
            margin-top: 20px;
            padding: 10px;
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #444;
        }
    </style>
</head>
<body>

<h2><?= $existing ? 'Update' : 'Create' ?> Your Profile</h2>

<form action="save_profile.php" method="post">
    <label for="name">Full Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($existing['name'] ?? '') ?>" required>

    <label for="age">Age</label>
    <input type="number" name="age" min="1" value="<?= htmlspecialchars($existing['age'] ?? '') ?>" required>

    <label for="gender">Gender</label>
    <select name="gender" required>
        <option value="">Select...</option>
        <option value="Male" <?= (isset($existing['gender']) && $existing['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= (isset($existing['gender']) && $existing['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
        <option value="Other" <?= (isset($existing['gender']) && $existing['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
    </select>

    <label for="address">Address</label>
    <input type="text" name="address" value="<?= htmlspecialchars($existing['address'] ?? '') ?>" required>

    <label for="motto">Motto</label>
    <textarea name="motto" rows="2"><?= htmlspecialchars($existing['motto'] ?? '') ?></textarea>

    <label>
        <input type="checkbox" name="is_anonymous" <?= (isset($existing['is_anonymous']) && $existing['is_anonymous']) ? 'checked' : '' ?>>
        Keep my profile private (anonymous)
    </label>

    <button type="submit">Save Profile</button>
</form>

</body>
</html>

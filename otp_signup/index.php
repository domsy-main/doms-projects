<?php
$messages = [
    "Secure your access with a one-time verification.",
    "Your journey starts with a single OTP.",
    "Stay protected with email confirmation.",
    "Creating trust through verified access.",
    "DOMS ensures your signups are secure."
];
$message = $messages[array_rand($messages)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOMS - OTP Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #222;
        }
        .container {
            background-color: #fff;
            color: #000;
            padding: 2rem 3rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }
        .container img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1rem;
        }
        .container h1 {
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
            color: #111;
        }
        .container p {
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            color: #555;
        }
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border: 1px solid #333;
            background: none;
            color: #000;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="doms.png" alt="DOMS Logo">
        <h1>Welcome to DOMS</h1>
        <p><?php echo $message; ?></p>
        <a href="signup.php" class="btn">Go to Sign Up</a>
    </div>
</body>
</html>

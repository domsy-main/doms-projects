<?php 
session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Forum Site</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        header {
            padding: 1rem;
            background: #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 120px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: #333;
            padding: 10px 12px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f0f0f0;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
                position: absolute;
                right: 1rem;
                top: 1.2rem;
            }

            nav {
                display: none;
                flex-direction: column;
                gap: 10px;
                width: 100%;
                background: #eee;
                padding: 1rem;
            }

            nav.show {
                display: flex;
            }

            .dropdown-content {
                position: static;
                box-shadow: none;
                background: #eee;
                width: 100%;
            }

            .dropdown-content a {
                padding: 8px 0;
                width: 100%;
            }
            header h1{
                text-align: center;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="nav-container">
        <h1>Simple Forum</h1>
        <i class="fas fa-ellipsis-v menu-toggle" onclick="toggleMenu()"></i>
        <nav id="mobileMenu">
            <a href="/forum-site/index.php"><i class="fas fa-home"></i> Home</a>
            <a href="/forum-site/notifications.php"><i class="fas fa-bell"></i> Notifications</a>
            <a href="/forum-site/friends/"><i class="fa-solid fa-user-group"></i> Friends</a>
            <div class="dropdown">
                <a href="#"><i class="fas fa-user"></i>
                    <?php if (isset($_SESSION['user'])): ?>
                        <?= htmlspecialchars($_SESSION['user']['username']) ?>
                    <?php else: ?>
                        Profile
                    <?php endif; ?>
                    <i class="fas fa-caret-down"></i>
                </a>
                <div class="dropdown-content">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="/forum-site/profile.php">Profile</a>
                        <a href="/forum-site/logout.php">Logout</a>
                    <?php else: ?>
                        <a href="/forum-site/login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
</header>
<script>
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('show');
    }
</script>

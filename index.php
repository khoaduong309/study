<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - StudyHub</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 20px;
        }

        header h1 {
            margin: 0;
        }

        nav ul {
            padding: 0;
            list-style: none;
            text-align: center;
            margin: 0;
        }

        nav ul li {
            display: inline;
            margin-right: 20px;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            text-align: left;
        }

        .text-content {
            flex: 1;
            opacity: 0;
            animation: fadeIn 1s forwards;
            animation-delay: 0.5s;
        }

        .image-content {
            flex: 1;
            text-align: right;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to StudyHub</h1>
    <nav>
        <ul>
            <li><a href="index.php"class="active">Home</a></li>
            <li><a href="lessons.php">Lessons</a></li>
            <li><a href="exercises.php">Exercises</a></li>
            <li><a href="codepad.php">CodePad</a></li>
            <li><a href="contact.php">Contact</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <div class="text-content">
        <h2>Learn, Practice, and Code with StudyHub</h2>
        <p>This is a platform for Python learners to improve your skills through lessons and exercises.</p>
    </div>
    <div class="image-content">
        <img src="images/pasted-image-0.webp" alt="StudyHub Image">
    </div>
</main>

<script src="js/scripts.js"></script> 
</body>
</html>

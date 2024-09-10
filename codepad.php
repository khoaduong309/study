<?php
session_start();
require 'db/db.php'; // Ensure this path is correct

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle saving the code as a document
if (isset($_POST['save_document'])) {
    $username = $_SESSION['username'];
    $code_name = $_POST['code_name']; // Get the code name from the form
    $code_content = $_POST['code_content'];

    // Insert the code into the database
    $stmt = $conn->prepare("INSERT INTO saved_codes (username, code_name, code_content) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $code_name, $code_content);
    $stmt->execute();
    $stmt->close();

    // Notify the user that the code has been saved
    echo "<script>alert('Code saved successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodePad - StudyHub</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        iframe {
            border: none;
            width: 100%;
            height: 400px; /* Adjust iframe height */
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <h1>StudyHub</h1>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="lessons.php">Lessons</a></li>
            <li><a href="exercises.php">Exercises</a></li>
            <li><a href="codepad.php">CodePad</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li>
                <?php if (isset($_SESSION['username'])): ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php">Logout</a>
                    <a href="profile.php">Profile</a> <!-- Link đến Profile -->
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
</header>

<h1>Try it</h1>
<iframe src="https://trinket.io/embed/python3/a5bd54189b" width="100%" height="356" frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>

<h2>Your Code</h2>
<form method="POST">
    <input type="text" name="code_name" required placeholder="Code Name"> <!-- Field for code name -->
    <textarea name="code_content" required placeholder="Write your code here..."></textarea><br>
    <button type="submit" name="save_document">Save Code</button>
</form>

<script src="js/scripts.js"></script> 

</body>
</html>

<?php $conn->close(); ?>

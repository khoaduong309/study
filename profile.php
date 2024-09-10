<?php
session_start();
require 'db/db.php'; // Ensure this path is correct

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user profile data
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch saved codes for the user
$stmt = $conn->prepare("SELECT * FROM saved_codes WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$saved_codes = $stmt->get_result();

// Handle code deletion
if (isset($_POST['delete_code'])) {
    $code_id = $_POST['code_id'];

    // Delete the code from the database
    $delete_stmt = $conn->prepare("DELETE FROM saved_codes WHERE id = ?");
    $delete_stmt->bind_param("i", $code_id);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Refresh the page to see changes
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - StudyHub</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .code-content {
            display: none; /* Hide code content by default */
        }
    </style>
    <script>
        function toggleCodeContent(codeId) {
            var content = document.getElementById('code-content-' + codeId);
            if (content.style.display === "none") {
                content.style.display = "block"; // Show code content
            } else {
                content.style.display = "none"; // Hide code content
            }
        }
    </script>
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

<h1>Your Profile</h1>
<p>Username: <?php echo htmlspecialchars($user['username']); ?></p>
<p>Role: <?php echo htmlspecialchars($user['role']); ?></p>

<h2>Saved Codes</h2>
<ul>
    <?php while ($code = $saved_codes->fetch_assoc()): ?>
        <li>
            <?php echo htmlspecialchars($code['code_name']); ?>
            <button onclick="toggleCodeContent(<?php echo $code['id']; ?>)">Show/Hide Code</button>
            <div id="code-content-<?php echo $code['id']; ?>" class="code-content">
                <pre><?php echo htmlspecialchars($code['code_content']); ?></pre>
            </div>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="code_id" value="<?php echo $code['id']; ?>">
                <button type="submit" name="delete_code" onclick="return confirm('Are you sure you want to delete this code?');">Delete</button>
            </form>
        </li>
    <?php endwhile; ?>
</ul>

</body>
</html>

<script src="js/scripts.js"></script> 

<?php $conn->close(); ?>

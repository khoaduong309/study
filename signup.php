<?php
session_start();
require 'db/db.php'; // Ensure this path is correct

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'user'; // Default role for new users

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username already exists, show an error message
        $error_message = "Username already taken. Please choose a different one.";
    } else {
        // Check if the user wants to register as an admin
        if (isset($_POST['is_admin']) && $_POST['is_admin'] == 'on') {
            $invite_code = $_POST['invite_code'];
            
            // Validate invite code for admin registration
            if ($invite_code === '1234') { // Replace '1234' with your actual invite code
                $role = 'admin'; // Set role to admin
            } else {
                $error_message = "Invalid invite code for admin.";
            }
        }

        // If no error messages, proceed with registration
        if (!isset($error_message)) {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            $stmt->execute();

            // Redirect to the login page after successful signup
            header("Location: login.php");
            exit();
        }
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - StudyHub</title>
    <link rel="stylesheet" href="./css/style.css">
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
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
</header>

<h1>Create an Account</h1>
<form method="POST">
    <input type="text" name="username" required placeholder="Username"><br>
    <input type="password" name="password" required placeholder="Password"><br>

    <label for="isAdmin">Are you an admin?</label>
    <input type="checkbox" id="isAdmin" name="is_admin"><br>

    <div id="inviteCodeSection" style="display:none;">
        <label for="invite_code">Enter Invite Code:</label>
        <input type="text" id="inviteCode" name="invite_code" placeholder="Invite Code"><br>
    </div>

    <button type="submit">Sign Up</button>
</form>

<?php if (isset($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<script>
    document.getElementById('isAdmin').addEventListener('change', function() {
        const inviteCodeSection = document.getElementById('inviteCodeSection');
        inviteCodeSection.style.display = this.checked ? 'block' : 'none';
    });
</script>

<script src="js/scripts.js"></script> 

</body>
</html>

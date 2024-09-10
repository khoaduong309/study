<?php
session_start();
require 'db/db.php';

// Add lesson if admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    if (isset($_FILES['lesson_file']) && $_FILES['lesson_file']['error'] == 0) {
        $lesson_name = $_POST['lesson_name'];
        $lesson_file = $_FILES['lesson_file'];

        // Validate file type
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (in_array($lesson_file['type'], $allowed_types)) {
            // Ensure the uploads directory exists
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            $file_path = 'uploads/' . basename($lesson_file['name']);
            if (move_uploaded_file($lesson_file['tmp_name'], $file_path)) {
                // Store the lesson name and file path in the database
                $stmt = $conn->prepare("INSERT INTO lessons (lesson_name, lesson_file) VALUES (?, ?)");
                $stmt->bind_param("ss", $lesson_name, $lesson_file['name']);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "File upload failed.";
            }
        } else {
            echo "File type not allowed. Please upload a PDF or Word document.";
        }
    }
}

// Delete lesson if requested by admin
if (isset($_POST['delete_lesson']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $lesson_id = $_POST['lesson_id'];
    
    // Fetch the file path from the database to delete the file
    $stmt = $conn->prepare("SELECT lesson_file FROM lessons WHERE id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($lesson_file);
        $stmt->fetch();
        // Delete the file from the server
        unlink('uploads/' . $lesson_file);
    }
    $stmt->close();

    // Delete the lesson from the database
    $stmt = $conn->prepare("DELETE FROM lessons WHERE id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch lessons
$sql = "SELECT * FROM lessons";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessons - StudyHub</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .lesson-title {
            display: block; /* Block display to fill the width */
            width: 80%; /* 80% of the screen width */
            max-width: 600px; /* Max width */
            height: auto; /* Auto height for flexibility */
            background-color: #f0f0f0; /* Background color */
            margin: 20px auto; /* Centered and spacing */
            text-align: center; /* Center text */
            cursor: pointer; /* Pointer on hover */
            border: 3px solid #ccc; /* Thick border */
            border-radius: 10px; /* Rounded corners */
            transition: background-color 0.3s; /* Transition for hover */
            padding: 20px; /* Padding for content spacing */
        }
        .lesson-title:hover {
            background-color: #ddd; /* Change background on hover */
        }
        .delete-button {
            margin-top: 10px; /* Margin for spacing */
            padding: 5px 10px; /* Button padding */
            background-color: red; /* Button color */
            color: white; /* Button text color */
            border: none; /* Remove border */
            border-radius: 5px; /* Rounded button corners */
            cursor: pointer; /* Pointer on hover */
        }
        .delete-button:hover {
            background-color: darkred; /* Darker red on hover */
        }
    </style>
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="lessons.php"class="active">Lessons</a></li>
            <li><a href="exercises.php">Exercises</a></li>
            <li><a href="codepad.php">CodePad</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li>
                <?php if (isset($_SESSION['username'])): ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="profile.php">Profile</a> <!-- Add profile link -->
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
</header>

<h1>Lessons</h1>
<div id="lessonList">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="lesson-title">
            <a href="uploads/<?php echo htmlspecialchars($row['lesson_file']); ?>" target="_blank"><?php echo htmlspecialchars($row['lesson_name']); ?></a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <form method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="lesson_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete_lesson" class="delete-button" onclick="return confirm('Are you sure you want to delete this lesson? This action cannot be undone.');">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>
<div id="lessonList">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="lesson-title">
            <a href="uploads/<?php echo htmlspecialchars($row['lesson_file']); ?>" target="_blank"><?php echo htmlspecialchars($row['lesson_name']); ?></a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
                <form method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="lesson_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete_lesson" class="delete-button" onclick="return confirm('Are you sure you want to delete this lesson? This action cannot be undone.');">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
    <h2>Add New Lesson</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="lesson_name" required placeholder="Lesson Name"><br>
        <input type="file" name="lesson_file" required accept=".pdf,.doc,.docx"><br>
        <button type="submit">Add Lesson</button>
    </form>
<?php endif; ?>

<script src="js/scripts.js"></script> 

</body>
</html>

<?php $conn->close(); ?>

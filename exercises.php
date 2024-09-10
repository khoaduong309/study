<?php
session_start();
require 'db/db.php';

// Delete exercise if requested
if (isset($_POST['delete_exercise']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $exercise_id = $_POST['exercise_id'];
    
    $stmt = $conn->prepare("DELETE FROM exercises WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $exercise_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Mark exercise as complete
if (isset($_POST['complete_exercise']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $exercise_id = $_POST['exercise_id'];

    // Check if the exercise has already been marked as complete
    $check_stmt = $conn->prepare("SELECT * FROM user_exercise_completion WHERE user_id = ? AND exercise_id = ?");
    $check_stmt->bind_param("ii", $user_id, $exercise_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    // If not completed yet, insert the completion
    if ($check_result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO user_exercise_completion (user_id, exercise_id, completed) VALUES (?, ?, 1)");
        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $exercise_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    $check_stmt->close();
}

// Add exercise if admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    if (isset($_POST['exercise_name']) && !empty($_POST['exercise_name'])) {
        $exercise_name = htmlspecialchars($_POST['exercise_name']);
        $exercise_content = isset($_POST['exercise_content']) ? htmlspecialchars($_POST['exercise_content']) : '';

        $stmt = $conn->prepare("INSERT INTO exercises (exercise_name, exercise_content) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $exercise_name, $exercise_content);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        echo "Exercise name cannot be empty.";
    }
}

// Fetch exercises
$sql = "SELECT * FROM exercises";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercises - StudyHub</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .exercise-title {
            display: block;
            width: 80%;
            max-width: 600px;
            height: 100px;
            background-color: #f0f0f0;
            margin: 20px auto;
            text-align: center;
            line-height: 100px;
            cursor: pointer;
            border: 3px solid #ccc;
            border-radius: 10px;
            transition: background-color 0.3s;
        }
        .exercise-title:hover {
            background-color: #ddd;
        }
        .exercise-content {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<header>
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
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
</header>

<h1>Exercises</h1>

<div id="exerciseList">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="exercise-title" onclick="toggleContent('<?php echo $row['id']; ?>')">
            <?php echo htmlspecialchars($row['exercise_name']); ?>
        </div>
        <div id="exercise-content-<?php echo $row['id']; ?>" class="exercise-content">
            <em><?php echo htmlspecialchars($row['exercise_content']); ?></em>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="exercise_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="complete_exercise">Complete</button>
                </form>
            <?php endif; ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="exercise_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete_exercise" onclick="return confirm('Are you sure you want to delete this exercise?');">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
    <h2>Add New Exercise</h2>
    <form method="POST">
        <input type="text" name="exercise_name" required placeholder="Exercise Name"><br>
        <textarea name="exercise_content" required placeholder="Exercise Content"></textarea><br>
        <button type="submit">Add Exercise</button>
    </form>
<?php endif; ?>

<script>
    // Function to toggle the exercise content visibility
    function toggleContent(exerciseId) {
        const content = document.getElementById('exercise-content-' + exerciseId);
        content.style.display = (content.style.display === 'none' || content.style.display === '') ? 'block' : 'none';
    }
</script>

<script src="js/scripts.js"></script> 

</body>
</html>

<?php $conn->close(); ?>

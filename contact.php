<?php
session_start();
require 'db/db.php'; // Kết nối với database

// Kiểm tra xem người dùng có đăng nhập không


// Kiểm tra xem người dùng đã gửi form chưa
// Kiểm tra quyền admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Kiểm tra kết nối tới database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy tất cả liên hệ từ bảng Contact (chỉ cho admin)
if ($isAdmin) {
    $query = "SELECT Contact.ContactID, Contact.Subject, Contact.Messages, Contact.Date_sent, users.username 
              FROM Contact 
              JOIN users ON Contact.UserID = users.id
              ORDER BY Contact.Date_sent DESC";

    $result = $conn->query($query);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['users_id'];
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    $date_sent = date('Y-m-d H:i:s');

    // Chuẩn bị truy vấn để thêm liên hệ vào bảng Contact
    $query = "INSERT INTO Contact (UserID, Subject, Messages, Date_sent) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isss', $user_id, $subject, $message, $date_sent);

    if ($stmt->execute()) {
        echo "Your message has been sent successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - StudyHub</title>
    <link rel="stylesheet" href="./css/style.css">
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

<h1>Contact Us</h1>

<form action="contact.php" method="POST">
    <label for="subject">Subject:</label>
    <input type="text" id="subject" name="subject" required><br><br>

    <label for="message">Message:</label><br>
    <textarea id="message" name="message" rows="6" required></textarea><br><br>

    <button type="submit">Send Message</button>
</form>
<?php if ($isAdmin): ?>
    <h2>Messages</h2>
    <table>
        <thead>
            <tr>
                <th>Contact ID</th>
                <th>Username</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Date Sent</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['ContactID']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['Subject']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['Messages'])); ?></td>
                        <td><?php echo $row['Date_sent']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No contacts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<script src="js/scripts.js"></script> 

</body>
</html>

</body>
</html>
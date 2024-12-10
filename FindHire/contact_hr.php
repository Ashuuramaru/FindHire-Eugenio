<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'HR'");
$stmt->execute();
$hr_users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $receiver_id = $_POST['receiver_id'];  
    $sender_id = $_SESSION['user_id'];  

    if (!empty($message) && !empty($receiver_id)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$sender_id, $receiver_id, $message])) {
            $_SESSION['message'] = "Message sent successfully!";
            header("Location: contact_hr.php");
            exit();
        } else {
            $_SESSION['message'] = "Error sending message.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message to HR</title>
</head>
<body>
    <h1>Send Message to HR</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <p style="color: red;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <form action="contact_hr.php" method="POST">
        <p>
            <label for="receiver_id">Select HR:</label>
            <select name="receiver_id" required>
                <?php foreach ($hr_users as $hr): ?>
                    <option value="<?php echo $hr['user_id']; ?>"><?php echo htmlspecialchars($hr['username']); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="message">Message:</label>
            <textarea name="message" rows="5" cols="50" required></textarea>
        </p>
        <button type="submit">Send Message</button>
    </form>

    <p><a href="applicant_dashboard.php">Go back to Dashboard</a></p>
</body>
</html>

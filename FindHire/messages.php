<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT m.*, u.username AS sender FROM messages m JOIN users u ON m.sender_id = u.user_id WHERE m.receiver_id = ?");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $receiver_id, $message]);

    echo "Reply sent successfully.";
}
?>

<h1>Messages</h1>
<ul>
    <?php foreach ($messages as $msg): ?>
        <li>
            <strong>From:</strong> <?php echo htmlspecialchars($msg['sender']); ?><br>
            <p><?php echo htmlspecialchars($msg['message']); ?></p>

            <form method="POST">
                <input type="hidden" name="receiver_id" value="<?php echo $msg['sender_id']; ?>">
                <textarea name="message" required placeholder="Reply to this message"></textarea>
                <button type="submit">Send Reply</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

<a href="hr_dashboard.php">Back to Dashboard</a>

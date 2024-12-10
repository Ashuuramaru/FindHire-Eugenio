<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$hr_id = $_SESSION['user_id'];

$stmtJobPosts = $pdo->prepare("SELECT * FROM job_posts WHERE created_by = ?");
$stmtJobPosts->execute([$hr_id]);
$job_posts = $stmtJobPosts->fetchAll();

$stmtApplications = $pdo->prepare("
    SELECT a.*, j.title AS job_title, u.username AS applicant_name, a.status AS application_status
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    JOIN users u ON a.applicant_id = u.user_id
    WHERE j.created_by = ?
");
$stmtApplications->execute([$hr_id]);
$applications = $stmtApplications->fetchAll();

$stmtMessages = $pdo->prepare("
    SELECT m.*, u.username AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ?
    ORDER BY m.sent_at DESC
");
$stmtMessages->execute([$hr_id]);
$messages = $stmtMessages->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $receiver_id = $_POST['receiver_id'];  
    $sender_id = $_SESSION['user_id']; 

    if (!empty($message) && !empty($receiver_id)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type) VALUES (?, ?, ?, 'reply')");
        if ($stmt->execute([$sender_id, $receiver_id, $message])) {
            $_SESSION['message'] = "Reply sent successfully!";
            header("Location: hr_dashboard.php");  
            exit();
        } else {
            $_SESSION['message'] = "Error sending reply.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
}
?>

<h1>HR Dashboard</h1>

<p><a href="create_job.php">
    <button>Add New Job Post</button>
</a></p>

<h2>Manage Job Posts</h2>
<?php if ($job_posts): ?>
    <ul>
        <?php foreach ($job_posts as $job): ?>
            <li>
                <strong><?php echo htmlspecialchars($job['title']); ?></strong><br>
                <p><?php echo htmlspecialchars($job['description']); ?></p>
                <a href="edit_job.php?job_id=<?php echo $job['job_id']; ?>">Edit</a> |
                <a href="delete_job.php?job_id=<?php echo $job['job_id']; ?>">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No job posts available.</p>
<?php endif; ?>

<h2>Applications</h2>
<?php if ($applications): ?>
    <ul>
        <?php foreach ($applications as $application): ?>
            <li>
                <strong><?php echo htmlspecialchars($application['job_title']); ?></strong><br>
                <p><strong>Applicant:</strong> <?php echo htmlspecialchars($application['applicant_name']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($application['application_status']); ?></p>
                <a href="update_status.php?application_id=<?php echo $application['application_id']; ?>">Update Status</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No applications yet.</p>
<?php endif; ?>

<h2>Messages from Applicants</h2>
<?php if ($messages): ?>
    <ul>
        <?php foreach ($messages as $message): ?>
            <li>
                <strong>From: <?php echo htmlspecialchars($message['sender_name']); ?></strong><br>
                <p><?php echo htmlspecialchars($message['message']); ?></p>
                <p><small>Sent on: <?php echo $message['sent_at']; ?></small></p>

                <form action="hr_dashboard.php" method="POST">
                    <input type="hidden" name="receiver_id" value="<?php echo $message['sender_id']; ?>">
                    <textarea name="message" rows="3" cols="50" required></textarea><br>
                    <button type="submit">Reply</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No messages from applicants yet.</p>
<?php endif; ?>

<p><a href="logout.php">Logout</a></p>

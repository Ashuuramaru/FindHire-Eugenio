<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: login.php");
    exit();
}

$applicant_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.*, j.title AS job_title, j.description AS job_description, a.status AS application_status
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    WHERE a.applicant_id = ?
");
$stmt->execute([$applicant_id]);
$applications = $stmt->fetchAll();

$stmtJobPosts = $pdo->query("SELECT * FROM job_posts");
$job_posts = $stmtJobPosts->fetchAll();

$stmtMessages = $pdo->prepare("
    SELECT m.*, u.username AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ?
    ORDER BY m.sent_at DESC
");
$stmtMessages->execute([$applicant_id]);
$messages = $stmtMessages->fetchAll();
?>

<h1>Applicant Dashboard</h1>

<h2>Available Job Posts</h2>
<?php if ($job_posts): ?>
    <ul>
        <?php foreach ($job_posts as $job): ?>
            <li>
                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
                <a href="apply_job.php?job_id=<?php echo $job['job_id']; ?>">Apply</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No job posts available at the moment.</p>
<?php endif; ?>

<h2>Your Applications</h2>
<?php if ($applications): ?>
    <ul>
        <?php foreach ($applications as $application): ?>
            <li>
                <strong><?php echo htmlspecialchars($application['job_title']); ?></strong><br>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($application['application_status']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($application['job_description']); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>You haven't applied to any jobs yet.</p>
<?php endif; ?>

<h2>Your Messages</h2>
<?php if ($messages): ?>
    <ul>
        <?php foreach ($messages as $message): ?>
            <li>
                <strong>From: <?php echo htmlspecialchars($message['sender_name']); ?></strong><br>
                <p><?php echo htmlspecialchars($message['message']); ?></p>
                <p><small>Sent on: <?php echo $message['sent_at']; ?></small></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No messages from HR yet.</p>
<?php endif; ?>

<p><a href="contact_hr.php">
    <button>Send Message to HR</button>
</a></p>

<p><a href="logout.php">Logout</a></p>

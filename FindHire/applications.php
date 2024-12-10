<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("
    SELECT a.*, j.title, u.username AS applicant_name 
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    JOIN users u ON a.applicant_id = u.user_id
");

$applications = $stmt->fetchAll();
?>

<h1>Applications</h1>
<ul>
    <?php foreach ($applications as $app): ?>
        <li>
            <strong>Applicant:</strong> <?php echo htmlspecialchars($app['applicant_name']); ?><br>
            <strong>Job Title:</strong> <?php echo htmlspecialchars($app['title']); ?><br>
            <strong>Status:</strong> <?php echo htmlspecialchars($app['status']); ?><br>
            <a href="update_status.php?application_id=<?php echo $app['application_id']; ?>&status=Accepted">Accept</a> |
            <a href="update_status.php?application_id=<?php echo $app['application_id']; ?>&status=Rejected">Reject</a>
        </li>
    <?php endforeach; ?>
</ul>

<a href="hr_dashboard.php">Back to Dashboard</a>

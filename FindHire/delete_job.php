<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    echo "Job ID is missing.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $stmt = $pdo->prepare("DELETE FROM job_posts WHERE job_id = ?");
    $deleted = $stmt->execute([$job_id]);

    if ($deleted) {
        echo "Job post deleted successfully. <a href='hr_dashboard.php'>Go back to HR Dashboard</a>";
        exit();
    } else {
        echo "Error deleting job post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Job Post</title>
</head>
<body>
    <h1>Delete Job Post</h1>
    <p>Are you sure you want to delete this job post?</p>
    <form action="core/handleForms.php" method="POST">
        <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
        <button type="submit" name="deleteJobBtn">Delete Job</button>
    </form>
</body>
</html>

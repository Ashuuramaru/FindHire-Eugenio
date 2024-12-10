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

$job = getJobPostByID($pdo, $job_id);

if (!$job) {
    echo "Job not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("UPDATE job_posts SET title = ?, description = ? WHERE job_id = ?");
    $updated = $stmt->execute([$title, $description, $job_id]);

    if ($updated) {
        echo "Job post updated successfully. <a href='hr_dashboard.php'>Go back to HR Dashboard</a>";
        exit();
    } else {
        echo "Error updating job post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Job Post</title>
</head>
<body>
    <h1>Edit Job Post</h1>
        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
            <input type="text" name="title" value="<?php echo $job['title']; ?>" required>
            <textarea name="description" required><?php echo $job['description']; ?></textarea>
            <button type="submit" name="editJobBtn">Update Job</button>
        </form>
    <p><a href="hr_dashboard.php">Cancel and Go Back</a></p>
</body>
</html>

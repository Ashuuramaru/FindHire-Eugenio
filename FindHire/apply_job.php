<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
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
    $applicant_id = $_SESSION['user_id'];
    $resume = $_FILES['resume'];

    if (!empty($resume['name'])) {
        $resumePath = 'uploads/' . basename($resume['name']);
        
        if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_id, resume) VALUES (?, ?, ?)");
            $stmt->execute([$job_id, $applicant_id, $resumePath]);

            echo "Application submitted successfully! <a href='applicant_dashboard.php'>Go back to dashboard</a>";
        } else {
            echo "Error uploading resume.";
        }
    } else {
        echo "Please upload a resume.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Job</title>
</head>
<body>
    <h1>Apply for Job: <?php echo htmlspecialchars($job['title']); ?></h1>

    <form action="apply_job.php?job_id=<?php echo $job_id; ?>" method="POST" enctype="multipart/form-data">
        <p>
            <label for="resume">Upload Resume:</label>
            <input type="file" name="resume" required>
        </p>
        <button type="submit">Apply</button>
    </form>

    <p><a href="applicant_dashboard.php">Cancel and go back to dashboard</a></p>
</body>
</html>

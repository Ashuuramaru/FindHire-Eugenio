<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$title, $description, $created_by]);

    echo "Job post created successfully. <a href='hr_dashboard.php'>Go back</a>";
}
?>

<h1>Add a Job post</h1>

<form action="core/handleForms.php" method="POST">
    <input type="text" name="title" required placeholder="Job Title">
    <textarea name="description" required placeholder="Job Description"></textarea>
    <button type="submit" name="createJobBtn">Create Job Post</button>
</form>


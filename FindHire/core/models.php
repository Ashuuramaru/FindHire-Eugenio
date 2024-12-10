<?php
require_once 'dbConfig.php';


function checkIfUserExists($pdo, $username) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch();
}

function insertNewUser($pdo, $username, $password, $role) {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $password, $role]);
}


function getAllJobPosts($pdo) {
    $stmt = $pdo->query("SELECT * FROM job_posts");
    return $stmt->fetchAll();
}

function getJobPostByID($pdo, $job_id) {
    $stmt = $pdo->prepare("SELECT * FROM job_posts WHERE job_id = ?");
    $stmt->execute([$job_id]);
    return $stmt->fetch();
}

function insertJobPost($pdo, $title, $description, $created_by) {
    $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)");
    return $stmt->execute([$title, $description, $created_by]);
}

function updateJobPost($pdo, $job_id, $title, $description) {
    $stmt = $pdo->prepare("UPDATE job_posts SET title = ?, description = ? WHERE job_id = ?");
    return $stmt->execute([$title, $description, $job_id]);
}

function deleteJobPost($pdo, $job_id) {
    $stmt = $pdo->prepare("DELETE FROM job_posts WHERE job_id = ?");
    return $stmt->execute([$job_id]);
}


function getAllApplications($pdo) {
    $stmt = $pdo->query("
        SELECT a.*, j.title, u.username AS applicant_name
        FROM applications a
        JOIN job_posts j ON a.job_id = j.job_id
        JOIN users u ON a.applicant_id = u.user_id
    ");
    return $stmt->fetchAll();
}

function insertApplication($pdo, $job_id, $applicant_id, $resume) {
    $stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_id, resume) VALUES (?, ?, ?)");
    return $stmt->execute([$job_id, $applicant_id, $resume]);
}

function updateApplicationStatus($pdo, $application_id, $status) {
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE application_id = ?");
    return $stmt->execute([$status, $application_id]);
}


function insertMessage($pdo, $sender_id, $receiver_id, $message) {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    return $stmt->execute([$sender_id, $receiver_id, $message]);
}

function getMessagesForUser($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE m.receiver_id = ?
        ORDER BY m.sent_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}
?>

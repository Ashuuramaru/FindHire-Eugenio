<?php
session_start();
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['registerUserBtn'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if (!empty($username) && !empty($password) && !empty($role)) {
        $userExists = checkIfUserExists($pdo, $username);

        if (!$userExists) {
            if (insertNewUser($pdo, $username, $password, $role)) {
                $_SESSION['message'] = "Registration successful!";
                header("Location: ../login.php");
                exit();
            } else {
                $_SESSION['message'] = "Error inserting user.";
            }
        } else {
            $_SESSION['message'] = "Username already exists.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
    header("Location: ../register.php");
    exit();
}

if (isset($_POST['loginUserBtn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $user = checkIfUserExists($pdo, $username);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $username;

        if ($user['role'] === 'HR') {
            header("Location: ../hr_dashboard.php");
        } else {
            header("Location: ../applicant_dashboard.php");
        }
        exit();
    } else {
        $_SESSION['message'] = "Invalid username or password.";
        header("Location: ../login.php");
        exit();
    }
}

if (isset($_GET['logoutUserBtn'])) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['createJobBtn'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $created_by = $_SESSION['user_id'];

    if (!empty($title) && !empty($description)) {
        if (insertJobPost($pdo, $title, $description, $created_by)) {
            $_SESSION['message'] = "Job post created successfully!";
        } else {
            $_SESSION['message'] = "Error creating job post.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
    header("Location: ../hr_dashboard.php");
    exit();
}

if (isset($_POST['editJobBtn'])) {
    $job_id = $_POST['job_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    $stmt = $pdo->prepare("UPDATE job_posts SET title = ?, description = ? WHERE job_id = ?");
    if ($stmt->execute([$title, $description, $job_id])) {
        $_SESSION['message'] = "Job post updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating job post.";
    }
    header("Location: ../hr_dashboard.php");
    exit();
}

if (isset($_POST['deleteJobBtn'])) {
    $job_id = $_POST['job_id'];

    $stmt = $pdo->prepare("DELETE FROM job_posts WHERE job_id = ?");
    if ($stmt->execute([$job_id])) {
        $_SESSION['message'] = "Job post deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting job post.";
    }
    header("Location: ../hr_dashboard.php");
    exit();
}

if (isset($_POST['applyJobBtn'])) {
    $job_id = $_POST['job_id'];
    $applicant_id = $_SESSION['user_id'];
    $resume = $_FILES['resume'];

    if (!empty($job_id) && !empty($resume['name'])) {
        $resumePath = 'uploads/' . basename($resume['name']);
        
        if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_id, resume, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$job_id, $applicant_id, $resumePath, 'Pending']);  

            echo "Application submitted successfully! <a href='applicant_dashboard.php'>Go back to dashboard</a>";
        } else {
            echo "Error uploading resume.";
        }
    } else {
        echo "Please upload a resume.";
    }
    exit();
}


if (isset($_POST['updateApplicationStatusBtn'])) {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];

    if (updateApplicationStatus($pdo, $application_id, $status)) {
        $_SESSION['message'] = "Application status updated to $status.";
    } else {
        $_SESSION['message'] = "Error updating application status.";
    }
    header("Location: ../applications.php");
    exit();
}

if (isset($_POST['sendMessageBtn'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);
    $sender_id = $_SESSION['user_id'];

    if (!empty($receiver_id) && !empty($message)) {
        if (insertMessage($pdo, $sender_id, $receiver_id, $message)) {
            $_SESSION['message'] = "Message sent successfully!";
        } else {
            $_SESSION['message'] = "Error sending message.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
    header("Location: ../contact_hr.php");
    exit();
}
?>

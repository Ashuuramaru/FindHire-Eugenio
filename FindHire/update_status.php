<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];

    $stmt = $pdo->prepare("SELECT * FROM applications WHERE application_id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        $_SESSION['message'] = "Application not found.";
        header("Location: hr_dashboard.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = $_POST['status']; 

        $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE application_id = ?");
        if ($stmt->execute([$status, $application_id])) {
            $_SESSION['message'] = "Application status updated to $status.";
        } else {
            $_SESSION['message'] = "Error updating status.";
        }

        header("Location: hr_dashboard.php"); 
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Application Status</title>
</head>
<body>

<h1>Update Application Status</h1>

<?php if (isset($_SESSION['message'])): ?>
    <p style="color: red;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
<?php endif; ?>

<form action="update_status.php?application_id=<?php echo $application['application_id']; ?>" method="POST">
    <label for="status">Choose status:</label>
    <select name="status" id="status" required>
        <option value="Accepted" <?php if ($application['status'] == 'Accepted') echo 'selected'; ?>>Accept</option>
        <option value="Rejected" <?php if ($application['status'] == 'Rejected') echo 'selected'; ?>>Reject</option>
    </select>

    <button type="submit">Update Status</button>
</form>

<p><a href="hr_dashboard.php">Back to HR Dashboard</a></p>

</body>
</html>

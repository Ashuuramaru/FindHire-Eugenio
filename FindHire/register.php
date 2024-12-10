<?php
require 'core/dbConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; 

    if (!empty($username) && !empty($password) && !empty($role)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $password, $role])) {
                $_SESSION['message'] = "Registration successful!";
                header("Location: login.php");
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <p style="color: red;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <p>
            <label for="username">Username:</label>
            <input type="text" name="username" required>
        </p>
        <p>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
        </p>
        <p>
            <label for="role">Role:</label>
            <select name="role">
                <option value="Applicant">Applicant</option>
                <option value="HR">HR</option>
            </select>
        </p>
        <button type="submit">Register</button>
    </form>
</body>
</html>

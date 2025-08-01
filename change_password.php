<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "computer_shop");

$user_id = $_SESSION['user_id'];
$current = $_POST['current_password'];
$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

// Fetch current password from DB
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($db_pass);
$stmt->fetch();
$stmt->close();

// Verify old password
if (!password_verify($current, $db_pass)) {
    echo "<script>alert('Incorrect current password.'); window.location='profile.php';</script>";
    exit;
}

// Confirm new password
if ($new !== $confirm) {
    echo "<script>alert('New passwords do not match.'); window.location='profile.php';</script>";
    exit;
}

// Update password
$new_hashed = password_hash($new, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $new_hashed, $user_id);
$stmt->execute();

echo "<script>alert('Password updated successfully.'); window.location='profile.php';</script>";
?>

<?php
include '../db.php'; // adjust path if needed

$username = 'admin';
$password_plain = 'admin123'; // your desired password

// Hash the password securely
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

// Prepare insert
$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param('ss', $username, $password_hash);

if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $conn->error;
}
?>

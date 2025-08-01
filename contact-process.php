<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "computer_shop";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Get form inputs
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$message = $_POST['message'];

// Validate email against users table
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $stmt = $conn->prepare("INSERT INTO contact_messages (first_name, last_name, email, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $message);
    $stmt->execute();
    echo "<script>alert('Your message has been sent.'); window.history.back();</script>";
} else {
    echo "<script>alert('Message could not be sent.'); window.history.back();</script>";
}

$conn->close();
?>

<?php
// Remove incorrect include if file doesn't exist or needed
// include '../includes/admin_auth.php'; // REMOVE or FIX if needed

$host = "localhost";
$user = "root";
$password = "";
$dbname = "computer_shop"; // ✅ replace with your actual DB name

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Set your DB password
$db = 'computer_shop'; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>






<?php
session_start();
include 'db.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

$result = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE session_id = '$session_id'");
$row = $result->fetch_assoc();
echo $row['total'] ?? 0;
?>
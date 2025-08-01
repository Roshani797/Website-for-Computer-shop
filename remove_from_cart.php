<?php
session_start();
include 'db.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

// Check if 'id' parameter is present
if (isset($_GET['id'])) {
    $cart_item_id = intval($_GET['id']);

    // Prepare delete statement
    $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ? AND id = ?");
    $stmt->bind_param("si", $session_id, $cart_item_id);
    $stmt->execute();
}

// Redirect back to cart page
header("Location: cart.php");
exit;

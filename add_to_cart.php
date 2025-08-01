<?php
session_start();
include 'db.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $product_name = $_POST['product_name'] ?? '';
    $product_price = floatval($_POST['product_price'] ?? 0);
    $product_image = $_POST['product_image'] ?? '';
    
    if ($product_id && $product_name && $product_price && $product_image) {
        // Check if product already in cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
        $stmt->bind_param("si", $session_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity by 1
            $row = $result->fetch_assoc();
            $new_qty = $row['quantity'] + 1;
            $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $update->bind_param("ii", $new_qty, $row['id']);
            $update->execute();
        } else {
            // Insert new product
            $insert = $conn->prepare("INSERT INTO cart (session_id, product_id, product_name, product_price, product_image, quantity) VALUES (?, ?, ?, ?, ?, 1)");
            $insert->bind_param("sisds", $session_id, $product_id, $product_name, $product_price, $product_image);
            $insert->execute();
        }
    }
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php"));
exit;



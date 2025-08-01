<?php
session_start();
// Temporary hardcoded user_id
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}
include 'db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $product_id = $_POST['product_id'];
        $name = $_POST['product_name'];
        $price = $_POST['product_price'];
        $image = $_POST['product_image'];

        // Check if already in wishlist
        $check = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
        $check->bind_param("ii", $user_id, $product_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id, product_name, product_price, product_image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisds", $user_id, $product_id, $name, $price, $image);
            $stmt->execute();
        }
        echo getCount($conn, $user_id);

    } elseif ($action === 'remove') {
        $product_id = $_POST['product_id'];
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        echo getCount($conn, $user_id);

    } elseif ($action === 'count') {
        echo getCount($conn, $user_id);
    }
}

function getCount($conn, $user_id) {
    $res = $conn->prepare("SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?");
    $res->bind_param("i", $user_id);
    $res->execute();
    $count = $res->get_result()->fetch_assoc()['total'];
    return $count;
}
?>

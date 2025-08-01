<?php
session_start();

$product_id = $_POST['product_id'] ?? '';

if (isset($_SESSION['wishlist'])) {
    if (($key = array_search($product_id, $_SESSION['wishlist'])) !== false) {
        unset($_SESSION['wishlist'][$key]);
        // Reindex array
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
        echo "Product removed from wishlist.";
        exit;
    }
}

echo "Product not found in wishlist.";




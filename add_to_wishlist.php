<?php
session_start();
if (!isset($_SESSION['wishlist'])) {
  $_SESSION['wishlist'] = [];
}

$product_id = $_POST['product_id'] ?? '';

if ($product_id && !in_array($product_id, $_SESSION['wishlist'])) {
  $_SESSION['wishlist'][] = $product_id;
  echo "Product added to wishlist!";
} else {
  echo "Product already in wishlist.";
}
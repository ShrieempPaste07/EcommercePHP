<?php
session_start();
require '../db.php';

if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    die("Invalid request.");
}

$product_id = (int) $_POST['product_id'];

// Initialize cart array if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add +1 quantity or initialize
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += 1;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

header("Location: cart.php");
exit;
?>

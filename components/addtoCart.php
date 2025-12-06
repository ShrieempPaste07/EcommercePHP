<?php
session_start();
require '../db.php';

if (!isset($_POST['product_id'])) {
    die("Invalid request.");
}

$product_id = $_POST['product_id'];

// Cart Array holder
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If product not yet added
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += 1; 
} else {
    $_SESSION['cart'][$product_id] = 1;
}

header("Location: cart.php");
exit();
?>

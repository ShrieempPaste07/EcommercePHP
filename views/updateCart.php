<?php
session_start();

if (!isset($_POST['product_id'], $_POST['action'])) {
    header("Location: cart.php");
    exit;
}

$product_id = (int)$_POST['product_id'];
$action = $_POST['action'];

if (!isset($_SESSION['cart'][$product_id])) {
    header("Location: cart.php");
    exit;
}

switch ($action) {
    case 'increase':
        $_SESSION['cart'][$product_id]++;
        break;
    case 'decrease':
        $_SESSION['cart'][$product_id]--;
        if ($_SESSION['cart'][$product_id] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }
        break;
    case 'remove':
        unset($_SESSION['cart'][$product_id]);
        break;
}

header("Location: cart.php");
exit;

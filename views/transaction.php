<?php
session_start();
require '../db.php';

// Determine if coming from "Buy Now" or "Cart Checkout"
$cart_items = $_SESSION['cart'] ?? [];
$total = 0;
$products = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['cart_action']) && $_POST['cart_action'] === 'checkout') {
        // Checkout all items in cart
        if (empty($cart_items)) {
            echo "<script>alert('Your cart is empty.'); window.location.href='product.php';</script>";
            exit;
        }

        foreach ($cart_items as $product_id => $qty) {
            $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $product = $res->fetch_assoc();
            $stmt->close();

            if ($product) {
                $product['quantity'] = $qty;
                $product['subtotal'] = $qty * floatval($product['product_price']);
                $total += $product['subtotal'];
                $products[] = $product;
            }
        }

    } elseif (isset($_POST['product_id'])) {
        // Single Buy Now product
        $product_id = (int) $_POST['product_id'];
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $product = $res->fetch_assoc();
        $stmt->close();

        if (!$product) {
            header("Location: product.php");
            exit;
        }

        $product['quantity'] = 1;
        $product['subtotal'] = floatval($product['product_price']);
        $total = $product['subtotal'];
        $products[] = $product;
    }

} else {
    // Accessed directly
    echo "<script>alert('No items to checkout.'); window.location.href='product.php';</script>";
    exit;
}

// Handle final checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address'], $_POST['payment'])) {
    // Clear the cart
    unset($_SESSION['cart']);

    echo "<script>alert('Thanks for buying!'); window.location.href='product.php';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include '../components/header.php'; ?>
    <?php include '../components/design.php'; ?>
    <div class="max-w-lg mx-auto mt-10 bg-white rounded-xl shadow-md p-6">
    <h2 class="text-2xl font-bold text-purple-800 mb-6 text-center">Checkout Information</h2>

    <div class="mb-4">
        <h3 class="font-bold text-lg mb-2">Products:</h3>
        <?php foreach ($products as $p): ?>
            <div class="flex justify-between mb-1">
                <span><?= htmlspecialchars($p['product_name']) ?> x <?= $p['quantity'] ?></span>
                <span>$<?= number_format($p['subtotal'], 2) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" class="space-y-6">
        <div>
            <label class="block font-semibold mb-1 text-gray-700">Shipping Address:</label>
            <input type="text" name="address" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-purple-600">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Payment Method:</label>
            <label class="flex items-center gap-2 mb-2">
                <input type="radio" name="payment" value="credit" required>
                <span>Credit Card</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" name="payment" value="debit" required>
                <span>Debit Card</span>
            </label>
        </div>

        <div class="text-right text-xl font-bold text-purple-900 border-t pt-3">
            Total Price: $<?= number_format($total, 2) ?>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white font-bold text-lg py-3 rounded-lg hover:bg-green-700 transition">
            Buy Now
        </button>
    </form>
</div>
    
</body>
</html>

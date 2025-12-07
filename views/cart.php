<?php
session_start();
require '../db.php';

// Initialize cart
$cart_items = $_SESSION['cart'] ?? [];

// Fetch product info
$products = [];
$total_price = 0;

foreach ($cart_items as $product_id => $qty) {
    $product = get_product_by_id($product_id);
    if ($product) {
        $product['quantity'] = $qty;
        $product['subtotal'] = $qty * floatval($product['product_price']);
        $total_price += $product['subtotal'];
        $products[] = $product;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F8F7FC] min-h-screen">

<?php include '../components/design.php'; ?>
<?php include '../components/header.php'; ?>

<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-purple-700 mb-6">Your Cart</h1>

    <?php if (empty($products)): ?>
        <p class="text-gray-600">Your cart is empty.</p>
        <div class="mt-6">
            <a href="product.php" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700">
                Shop Now
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
            <div class="bg-white shadow-md rounded-lg p-4 flex items-center justify-between mb-3">

                <!-- Product Info -->
                <div class="flex items-center gap-4">
                    <!-- Image -->
                    <div class="w-20 h-20 bg-gray-200 rounded overflow-hidden">
                        <?php if (!empty($p['product_image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($p['product_image']) ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <p class="text-center text-sm">No Image</p>
                        <?php endif; ?>
                    </div>

                    <!-- Details -->
                    <div>
                        <h2 class="font-bold text-lg"><?= htmlspecialchars($p['product_name']) ?></h2>
                        <p class="text-sm"><?= htmlspecialchars($p['product_category']) ?></p>
                        <p class="text-sm font-semibold">$<?= number_format($p['product_price'], 2) ?></p>
                    </div>
                </div>

                <!-- Quantity Controls & Subtotal -->
                <div class="text-right flex flex-col items-end gap-2">

                    <!-- Quantity Increment/Decrement -->
                    <div class="flex items-center gap-2">
                        <form method="POST" action="updateCart.php">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <input type="hidden" name="action" value="decrease">
                            <button type="submit" class="px-2 py-1 bg-gray-300 rounded hover:bg-gray-400">-</button>
                        </form>

                        <span class="px-2"><?= $p['quantity'] ?></span>

                        <form method="POST" action="updateCart.php">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <input type="hidden" name="action" value="increase">
                            <button type="submit" class="px-2 py-1 bg-gray-300 rounded hover:bg-gray-400">+</button>
                        </form>
                    </div>

                    <p class="font-bold text-purple-700">$<?= number_format($p['subtotal'], 2) ?></p>

                    <!-- Remove button -->
                    <form method="POST" action="updateCart.php">
                        <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit" class="text-red-600 text-sm hover:underline">Remove</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Total -->
        <div class="text-right font-bold text-xl text-purple-800 mt-4">
            Total: $<?= number_format($total_price, 2) ?>
        </div>

        <!-- Buy Now -->
        <form method="POST" action="transaction.php" class="text-right mt-6">
    <input type="hidden" name="cart_action" value="checkout">
    <button type="submit" class="px-6 py-3 bg-green-600 text-white text-lg rounded shadow hover:bg-green-700 transition">
        BUY NOW
    </button>
</form>


    <?php endif; ?>
</div>

</body>
</html>

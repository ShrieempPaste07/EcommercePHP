<?php
session_start();
require "../db.php";

// 🛒 Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 🛠 Handle Button Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === "increment") {
        $_SESSION['cart'][$product_id]++;
    }

    if ($action === "decrement") {
        $_SESSION['cart'][$product_id]--;
        if ($_SESSION['cart'][$product_id] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    if ($action === "remove") {
        unset($_SESSION['cart'][$product_id]);
    }

    header("Location: cart.php");
    exit();
}

// 🧠 Build Cart Array with product info
$cartItems = [];

foreach ($_SESSION['cart'] as $product_id => $qty) {
    $product = get_product_by_id($product_id);
    if ($product) {
        $cartItems[] = [
            "id"       => $product_id,
            "name"     => $product['product_name'],
            "price"    => $product['price'],
            "image"    => "../uploads/" . $product['image'],
            "desc"     => $product['description'],
            "quantity" => $qty
        ];
    }
}

// 💰 Compute total
function totalPrice($items) {
    $sum = 0;
    foreach ($items as $item) {
        $sum += $item['price'] * $item['quantity'];
    }
    return $sum;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
    <?php include '../components/design.php'; ?>

    <div class="min-h-screen bg-[#F8F7FC] p-6">
        <h1 class="text-3xl font-bold mb-6 text-[#1A1A1A] Header">🛒 Shopping Cart</h1>

        <?php if (count($cartItems) > 0): ?>
            <div class="space-y-4">
                <?php foreach ($cartItems as $item): ?>
                    <div class="bg-white p-4 rounded shadow flex justify-between items-center border border-[#E3E3E3]">
                        
                        <div class="flex gap-4">
                            <img src="<?= $item['image']; ?>" class="w-24 h-24 rounded object-cover bg-[#EEE]" />

                            <div>
                                <h2 class="text-lg font-semibold Header"><?= htmlspecialchars($item['name']); ?></h2>
                                <p class="text-gray-600 Description"><?= htmlspecialchars($item['desc']); ?></p>

                                <div class="flex items-center gap-2 mt-2">
                                    
                                    <!-- DECREMENT -->
                                    <form method="post" action="cart.php">
                                        <input type="hidden" name="action" value="decrement">
                                        <input type="hidden" name="id" value="<?= $item['id']; ?>">
                                        <button type="submit"
                                            class="px-3 py-1 bg-[#ECE7FA] rounded hover:bg-[#d9cfff]">-</button>
                                    </form>

                                    <span class="px-2 font-bold"><?= $item['quantity']; ?></span>

                                    <!-- INCREMENT -->
                                    <form method="post" action="cart.php">
                                        <input type="hidden" name="action" value="increment">
                                        <input type="hidden" name="id" value="<?= $item['id']; ?>">
                                        <button type="submit"
                                            class="px-3 py-1 bg-[#ECE7FA] rounded hover:bg-[#d9cfff]">+</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="text-xl font-bold Description">
                                ₱<?= number_format($item['price'] * $item['quantity'], 2); ?>
                            </span>

                            <!-- REMOVE -->
                            <form method="post" action="cart.php">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="id" value="<?= $item['id']; ?>">
                                <button type="submit" class="text-[#FF4C4C] hover:text-[#FF6666] Accent">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>

            <div class="text-right mt-6 text-2xl font-bold Header">
                Total: ₱<?= number_format(totalPrice($cartItems), 2); ?>
            </div>

        <?php else: ?>
            <p class="text-gray-600 mt-6">🛍️ Your cart is currently empty.</p>
        <?php endif; ?>

    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>

<?php
session_start();
include '../db.php';

// Define categories
$categories = ["Mouse", "Keyboard", "Chair", "Headset", "Mousepads"];

// Determine selected category (default = All)
$selectedCategory = $_GET['category'] ?? 'All';

// Fetch products based on category
$productsByCategory = [];

if ($selectedCategory === 'All') {
    foreach ($categories as $cat) {
        $stmt = $conn->prepare("SELECT product_id, product_name, product_category, product_description, product_price, product_image 
                                FROM products 
                                WHERE product_category = ?
                                ORDER BY product_id DESC LIMIT 5");
        $stmt->bind_param("s", $cat);
        $stmt->execute();
        $res = $stmt->get_result();
        $productsByCategory[$cat] = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} else {
    if (in_array($selectedCategory, $categories)) {
        $stmt = $conn->prepare("SELECT product_id, product_name, product_category, product_description, product_price, product_image 
                                FROM products 
                                WHERE product_category = ?
                                ORDER BY product_id DESC LIMIT 5");
        $stmt->bind_param("s", $selectedCategory);
        $stmt->execute();
        $res = $stmt->get_result();
        $productsByCategory[$selectedCategory] = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $productsByCategory = [];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg-[#F8F7FC]">

<?php include '../components/design.php'; ?>
<?php include '../components/header.php'; ?>

<!-- Category buttons -->
<div class="bg-[#D6C4FF] h-28 flex items-center justify-center gap-4 px-4 shadow-md flex-wrap">
    <a href="product.php?category=All">
        <button class="bg-white text-[#8A2BE2] font-semibold py-2 px-4 rounded-lg shadow hover:bg-[#8A2BE2] hover:text-white transition">
            All
        </button>
    </a>
    <?php foreach ($categories as $cat): ?>
        <a href="product.php?category=<?= urlencode($cat) ?>">
            <button class="bg-white text-[#8A2BE2] font-semibold py-2 px-4 rounded-lg shadow hover:bg-[#8A2BE2] hover:text-white transition">
                <?= htmlspecialchars($cat) ?>
            </button>
        </a>
    <?php endforeach; ?>
</div>

<!-- Category Sections -->
<div class="p-6 space-y-12">
<?php foreach ($productsByCategory as $cat => $products): ?>
    <section id="<?= strtolower($cat) ?>">
        <h2 class="text-3xl font-bold text-[#8A2BE2] mb-4"><?= htmlspecialchars($cat) ?></h2>

        <div class="overflow-x-auto">
            <div class="flex gap-6">

            <?php if (empty($products)): ?>
                <p class="text-gray-500 italic">No products found.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>

                    <!-- CARD DESIGN -->
                    <div class="bg-[#D6C4FF] min-w-[300px] h-[350px] rounded-md shadow-lg overflow-hidden flex flex-col">

                        <!-- Image Section -->
                        <div class="bg-[#8A2BE2] h-[150px] flex items-center justify-center overflow-hidden">
                            <?php if (!empty($product['product_image'])): ?>
                                <img 
                                    src="data:image/jpeg;base64,<?= base64_encode($product['product_image']) ?>"
                                    class="w-full h-full object-cover"
                                >
                            <?php else: ?>
                                <p class="text-white font-bold">No Image</p>
                            <?php endif; ?>
                        </div>

                        <!-- Content Section -->
                        <div class="flex-1 p-4 flex flex-col justify-between">
                            <div>
                                <h1 class="text-lg font-bold mb-1"><?= htmlspecialchars($product['product_name']) ?></h1>
                                <p class="text-sm text-gray-700 mb-2"><?= htmlspecialchars($product['product_description']) ?></p>
                                <p class="text-md font-semibold">$<?= number_format($product['product_price'], 2) ?></p>
                            </div>

                            <div class="flex gap-2 mt-3">
                                <!-- Add to Cart Form -->
                                <form method="POST" action="../views/addToCart.php" class="flex-1">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <button type="submit"
                                        class="w-full bg-[#44BCFF] text-white font-bold py-1 rounded hover:bg-[#3399cc] transition">
                                        Add to Cart
                                    </button>
                                </form>

                                <!-- Buy Now Form -->
                                <form method="POST" action="transaction.php" class="flex-1">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <button type="submit"
                                        class="w-full bg-[#FF675E] text-white font-bold py-1 rounded hover:bg-[#e65550] transition">
                                        Buy Now
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>

            </div>
        </div>

    </section>
<?php endforeach; ?>
</div>

</body>
</html>

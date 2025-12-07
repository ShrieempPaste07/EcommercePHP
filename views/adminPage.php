<?php
session_start();
include '../db.php';

// Only allow admins
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>";
    exit;
}

// Handle delete product
if (isset($_GET['delete'])) {
    $delID = (int) $_GET['delete'];
    $res = delete_product($delID); // make sure you have this function in your db/functions file
    if ($res['ok']) {
        header("Location: adminPage.php?deleted=1");
        exit;
    } else {
        $message = "Error deleting product: " . $res['error'];
    }
}

// Handle Add product form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $data = [
        'product_name' => $_POST['product_name'] ?? '',
        'product_category' => $_POST['product_category'] ?? '',
        'product_description' => $_POST['product_description'] ?? '',
        'product_price' => $_POST['product_price'] ?? '',
    ];
    $file = $_FILES['product_image'] ?? null;

    $res = insert_product($data, $file);
    if ($res['ok']) {
        header("Location: adminPage.php?added=1");
        exit;
    } else {
        $message = "Error: " . $res['error'];
    }
}

// Optional redirect message
if (isset($_GET['added'])) {
    $message = "Product added successfully!";
}

// Handle Update product form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {

    $pid = (int)$_POST['product_id'];

    $data = [
        'product_name' => $_POST['product_name'] ?? '',
        'product_category' => $_POST['product_category'] ?? '',
        'product_description' => $_POST['product_description'] ?? '',
        'product_price' => $_POST['product_price'] ?? '',
    ];

    $file = $_FILES['product_image'] ?? null;

    $res = update_product($pid, $data, $file);
    if ($res['ok']) {
        header("Location: adminPage.php?updated=1");
        exit;
    } else {
        $message = "Error: " . $res['error'];
    }
}

if (isset($_GET['updated'])) {
    $message = "Product updated successfully!";
}

if (isset($_GET['deleted'])) {
    $message = "Product deleted successfully!";
}

// Fetch all products
$products = get_products(100);

// Determine if edit mode
$editID = isset($_GET['edit']) ? (int)$_GET['edit'] : null;

$editProduct = null;
if ($editID) {
    foreach ($products as $p) {
        if ($p['product_id'] == $editID) {
            $editProduct = $p;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

<?php include '../components/design.php'; ?>

<div class="flex flex-col items-center justify-center px-4 py-6 space-y-6 w-full max-w-5xl">

    <div class="bg-white shadow-lg rounded-xl p-8 w-full text-center">
        <h1 class="text-3xl font-bold text-purple-700 mb-4">
            Welcome Admin 
        </h1>

        <?php if($message): ?>
            <p class="mb-4 text-green-600 font-semibold"><?= $message ?></p>
        <?php endif; ?>

        <a href="adminPage.php?add=1"
           class="bg-purple-700 text-white font-semibold px-6 py-2 rounded-lg hover:bg-purple-500 transition-colors">
            Add Item
        </a>

        <a href="logout.php"
           class="bg-gray-700 text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-500 transition-colors ml-4">
            Logout
        </a>
    </div>

    <!-- IF ADD MODE -->
    <?php if(isset($_GET['add'])): ?>
        <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-lg">
            <h2 class="text-2xl font-bold text-purple-700 mb-4">Add Product</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="add_product" value="1">

                <input type="text" name="product_name" placeholder="Product Name" required
                       class="w-full mb-2 p-2 border rounded">

                <select name="product_category" required class="w-full mb-2 p-2 border rounded">
                    <option value="">--Select Category--</option>
                    <option value="Mouse">Mouse</option>
                    <option value="Headset">Headset</option>
                    <option value="Keyboard">Keyboard</option>
                    <option value="Monitor">Monitor</option>
                    <option value="Chair">Chair</option>
                </select>

                <textarea name="product_description" placeholder="Description"
                          class="w-full mb-2 p-2 border rounded"></textarea>

                <input type="number" step="0.01" name="product_price" placeholder="Price" required
                       class="w-full mb-2 p-2 border rounded">

                <input type="file" name="product_image" class="mb-2 w-full">

                <button type="submit"
                        class="bg-purple-700 text-white px-4 py-2 rounded hover:bg-purple-500">
                    Save
                </button>

                <a href="adminPage.php" class="px-4 py-2 rounded bg-gray-300 ml-2">Cancel</a>
            </form>
        </div>
    <?php endif; ?>

    <!-- IF EDIT MODE -->
    <?php if($editProduct): ?>
        <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-lg">
            <h2 class="text-2xl font-bold text-purple-700 mb-4">Edit Product</h2>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="update_product" value="1">
                <input type="hidden" name="product_id" value="<?= $editProduct['product_id'] ?>">

                <input type="text" name="product_name" value="<?= htmlspecialchars($editProduct['product_name']) ?>" required
                       class="w-full mb-2 p-2 border rounded">

                <select name="product_category" class="w-full mb-2 p-2 border rounded">
                    <option value="<?= $editProduct['product_category'] ?>"><?= $editProduct['product_category'] ?></option>
                </select>

                <textarea name="product_description"
                          class="w-full mb-2 p-2 border rounded"><?= htmlspecialchars($editProduct['product_description']) ?></textarea>

                <input type="number" step="0.01" name="product_price"
                       value="<?= htmlspecialchars($editProduct['product_price']) ?>"
                       class="w-full mb-2 p-2 border rounded">

                <label class="text-sm mb-1 block">Change Image:</label>
                <input type="file" name="product_image" class="mb-2 w-full">

                <button type="submit"
                        class="bg-purple-700 text-white px-4 py-2 rounded hover:bg-purple-500">
                    Update
                </button>

                <a href="adminPage.php" class="px-4 py-2 rounded bg-gray-300 ml-2">Cancel</a>
            </form>
        </div>
    <?php endif; ?>

    <!-- LIST PRODUCTS -->
    <div class="w-full space-y-2">
        <?php foreach($products as $prod): ?>
            <div class="bg-white shadow rounded-lg flex items-center p-4 space-x-4">

                <div class="w-24 h-24 bg-gray-200 flex items-center justify-center rounded-lg overflow-hidden">
                    <?php if(!empty($prod['product_image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($prod['product_image']) ?>"
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-gray-500 text-sm">No Image</span>
                    <?php endif; ?>
                </div>

                <div class="flex-1">
                    <h2 class="text-lg font-bold"><?= htmlspecialchars($prod['product_name']) ?></h2>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($prod['product_category']) ?></p>
                    <p class="text-gray-700 text-sm"><?= htmlspecialchars($prod['product_description']) ?></p>
                </div>

                <div class="text-right font-semibold text-purple-700">
                    $<?= number_format($prod['product_price'], 2) ?>
                </div>

                <div class="flex flex-col gap-1">
                    <a href="adminPage.php?edit=<?= $prod['product_id'] ?>"
                       class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-500 text-center">
                        Edit
                    </a>

                    <a href="adminPage.php?delete=<?= $prod['product_id'] ?>" 
                       onclick="return confirm('Are you sure you want to delete this product?');"
                       class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-500 text-center">
                        Delete
                    </a>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>

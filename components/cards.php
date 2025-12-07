<?php include '../db.php'; ?>

<?php 
$products = get_products(); 
?>

<div class="flex flex-wrap justify-center p-6">
<?php foreach ($products as $product): ?>
<div class="bg-[#D6C4FF] w-[300px] h-[350px] rounded-md shadow-lg overflow-hidden flex flex-col m-3">

  <!-- Image Section -->
  <div class="bg-[#8A2BE2] h-[150px] flex items-center justify-center overflow-hidden">
    <?php if (!empty($product['product_image'])): ?>
      <img 
        src="data:image/jpeg;base64,<?= base64_encode($product['product_image']) ?>"
        alt="Product Image"
        class="w-full h-full object-cover"
      >
    <?php else: ?>
      <p class="text-white font-bold text-center">No Image</p>
    <?php endif; ?>
  </div>

  <!-- Content Section -->
  <div class="flex-1 p-4 flex flex-col justify-between">

    <div>
      <h1 class="text-lg font-bold mb-2"><?= htmlspecialchars($product['product_name']) ?></h1>
      <h3 class="text-md mb-2">Category: <?= htmlspecialchars($product['product_category']) ?></h3>
      <p class="text-sm text-gray-700 mb-2"><?= htmlspecialchars($product['product_description']) ?></p>
      <p class="text-md font-semibold mb-4">$<?= number_format($product['product_price'], 2) ?></p>
    </div>

    <!-- Buttons -->
    <div class="flex gap-2">

      <form method="POST" action="../views/addToCart.php" class="flex-1">
        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
        <button type="submit"
          class="w-full bg-[#44BCFF] text-white font-bold py-2 rounded hover:bg-[#3399cc] transition">
          Add to Cart
        </button>
      </form>

      <button
        class="flex-1 bg-[#FF675E] text-white font-bold py-2 rounded hover:bg-[#e65550] transition">
        Buy Now
      </button>

    </div>

  </div>

</div>
<?php endforeach; ?>
</div>

<?php include '../components/design.php'; ?>
<?php include '../db.php'; ?>

<?php 
$products = get_products(); // You already have this
?>

<?php foreach ($products as $product): ?>
<div class="bg-[#D6C4FF] w-[300px] h-[350px] rounded-md shadow-lg overflow-hidden flex flex-col m-3">

  <!-- Image -->
  <div class="bg-[#8A2BE2] h-[150px] flex items-center justify-center">
    <img src="../uploads/<?= $product['image'] ?>" alt="" class="w-full h-full object-cover">
  </div>

  <!-- Content -->
  <div class="flex-1 p-4 flex flex-col justify-between">

    <div>
      <h1 class="text-lg font-bold mb-2"><?= $product['product_name'] ?></h1>
      <h3 class="text-md mb-2">Category: <?= $product['category'] ?></h3>
      <p class="text-sm text-gray-700 mb-2"><?= $product['description'] ?></p>
      <p class="text-md font-semibold mb-4">$<?= $product['price'] ?></p>
    </div>

    <div class="flex gap-2">

      <form method="POST" action="../views/addToCart.php" class="flex-1">
        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
        <button type="submit"
            class="w-full bg-[#44BCFF] text-white font-bold py-2 rounded hover:bg-[#3399cc] transition">
          Add to Cart
        </button>
      </form>

      <button class="flex-1 bg-[#FF675E] text-white font-bold py-2 rounded hover:bg-[#e65550] transition">
        Buy Now
      </button>
    </div>

  </div>
</div>
<?php endforeach; ?>

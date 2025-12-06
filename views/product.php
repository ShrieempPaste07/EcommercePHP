<?php
session_start();
// Example product data

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($selectedCategory); ?></title>
    <link rel="stylesheet" href="../style.css">
   
</head>
<body class="bg-[#F8F7FC]">
    <?php include '../components/design.php'; ?>
    <?php include '../components/header.php'; ?>
    <div class="bg-[#D6C4FF] h-28 flex items-center justify-center gap-4 px-4 shadow-md">
  <button class="bg-white text-[#8A2BE2] font-semibold py-2 px-4 rounded-lg shadow hover:bg-[#8A2BE2] hover:text-white transition">
    Mouse
  </button>
  <button class="bg-white text-[#8A2BE2] font-semibold py-2 px-4 rounded-lg shadow hover:bg-[#8A2BE2] hover:text-white transition">
    Keyboard
  </button>
  <button class="bg-white text-[#8A2BE2] font-semibold py-2 px-4 rounded-lg shadow hover:bg-[#8A2BE2] hover:text-white transition">
    Chairs
  </button>
  <button class="bg-white text-[#8A2BE2] font-semibold py-2 px-4 rounded-lg shadow hover:bg-[#8A2BE2] hover:text-white transition">
    Headset
  </button>
  <button class="bg-white text-[#8A2BE2] font-semibold py-2 px-4 rounded-lg shadow hover:bg-[#8A2BE2] hover:text-white transition">
    Mousepads
  </button>
</div>

<?php include '../components/cards.php'; ?>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Contact</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../components/design.php'; ?>
        <?php include '../components/header.php'; ?>
   <div class="min-h-screen flex flex-col justify-center items-center bg-[#F8F7FC] px-4">

    <section class="w-full max-w-3xl bg-white p-10 rounded-2xl shadow-2xl border border-gray-200">
        <h2 class="text-3xl font-bold text-[#1A1A1A] mb-6 text-center">
            Contact Us
        </h2>

        <p class="text-gray-700 text-lg mb-4">
            We'd love to hear from you! Reach out to us using the following information:
        </p>

        <div class="space-y-4">
            <div class="flex items-start">
                <div class="h-12 w-12 bg-[#B266FF] rounded-md flex items-center justify-center mr-4 text-white font-bold text-lg">
                    📧
                </div>
                <p class="text-gray-800 text-base">
                    Email: <a href="mailto:contact@gamehaven.com" class="text-[#6A0DAD] hover:underline">contact@gamehaven.com</a>
                </p>
            </div>

            <div class="flex items-start">
                <div class="h-12 w-12 bg-[#B266FF] rounded-md flex items-center justify-center mr-4 text-white font-bold text-lg">
                    📞
                </div>
                <p class="text-gray-800 text-base">
                    Phone: <a href="tel:+1234567890" class="text-[#6A0DAD] hover:underline">+1 234 567 890</a>
                </p>
            </div>

            <div class="flex items-start">
                <div class="h-12 w-12 bg-[#B266FF] rounded-md flex items-center justify-center mr-4 text-white font-bold text-lg">
                    📍
                </div>
                <p class="text-gray-800 text-base">
                    Address: B1 L1 Rafael St. Ricablanca Subdivision, Bigas IV, Abalon City, Cavite 
                </p>
            </div>
        </div>

        <p class="text-gray-600 mt-6 text-center">
            We are always here to assist you. Feel free to reach out anytime!
        </p>
    </section>

</div>


<?php include '../components/footer.php'; ?>

</body>
</html>
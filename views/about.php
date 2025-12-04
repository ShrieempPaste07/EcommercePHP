<!DOCTYPE html>
<html>
<head>
    <title>About</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../components/design.php'; ?>
    <div class="min-h-screen overflow-hidden relative">
    <?php include '../components/header.php'; ?>

     <section class="bg-[#F8F7FC] py-12 lg:min-h-screen lg:flex lg:items-center relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-20">
            <div class="lg:text-center mb-12">
                <h2 class="font-heading mb-4 bg-[#D6C4FF] px-4 py-2 rounded-lg md:w-64 md:mx-auto text-xs font-semibold tracking-widest text-[#1A1A1A] uppercase Description">
                    Why choose us?
                </h2>
                <p class="font-heading mt-2 text-3xl leading-8 font-semibold tracking-tight text-[#1A1A1A] sm:text-4xl Header">
                    We Know Tech, We Know Gaming. We are
                    <span class="Title text-[#6A0DAD]">GameHaven</span>.
                </p>
                <p class="mt-4 max-w-2xl text-lg text-[#555555] lg:mx-auto Description">
                    Our store ensures that your PC will be upgraded for every purchase
                    of Computer Accessories. Your PC's best Companion.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 lg:gap-14">
                <!-- Feature 1 -->
                <div class="bg-white p-6 rounded-xl border border-[#E3E3E3] shadow">
                    <div class="flex items-center mb-4">
                        <div class="h-14 w-14 bg-[#B266FF] rounded-md flex items-center justify-center mr-4"></div>
                        <p class="text-xl font-bold text-[#1A1A1A] font-heading Header">Fast Delivery</p>
                    </div>
                    <p class="mt-2 text-base text-gray-500 Description">
                        Get your orders quickly and reliably. We ensure that all your orders arrive fast, safely, and on time.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-6 rounded-xl border border-[#E3E3E3] shadow">
                    <div class="flex items-center mb-4">
                        <div class="h-14 w-14 bg-[#B266FF] rounded-md flex items-center justify-center mr-4"></div>
                        <p class="text-xl font-bold text-[#1A1A1A] font-heading Header">Best Quality</p>
                    </div>
                    <p class="mt-2 text-base text-gray-500 Description">
                        Premium products for every gamer. All our products go through strict quality checks to ensure your PC gets the best upgrades possible.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-6 rounded-xl border border-[#E3E3E3] shadow">
                    <div class="flex items-center mb-4">
                        <div class="h-14 w-14 bg-[#B266FF] rounded-md flex items-center justify-center mr-4"></div>
                        <p class="text-xl font-bold text-[#1A1A1A] font-heading Header">24/7 Support</p>
                    </div>
                    <p class="mt-2 text-base text-gray-500 Description">
                        We are always here to help. Our support team is available around the clock to assist you with any questions or issues.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-6 rounded-xl border border-[#E3E3E3] shadow">
                    <div class="flex items-center mb-4">
                        <div class="h-14 w-14 bg-[#B266FF] rounded-md flex items-center justify-center mr-4"></div>
                        <p class="text-xl font-bold text-[#1A1A1A] font-heading Header">Affordable Prices</p>
                    </div>
                    <p class="mt-2 text-base text-gray-500 Description">
                        Get the best value for your money. We offer competitive prices without compromising quality or performance.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php include '../components/footer.php'; ?>
</div>

</body>
</html>
<?php
session_start();
include '../db.php'; // your existing DB connection

// require login
if (!isset($_SESSION['user_id'])) {
    header('Location: userLogin.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// fetch user
$sql = "SELECT first_name, last_name, phone_number, birth_date, username, user_image FROM users WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;
$stmt->close();

// if no user found, log out / redirect
if (!$user) {
    session_destroy();
    header('Location: userLogin.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
    <?php include '../components/design.php'; ?>

    <div class="min-h-screen bg-[#F8F7FC] p-6 flex flex-col justify-center items-center">

    <h1 class="text-3xl font-bold mb-8 text-[#1A1A1A] Header">Profile</h1>

    <div class="bg-white p-8 rounded shadow w-full max-w-lg flex flex-col items-center">

        <!-- USER IMAGE -->
        <?php if (!empty($user['user_image'])): ?>
            <?php
            $imgPath = '../uploads/' . $user['user_image'];
            $showImg = file_exists($imgPath);
            ?>
            <?php if ($showImg): ?>
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="Profile" class="w-40 h-40 rounded-md object-cover border mb-6" />
            <?php else: ?>
                <?php if (strlen($user['user_image']) > 50): ?>
                    <div class="w-40 h-40 rounded-md overflow-hidden border mb-6">
                        <img src="data:image/jpeg;base64,<?= base64_encode($user['user_image']) ?>" class="w-full h-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="w-40 h-40 bg-[#8A2BE2] rounded-md flex items-center justify-center text-white font-bold mb-6">
                        NO IMAGE
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="w-40 h-40 bg-[#8A2BE2] rounded-md flex items-center justify-center text-white font-bold mb-6">
                NO IMAGE
            </div>
        <?php endif; ?>

        <!-- USER DETAILS -->
        <div class="text-center space-y-3 text-lg">
            <p><span class="font-bold">Username:</span> <?= htmlspecialchars($user['username']) ?></p>
            <p><span class="font-bold">First name:</span> <?= htmlspecialchars($user['first_name']) ?></p>
            <p><span class="font-bold">Last name:</span> <?= htmlspecialchars($user['last_name']) ?></p>
            <p><span class="font-bold">Phone:</span> <?= htmlspecialchars($user['phone_number']) ?></p>
            <p><span class="font-bold">Birthdate:</span> <?= htmlspecialchars($user['birth_date']) ?></p>
        </div>

        <div class="mt-4 flex space-x-4">
    <a href="editProfile.php"
       class="bg-[#8A2BE2] text-white px-6 py-2 rounded hover:bg-[#a86aff]">
       Edit Profile
    </a>

 
    <a href="logout.php" 
       class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
       Logout
    </a>


</div>

    </div>

</div>


    <?php include '../components/footer.php'; ?>
</body>
</html>

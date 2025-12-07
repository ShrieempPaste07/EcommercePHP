<?php
session_start();
require '../db.php'; // your database connection

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT username, first_name, last_name, phone_number, birth_date, user_image FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $phone      = trim($_POST['phone']);
    $birthdate  = trim($_POST['birth_date']);

    // Optional: validate birthdate format
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $birthdate)) {
        echo "<script>alert('Invalid birthdate format. Use YYYY-MM-DD'); window.location.href='editprofile.php';</script>";
        exit;
    }

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $imageData = file_get_contents($_FILES['profile_image']['tmp_name']);
    } else {
        $imageData = $user['user_image']; // Keep existing image if no new upload
    }

    // Update user in DB
    $update = $conn->prepare("
        UPDATE users 
        SET username = ?, first_name = ?, last_name = ?, phone_number = ?, birth_date = ?, user_image = ? 
        WHERE user_id = ?
    ");
    $update->bind_param("sssssbi", $username, $first_name, $last_name, $phone, $birthdate, $null, $user_id);
    
    // Bind blob (image)
    $update->send_long_data(5, $imageData);
    $update->execute();
    $update->close();

    $_SESSION['username'] = $username;

    // Redirect to profile.php
    header("Location: profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
    <?php include '../components/design.php'; ?>

    <div class="min-h-screen bg-[#F8F7FC] p-6 flex justify-center">
        <div class="w-full max-w-md">
            <h1 class="text-3xl font-bold mb-6 text-[#1A1A1A]">Edit Profile</h1>
            <form method="post" action="editprofile.php" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow space-y-4">

                <!-- Profile Image -->
                <div class="flex flex-col items-center">
                    <?php if (!empty($user['user_image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($user['user_image']) ?>" 
                             class="w-24 h-24 rounded-full object-cover mb-2" alt="Profile Picture">
                    <?php else: ?>
                        <div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center mb-2">
                            <span class="text-gray-500">No Image</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="profile_image" accept="image/*" class="mt-2">
                </div>

                <label class="block font-semibold text-gray-700">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" 
                       required class="w-full p-2 border rounded" />

                <label class="block font-semibold text-gray-700">First Name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" 
                       required class="w-full p-2 border rounded" />

                <label class="block font-semibold text-gray-700">Last Name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" 
                       required class="w-full p-2 border rounded" />

                <label class="block font-semibold text-gray-700">Phone Number</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone_number']) ?>" 
                       required class="w-full p-2 border rounded" />

                <label class="block font-semibold text-gray-700">Birthdate</label>
                <input type="date" name="birth_date" value="<?= htmlspecialchars($user['birth_date']) ?>" 
                       required class="w-full p-2 border rounded" />

                <button type="submit" class="w-full bg-[#8A2BE2] text-white px-4 py-2 rounded hover:bg-[#B266FF]">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>

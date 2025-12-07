<?php
session_start();
session_unset(); // clear old sessions
include '../db.php';

$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {

        // fetch user by email
        $stmt = $conn->prepare("SELECT user_id, username, user_password, user_type FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $admin = $res->fetch_assoc();
        $stmt->close();

        if ($admin && $admin['user_type'] === 'admin' && password_verify($password, $admin['user_password'])) {
            // login success
            $_SESSION['user_id'] = $admin['user_id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['is_admin'] = true;

            header('Location: adminPage.php');
            exit;
        } else {
            $errorMessage = "Invalid admin credentials!";
        }

    } else {
        $errorMessage = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="relative w-full h-full overflow-hidden">

    <video autoplay muted loop class="absolute top-0 left-0 w-full h-full object-cover brightness-50 -z-10">
        <source src="../assets/videoLogin.mp4" type="video/mp4">
    </video>

    <div class="relative z-10">
        <?php include '../components/design.php'; ?>

        <div class="min-h-screen p-6 flex flex-col items-center justify-center">
            <h1 class="text-3xl font-bold mb-4 text-white Header">Admin Login</h1>

            <form method="post" class="bg-white/80 backdrop-blur-sm p-4 rounded shadow w-[350px] text-black">

                <?php if (!empty($errorMessage)): ?>
                    <p class="text-red-700 font-bold text-center mb-3"><?= $errorMessage ?></p>
                <?php endif; ?>

                <input type="email" name="email" placeholder="Email" required class="w-full mb-2 p-2 border rounded" />   
                <input type="password" name="password" placeholder="Password" required class="w-full mb-2 p-2 border rounded" />

                <button type="submit" class="bg-[#8A2BE2] text-white px-4 py-2 rounded hover:bg-[#B266FF] w-full">
                    Login as Admin
                </button>

                <p class="text-center mt-4">
                    <a href="./userLogin.php" class="underline">Login as User</a>
                </p>
            </form>
        </div>
    </div>

</body>
</html>

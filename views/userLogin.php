<?php
session_start();
include '../db.php'; // connect to DB

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {

        $user_id = validate_user($username, $password); 

        if ($user_id) {
            $_SESSION['user_id'] = $user_id; // store session
            $_SESSION['username'] = $username;
            header('Location: ../views/hero.php'); // redirect to landing page
            exit;
        } else {
            $errorMessage = "Invalid username or password!";
        }

    } else {
        $errorMessage = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="relative w-full h-full overflow-hidden">

<!-- Background Video -->
<video autoplay muted loop class="absolute top-0 left-0 w-full h-full object-cover brightness-50 -z-10">
    <source src="../assets/videoLogin.mp4" type="video/mp4">
</video>

<div class="relative z-10">

    <?php include '../components/design.php'; ?>
    <?php include '../components/header.php'; ?>

    <div class="min-h-screen p-6 flex flex-col items-center justify-center">
        <h1 class="text-3xl font-bold mb-4 text-white Header">Login</h1>

        <form method="post"
            class="bg-white/80 backdrop-blur-sm p-4 rounded shadow w-[350px] text-black">

            <?php if(isset($errorMessage)): ?>
                <p class="text-red-700 text-center font-bold mb-2"><?= $errorMessage ?></p>
            <?php endif; ?>

            <input type="text" name="username" placeholder="Username" required
                class="w-full mb-2 p-2 border rounded"/>

            <input type="password" name="password" placeholder="Password" required
                class="w-full mb-2 p-2 border rounded"/>

            <button type="submit"
                class="bg-[#8A2BE2] text-white px-4 py-2 rounded hover:bg-[#B266FF] w-full">
                Login
            </button>

            <p class="text-center mt-4">
                <a href="./register.php" class="underline">Register as User</a>
            </p>

            <p class="text-center mt-2">
                <a href="./adminLogin.php" class="underline">Login as Admin</a>
            </p>
        </form>
    </div>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>

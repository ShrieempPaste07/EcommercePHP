<?php
include '../db.php'; // connect to DB

// when form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!empty($email) && !empty($username) && !empty($password) && !empty($confirm)) {

        if ($password !== $confirm) {
            $errorMessage = "Passwords do not match!";
        } else {

            // Check if username or email already exists
            $sql = "SELECT COUNT(*) AS count FROM users WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($res['count'] > 0) {
                $errorMessage = "Username or Email already exists!";
            } else {
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);

                // Required fields must have something valid
                $data = [
                    'first_name'     => '',
                    'last_name'      => '',
                    'email'          => $email,
                    'phone_number'   => 0,
                    'birth_date'     => '2000-01-01',
                    'address'        => '',
                    'username'       => $username,
                    'user_password'  => $hashedPass,
                    'user_type'      => 'user'
                ];

                $result = insert_user($data, null);

                if ($result['ok']) {
                    // Redirect to hero page after registration
                    $_SESSION['user_id'] = $result['id'];
                    $_SESSION['username'] = $username;
                    header('Location: ../views/hero.php');
                    exit;
                } else {
                    $errorMessage = $result['error'];
                }
            }
        }

    } else {
        $errorMessage = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="relative w-full h-full overflow-hidden">

<!-- Background Video -->
<video autoplay muted loop class="absolute top-0 left-0 w-full h-full object-cover brightness-50 -z-10">
    <source src="../assets/videoLogin.mp4" type="video/mp4">
</video>

<div class="relative z-10">

    <?php include '../components/design.php'; ?>

    <div class="min-h-screen p-6 flex flex-col items-center justify-center">
        <h1 class="text-3xl font-bold mb-4 text-white Header">Register</h1>

        <form method="post"
            class="bg-white/80 backdrop-blur-sm p-4 rounded shadow w-[350px] text-black">

            <?php if(isset($errorMessage)): ?>
                <p class="text-red-700 text-center font-bold mb-2"><?= $errorMessage ?></p>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Email" required
                class="w-full mb-2 p-2 border rounded"/>

            <input type="text" name="username" placeholder="Username" required
                class="w-full mb-2 p-2 border rounded"/>

            <input type="password" name="password" placeholder="Password" required
                class="w-full mb-2 p-2 border rounded"/>

            <input type="password" name="confirm_password" placeholder="Confirm Password" required
                class="w-full mb-2 p-2 border rounded"/>

            <button type="submit"
                class="bg-[#8A2BE2] text-white px-4 py-2 rounded hover:bg-[#B266FF] w-full">
                Register
            </button>

            <p class="text-center mt-4"><a href="./userLogin.php" class="underline">Login as User</a></p>
        </form>
    </div>
</div>

</body>
</html>

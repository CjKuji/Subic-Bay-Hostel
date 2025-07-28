<?php
session_start();
include('../includes/db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            // ✅ Set session variables with consistent keys
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'] ?? 'admin@example.com';
            $_SESSION['admin_profile'] = $admin['profile_image'] ?: 'assets/images/default-profile.png';

            header("Location: rooms.php");
            exit;
        }
    }

    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="flex flex-col md:flex-row w-[90%] max-w-5xl h-[70vh] rounded-2xl overflow-hidden shadow-2xl">

        <!-- Left Side -->
        <div class="bg-white flex flex-col items-center justify-center px-8 py-6 md:w-1/2 text-center">
            <h1 class="text-5xl font-bold text-black mb-2 tracking-tight">
                Subic Bay <span class="text-[#D8322E]">Hostel <span class="text-black">&</span> Dormitory</span>
            </h1>
            <p class="text-sm text-gray-600 max-w-xs mb-6">
                The first and only Japanese capsule hotel in Subic Bay!
            </p>
        </div>

        <!-- Right Side: Login Form -->
        <div class="bg-black flex flex-col justify-center px-8 py-6 md:w-1/2">
            
            <!-- Greeting -->
            <div class="mb-8 text-center">
                <h2 class="text-white text-5xl font-extrabold leading-tight tracking-tight">
                    <span class="text-[#D8322E]">Welcome</span> Back!
                </h2>
                <h3 class="text-white mt-1 text-lg font-medium">Madam Jean</h3>
            </div>  

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="text-red-500 font-medium text-sm text-center py-2 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <div class="w-full max-w-sm mx-auto">
                <form method="POST" class="space-y-5">
                    <input type="text" name="username" placeholder="Username" required
                           class="w-full px-4 py-2 rounded-lg bg-white text-black placeholder-gray-500
                                  focus:ring-2 focus:ring-red-600 focus:outline-none transition">

                    <input type="password" name="password" placeholder="Password" required
                           class="w-full px-4 py-2 rounded-lg bg-white text-black placeholder-gray-500
                                  focus:ring-2 focus:ring-red-600 focus:outline-none transition">

                    <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition">
                        Login
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>
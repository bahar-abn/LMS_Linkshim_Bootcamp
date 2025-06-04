<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/projects/bootcamp_course_site/public');
}

$error = $_SESSION['register_error'] ?? null;
$success = $_SESSION['register_success'] ?? null;

unset($_SESSION['register_error'], $_SESSION['register_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="w-full max-w-md bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-center">Register</h1>

    <?php if ($success): ?>
        <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mb-4 p-3 bg-red-200 text-red-800 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/register" method="post" class="space-y-4">
        <div>
            <label for="name" class="block mb-1 font-medium">Full Name</label>
            <input type="text" name="name" id="name" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
        </div>

        <div>
            <label for="email" class="block mb-1 font-medium">Email</label>
            <input type="email" name="email" id="email" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
        </div>

        <div>
            <label for="password" class="block mb-1 font-medium">Password</label>
            <input type="password" name="password" id="password" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Register
        </button>
    </form>

    <p class="mt-4 text-center text-gray-600">
        Already have an account?
        <a href="<?= BASE_URL ?>/login" class="text-blue-600 hover:underline">Login here</a>
    </p>
</div>
</body>
</html>

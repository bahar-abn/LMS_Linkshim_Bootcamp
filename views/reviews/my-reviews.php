<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/lms-php-mvc/public');
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Course Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="w-full max-w-5xl bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-purple-700 mb-4">📝 My Course Reviews</h1>

    <?php if (empty($reviews)): ?>
        <div class="text-gray-600 text-lg">You don't have any reviews yet.</div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($reviews as $review): ?>
                <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-xl font-semibold text-purple-800"><?= htmlspecialchars($review['course_title']) ?></h2>
                        <span class="text-sm text-gray-500"><?= htmlspecialchars($review['created_at']) ?></span>
                    </div>
                    <div class="text-sm text-gray-700">
                        <span class="font-medium">Rating:</span> <?= htmlspecialchars($review['rating']) ?>/5
                    </div>
                    <p class="mt-2 text-gray-800">
                        <?= nl2br(htmlspecialchars($review['comment'])) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-8">
        <a href="<?= BASE_URL ?>/dashboard" class="inline-block bg-purple-600 text-white px-5 py-2 rounded shadow hover:bg-purple-700 transition">
            ← Back to Dashboard
        </a>
    </div>
</div>
</body>
</html>

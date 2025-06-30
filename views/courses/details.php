<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) define('BASE_URL', '/lms-php-mvc/public');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($course->title) ? htmlspecialchars($course->title) : 'Course Details' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($course) && isset($course->id)): ?>
        <h1 class="text-3xl font-bold text-blue-700 mb-4"><?= htmlspecialchars($course->title) ?></h1>
        <p class="text-gray-800 mb-4 whitespace-pre-line"><?= nl2br(htmlspecialchars($course->description)) ?></p>

        <p class="text-sm text-gray-600 mb-6">
            <strong>Status:</strong> <span class="font-semibold"><?= htmlspecialchars($course->status) ?></span>
        </p>

        <?php if (($_SESSION['user']['role'] ?? '') === 'student'): ?>
            <form action="<?= BASE_URL ?>/courses/<?= urlencode($course->id) ?>/enroll" method="post">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Enroll in this Course
                </button>
            </form>
        <?php endif; ?>

        <!-- Reviews Section -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-2">Reviews</h2>

            <?php foreach ($reviews ?? [] as $review): ?>
                <div class="bg-gray-50 p-3 rounded mb-2">
                    <p class="text-sm text-gray-700"><?= htmlspecialchars($review->comment) ?></p>
                    <div class="text-sm text-yellow-600 font-semibold mt-1">
                        ⭐ Rating: <?= htmlspecialchars($review->rating) ?>/5
                    </div>
                    <span class="text-xs text-gray-500">
                        By <?= htmlspecialchars($review->user_name ?? 'Student') ?>
                        on <?= date('Y-m-d', strtotime($review->created_at)) ?>
                    </span>
                </div>
            <?php endforeach; ?>

            <?php if (($_SESSION['user']['role'] ?? '') === 'student'): ?>
                <form method="post" action="<?= BASE_URL ?>/reviews/add" class="mt-6 space-y-4">
                    <input type="hidden" name="course_id" value="<?= htmlspecialchars($course->id) ?>">

                    <div>
                        <label for="comment" class="block text-gray-700 font-medium mb-1">Your Review:</label>
                        <textarea name="comment" id="comment" rows="3" required class="w-full border rounded p-2" placeholder="Write your review..."></textarea>
                    </div>

                    <div>
                        <label for="rating" class="block text-gray-700 font-medium mb-1">Rating (1–5):</label>
                        <input type="number" name="rating" id="rating" min="1" max="5" required class="w-full border rounded p-2">
                    </div>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Submit Review
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="text-red-600 font-semibold">Course not found or invalid.</p>
    <?php endif; ?>
</div>
</body>
</html>

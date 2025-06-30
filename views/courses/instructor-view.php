<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) define('BASE_URL', '/lms-php-mvc/public');

// Initialize variables with default values if not set
$course = $course ?? null;
$reviews = $reviews ?? [];
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
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-700"><?= htmlspecialchars($course->title) ?></h1>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="font-medium">Status:</span>
                    <span class="font-semibold <?=
                    $course->status === 'approved' ? 'text-green-600' :
                        ($course->status === 'pending' ? 'text-yellow-600' : 'text-red-600')
                    ?>">
                        <?= htmlspecialchars($course->status) ?>
                    </span>
                </p>
            </div>

            <?php if (($_SESSION['user']['role'] ?? '') === 'instructor' && ($_SESSION['user']['id'] ?? null) === ($course->instructor_id ?? null)): ?>
                <div class="flex gap-2">
                    <a href="<?= BASE_URL ?>/courses/<?= $course->id ?>/edit"
                       class="text-sm bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                        Edit Course
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="prose max-w-none">
            <p class="text-gray-800 mb-6 whitespace-pre-line"><?= nl2br(htmlspecialchars($course->description)) ?></p>
        </div>

        <?php if (($_SESSION['user']['role'] ?? '') === 'student' && ($course->status === 'approved')): ?>
            <form action="<?= BASE_URL ?>/courses/<?= urlencode($course->id) ?>/enroll" method="post" class="mb-6">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Enroll in this Course
                </button>
            </form>
        <?php endif; ?>

        <!-- Reviews Section -->
        <div class="mt-8 border-t pt-6">
            <h2 class="text-xl font-bold mb-4">Reviews</h2>

            <?php if (!empty($reviews)): ?>
                <div class="space-y-4">
                    <?php foreach ($reviews as $review): ?>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium text-gray-800">
                                    <?= htmlspecialchars($review->user_name ?? 'Student') ?>
                                </span>
                                <div class="flex items-center">
                                    <span class="text-yellow-500 mr-1">★</span>
                                    <span class="font-semibold"><?= htmlspecialchars($review->rating) ?>/5</span>
                                </div>
                            </div>
                            <p class="text-gray-700"><?= htmlspecialchars($review->comment) ?></p>
                            <div class="text-xs text-gray-500 mt-2">
                                <?= date('F j, Y', strtotime($review->created_at)) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No reviews yet.</p>
            <?php endif; ?>

            <?php if (($_SESSION['user']['role'] ?? '') === 'student' && ($isEnrolled ?? false)): ?>
                <div class="mt-8 bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Leave a Review</h3>
                    <form method="post" action="<?= BASE_URL ?>/reviews/add" class="space-y-4">
                        <input type="hidden" name="course_id" value="<?= htmlspecialchars($course->id) ?>">

                        <div>
                            <label for="rating" class="block text-gray-700 font-medium mb-1">Rating</label>
                            <select name="rating" id="rating" required class="w-full border rounded p-2">
                                <option value="">Select rating</option>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Very Good</option>
                                <option value="3">3 - Good</option>
                                <option value="2">2 - Fair</option>
                                <option value="1">1 - Poor</option>
                            </select>
                        </div>

                        <div>
                            <label for="comment" class="block text-gray-700 font-medium mb-1">Your Review</label>
                            <textarea name="comment" id="comment" rows="4" required
                                      class="w-full border rounded p-2" placeholder="Share your experience..."></textarea>
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Submit Review
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <p class="font-semibold">Course not found or invalid.</p>
            <a href="<?= BASE_URL ?>/courses" class="text-blue-600 hover:underline mt-2 inline-block">
                ← Back to Courses
            </a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
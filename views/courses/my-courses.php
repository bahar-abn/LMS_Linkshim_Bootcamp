<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>My Enrolled Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto flex gap-6">
    <!-- Course List Sidebar -->
    <div class="w-1/3 bg-white p-4 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">My Enrolled Courses</h1>

        <?php if (!empty($courses)): ?>
            <ul class="space-y-2">
                <?php foreach ($courses as $course): ?>
                    <li class="p-3 border rounded hover:bg-gray-50 <?= ($selectedCourse && $selectedCourse->id == $course->id) ? 'bg-blue-50 border-blue-200' : '' ?>">
                        <a href="<?= BASE_URL ?>/my-courses/<?= $course->id ?>" class="block">
                            <h2 class="text-lg font-semibold"><?= htmlspecialchars($course->title ?? '') ?></h2>
                            <p class="text-sm text-gray-600 truncate"><?= htmlspecialchars($course->description ?? '') ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600">You have not enrolled in any courses yet.</p>
        <?php endif; ?>
    </div>

    <!-- Course Details Area -->
    <div class="flex-1 bg-white p-6 rounded shadow">
        <?php if ($selectedCourse): ?>
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-blue-700 mb-2"><?= htmlspecialchars($selectedCourse->title) ?></h1>
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Status:</span>
                    <span class="font-semibold <?=
                    $selectedCourse->status === 'approved' ? 'text-green-600' :
                        ($selectedCourse->status === 'pending' ? 'text-yellow-600' : 'text-red-600')
                    ?>">
                        <?= htmlspecialchars($selectedCourse->status) ?>
                    </span>
                </p>
            </div>

            <div class="prose max-w-none mb-6">
                <p class="text-gray-800 whitespace-pre-line"><?= nl2br(htmlspecialchars($selectedCourse->description)) ?></p>
            </div>

            <!-- Reviews Section -->
            <div class="mt-8 border-t pt-6">
                <h2 class="text-xl font-bold text-blue-700 mb-4">Course Reviews</h2>

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
                    <p class="text-gray-600">No reviews yet for this course.</p>
                <?php endif; ?>

                <!-- <div class="mt-8 bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-700 mb-3">Write a Review</h3>
                    <form method="post" action="<?= BASE_URL ?>/reviews/add" class="space-y-4">
                        <input type="hidden" name="course_id" value="<?= htmlspecialchars($selectedCourse->id) ?>">

                        <div>
                            <label for="rating" class="block text-gray-700 font-medium mb-1">Your Rating</label>
                            <select name="rating" id="rating" required class="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select your rating</option>
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
                                      class="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Share your experience with this course..."></textarea>
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Submit Review
                        </button>
                    </form>
                </div> -->
            </div>
        <?php else: ?>
            <div class="text-center py-10">
                <h2 class="text-xl text-gray-600">Select a course from the sidebar to view details</h2>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
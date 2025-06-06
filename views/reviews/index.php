<?php /** @var $course \models\Course */ ?>
<?php /** @var $reviews \models\Review[] */ ?>
<?php /** @var $isEnrolled bool */ ?>
<?php /** @var $currentUser \models\User|null */ ?>

<div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-xl">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">Course: <?= htmlspecialchars($course->title) ?></h1>
    <hr class="mb-4">

    <?php if (!$currentUser): ?>
        <p class="text-gray-600">Please <a href="/login" class="text-blue-600 underline">login</a> to enroll or leave a review.</p>

    <?php elseif (!$isEnrolled): ?>
        <form method="post" action="/courses/enroll" class="mb-6">
            <input type="hidden" name="course_id" value="<?= $course->id ?>">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Enroll in this course
            </button>
        </form>

    <?php else: ?>
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-2 text-gray-700">Leave a Review</h2>
            <form method="post" action="/reviews/add" class="space-y-4">
                <input type="hidden" name="course_id" value="<?= $course->id ?>">
                <div>
                    <label class="block text-gray-600">Rating (1-5):</label>
                    <input type="number" name="rating" min="1" max="5" required
                           class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-gray-600">Comment:</label>
                    <textarea name="comment" required rows="4"
                              class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Submit Review
                </button>
            </form>
        </div>
    <?php endif; ?>

    <hr class="my-6">

    <h2 class="text-xl font-semibold mb-4 text-gray-700">Reviews</h2>

    <?php if (empty($reviews)): ?>
        <p class="text-gray-500">No reviews yet.</p>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($reviews as $review): ?>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-yellow-600 font-semibold">⭐ <?= htmlspecialchars($review->rating) ?>/5</span>
                        <span class="text-sm text-gray-400"><?= date('F j, Y', strtotime($review->created_at)) ?></span>
                    </div>
                    <p class="text-gray-700 whitespace-pre-line"><?= nl2br(htmlspecialchars($review->comment)) ?></p>
                    <p class="mt-2 text-sm text-gray-500">By user #<?= htmlspecialchars($review->user_id) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

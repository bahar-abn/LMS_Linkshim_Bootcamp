<?php /** @var $course \models\Course */ ?>
<?php /** @var $reviews \models\Review[] */ ?>
<?php /** @var $isEnrolled bool */ ?>
<?php /** @var $currentUser \models\User|null */ ?>

<h1>Course: <?= htmlspecialchars($course->title) ?></h1>

<hr>


<?php if (!$currentUser): ?>
    <p>Please <a href="/login">login</a> to enroll or leave a review.</p>


<?php elseif (!$isEnrolled): ?>
    <form method="post" action="/courses/enroll">
        <input type="hidden" name="course_id" value="<?= $course->id ?>">
        <button type="submit">Enroll in this course</button>
    </form>


<?php else: ?>
    <h2>Leave a Review</h2>
    <form method="post" action="/reviews/add">
        <input type="hidden" name="course_id" value="<?= $course->id ?>">
        <label>Rating (1-5):</label>
        <input type="number" name="rating" min="1" max="5" required>
        <br>
        <label>Comment:</label><br>
        <textarea name="comment" required></textarea>
        <br>
        <button type="submit">Submit Review</button>
    </form>
<?php endif; ?>

<hr>

<h2>Reviews:</h2>
<?php if (empty($reviews)): ?>
    <p>No reviews yet.</p>
<?php else: ?>
    <?php foreach ($reviews as $review): ?>
        <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
            <strong>Rating:</strong> <?= $review->rating ?>/5<br>
            <strong>Comment:</strong> <?= nl2br(htmlspecialchars($review->comment)) ?><br>
            <small>By user #<?= $review->user_id ?> on <?= $review->created_at ?></small>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

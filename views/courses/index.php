<?php include_once __DIR__ . '/../layout/main.php'; ?>

<h2>دوره های در دسترس</h2>

<?php if (!empty($courses) && is_array($courses)): ?>
    <div class="row">
        <?php foreach ($courses as $course): ?>
            <div class="course-card col-4">
                <h3><?= htmlspecialchars($course['title']) ?></h3>
                <p><?= htmlspecialchars($course['description']) ?></p>
                <a href="/courses/<?= $course['id'] ?>" class="btn btn-primary">Details</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning text-center">
        هیچ دوره‌ای برای نمایش وجود ندارد.
    </div>
<?php endif; ?>

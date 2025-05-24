<?php include_once __DIR__ . '/../layout/main.php'; ?>

<?php if (!empty($course) && is_array($course)): ?>
    <h2><?= htmlspecialchars($course['title']) ?></h2>
    <p><?= htmlspecialchars($course['description']) ?></p>
    <p><strong>Status:</strong> <?= ucfirst($course['status']) ?></p>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'student'): ?>
        <form method="POST" action="/courses/<?= $course['id'] ?>/enroll">
            <button type="submit" class="btn btn-primary">Enroll</button>
        </form>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-danger text-center">
        اطلاعات دوره یافت نشد.
    </div>
<?php endif; ?>

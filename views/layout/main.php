<?php
require_once __DIR__ . '/../../config/config.php';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'سیستم مدیریت آموزش (LMS)' ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/style.css">
</head>
<body>
<header style="background: #fff; padding: 1rem 0; box-shadow: 0 1px 6px rgba(0,0,0,0.05);">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        <a href="/" class="logo" style="font-weight: bold; font-size: 1.4rem; color: var(--primary); text-decoration: none;">LMS</a>
        <nav>
            <ul style="list-style: none; display: flex; gap: 1.5rem; margin: 0; padding: 0;">
                <li><a href="/courses" class="btn btn-secondary">دوره‌ها</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <li><a href="/admin/courses" class="btn btn-secondary">مدیریت دوره‌ها</a></li>
                        <li><a href="/dashboard" class="btn btn-secondary">داشبورد مدیر</a></li>
                    <?php elseif ($_SESSION['user']['role'] === 'instructor'): ?>
                        <li><a href="/courses/create" class="btn btn-secondary">ایجاد دوره</a></li>
                        <li><a href="/dashboard" class="btn btn-secondary">داشبورد مدرس</a></li>
                    <?php else: ?>
                        <li><a href="/dashboard" class="btn btn-secondary">داشبورد دانشجو</a></li>
                    <?php endif; ?>
                    <li><a href="/logout" class="btn btn-danger">خروج</a></li>
                <?php else: ?>
                    <li><a href="/login" class="btn btn-primary">ورود</a></li>
                    <li><a href="/register" class="btn btn-success">ثبت‌نام</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="container mt-4">
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($content)) echo $content; ?>
</main>

<footer class="text-center mt-4 p-4" style="background: #f1f1f1; color: #666; font-size: 0.9rem;">
    © <?= date('Y') ?> LMS - تمامی حقوق محفوظ است.
</footer>
</body>
</html>

<?php include_once __DIR__ . '/../layout/main.php'; ?>

<h2>ایجاد دوره جدید</h2>

<form method="POST" action="/courses/store">
    <label>Title</label>
    <input type="text" name="title" required>

    <label>Description</label>
    <textarea name="description" required></textarea>

    <label>Category</label>
    <select name="category_id">
        <?php if (!empty($categories) && is_array($categories)): ?>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        <?php else: ?>
            <option disabled selected>هیچ دسته‌بندی‌ای در دسترس نیست</option>
        <?php endif; ?>
    </select>

    <button type="submit" class="btn btn-success">Submit Course</button>
</form>
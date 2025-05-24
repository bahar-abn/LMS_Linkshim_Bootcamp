<h1>Student Dashboard</h1>
<h2>Your Enrollments</h2>
<ul>
    <?php foreach ($enrollments as $enrollment): ?>
        <li>Course ID: <?= $enrollment->course_id ?></li>
    <?php endforeach; ?>
</ul>

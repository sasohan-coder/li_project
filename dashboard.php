<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

$counts = [
    'books' => (int) fetch_one('SELECT COUNT(*) AS total FROM books')['total'],
    'students' => (int) fetch_one('SELECT COUNT(*) AS total FROM students')['total'],
    'vendors' => (int) fetch_one('SELECT COUNT(*) AS total FROM vendors')['total'],
    'allotments' => (int) fetch_one('SELECT COUNT(*) AS total FROM allotments')['total'],
];

render_header('Dashboard');
?>
<div class="cards">
    <div class="card"><h3>Total Books</h3><p><?= $counts['books'] ?></p></div>
    <div class="card"><h3>Total Students</h3><p><?= $counts['students'] ?></p></div>
    <div class="card"><h3>Total Vendors</h3><p><?= $counts['vendors'] ?></p></div>
    <div class="card"><h3>Total Allotments</h3><p><?= $counts['allotments'] ?></p></div>
</div>
<?php render_footer(); ?>

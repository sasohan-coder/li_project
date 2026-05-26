<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

if (isset($_GET['delete'])) {
    try {
        $stmt = db()->prepare('DELETE FROM students WHERE email = ?');
        $stmt->execute([$_GET['delete']]);
        set_flash('Student deleted.');
    } catch (PDOException $e) {
        set_flash('Student cannot be deleted while linked to allotments.', 'error');
    }
    redirect_to('student.php');
}

if (is_post()) {
    $stmt = db()->prepare('INSERT INTO students (name, email, phone, registration_date) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['name'] ?? ''),
        trim($_POST['email'] ?? ''),
        trim($_POST['phone'] ?? ''),
        date('Y-m-d'),
    ]);
    set_flash('Student added.');
    redirect_to('student.php');
}

$students = fetch_all('SELECT * FROM students ORDER BY registration_date DESC, name ASC');

render_header('Students');
?>
<h2>Student Management</h2>
<div class="form-card">
    <form class="inline-form" method="post">
        <input name="name" type="text" placeholder="Student name" required>
        <input name="email" type="email" placeholder="Student email" required>
        <input name="phone" type="text" placeholder="Phone number" required>
        <button type="submit">Add Student</button>
    </form>
</div>

<table>
    <tr><th>Name</th><th>Email</th><th>Phone</th><th>Registration Date</th><th>Action</th></tr>
    <?php foreach ($students as $student): ?>
        <tr>
            <td><?= h($student['name']) ?></td>
            <td><?= h($student['email']) ?></td>
            <td><?= h($student['phone']) ?></td>
            <td><?= h($student['registration_date']) ?></td>
            <td><a class="action-link delete-link" href="student.php?delete=<?= urlencode($student['email']) ?>">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php render_footer(); ?>

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
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $stmt = db()->prepare('UPDATE students SET name = ?, phone = ? WHERE email = ?');
        $stmt->execute([
            trim($_POST['name'] ?? ''),
            trim($_POST['phone'] ?? ''),
            trim($_POST['email'] ?? '')
        ]);
        set_flash('Student details updated.');
    } else {
        $stmt = db()->prepare('INSERT INTO students (name, email, phone, registration_date) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            trim($_POST['name'] ?? ''),
            trim($_POST['email'] ?? ''),
            trim($_POST['phone'] ?? ''),
            date('Y-m-d'),
        ]);
        set_flash('Student added.');
    }
    redirect_to('student.php');
}

$students = fetch_all('SELECT * FROM students ORDER BY registration_date DESC, name ASC');

render_header('Student Management');
?>
<!-- Header & Breadcrumbs -->
<div class="section-header">
    <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <span>/</span>
        <span>Student Management</span>
    </div>
    <button class="btn-primary">
        <i class="fas fa-plus"></i> Add New Student
    </button>
</div>

<!-- Table Controls -->
<div class="table-controls">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search by name, email or phone...">
    </div>
</div>

<!-- Table Wrapper -->
<div class="table-wrapper">
    <table class="data-table">
        <thead>
        <tr>
            <th>S.No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Registration Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $idx = 1; foreach ($students as $student): ?>
            <tr>
                <td><?= $idx++ ?></td>
                <td><?= h($student['name']) ?></td>
                <td><?= h($student['email']) ?></td>
                <td><?= h($student['phone']) ?></td>
                <td><?= h($student['registration_date']) ?></td>
                <td class="action-cell">
                    <button class="btn-action edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="student.php?delete=<?= urlencode($student['email']) ?>" class="btn-action delete delete-link" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Student Modal -->
<div class="modal" id="addStudentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Student</h2>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="studentName">Name</label>
                    <input type="text" id="studentName" name="name" placeholder="Enter student name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter email" required>
                </div>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" placeholder="Enter phone number" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save">Save</button>
                <button type="button" class="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal" id="editStudentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Student</h2>
            <button class="modal-close-edit">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form-edit" method="post">
            <input type="hidden" name="action" value="update">
            <div class="form-row">
                <div class="form-group">
                    <label for="editStudentName">Name</label>
                    <input type="text" id="editStudentName" name="name" placeholder="Enter student name" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <!-- Email is primary key, read-only to avoid breaking DB integrity on edit -->
                    <input type="email" id="editEmail" name="email" readonly required>
                </div>
            </div>
            <div class="form-group">
                <label for="editPhone">Phone</label>
                <input type="text" id="editPhone" name="phone" placeholder="Enter phone number" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-update">Update</button>
                <button type="button" class="btn-cancel-edit">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php render_footer(); ?>

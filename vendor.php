<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

if (isset($_GET['delete'])) {
    try {
        $stmt = db()->prepare('DELETE FROM vendors WHERE name = ?');
        $stmt->execute([$_GET['delete']]);
        set_flash('Vendor deleted.');
    } catch (PDOException $e) {
        set_flash('Vendor cannot be deleted while linked to purchases.', 'error');
    }
    redirect_to('vendor.php');
}

if (is_post()) {
    $stmt = db()->prepare('INSERT INTO vendors (name, email, phone) VALUES (?, ?, ?)');
    $stmt->execute([
        trim($_POST['name'] ?? ''),
        trim($_POST['email'] ?? ''),
        trim($_POST['phone'] ?? ''),
    ]);
    set_flash('Vendor added.');
    redirect_to('vendor.php');
}

$vendors = fetch_all('SELECT * FROM vendors ORDER BY name ASC');

render_header('Vendors');
?>
<h2>Vendor Management</h2>
<div class="form-card">
    <form class="inline-form" method="post">
        <input name="name" type="text" placeholder="Vendor name" required>
        <input name="email" type="email" placeholder="Vendor email" required>
        <input name="phone" type="text" placeholder="Phone number" required>
        <button type="submit">Add Vendor</button>
    </form>
</div>

<table>
    <tr><th>Name</th><th>Email</th><th>Phone</th><th>Action</th></tr>
    <?php foreach ($vendors as $vendor): ?>
        <tr>
            <td><?= h($vendor['name']) ?></td>
            <td><?= h($vendor['email']) ?></td>
            <td><?= h($vendor['phone']) ?></td>
            <td><a class="action-link delete-link" href="vendor.php?delete=<?= urlencode($vendor['name']) ?>">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php render_footer(); ?>

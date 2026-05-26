<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

if (isset($_GET['delete'])) {
    try {
        $stmt = db()->prepare('DELETE FROM publications WHERE name = ?');
        $stmt->execute([$_GET['delete']]);
        set_flash('Publication deleted.');
    } catch (PDOException $e) {
        set_flash('Publication cannot be deleted while linked to books.', 'error');
    }
    redirect_to('publication.php');
}

if (is_post()) {
    $stmt = db()->prepare('INSERT INTO publications (name, address, description) VALUES (?, ?, ?)');
    $stmt->execute([
        trim($_POST['name'] ?? ''),
        trim($_POST['address'] ?? ''),
        trim($_POST['description'] ?? ''),
    ]);
    set_flash('Publication added.');
    redirect_to('publication.php');
}

$publications = fetch_all('SELECT * FROM publications ORDER BY name ASC');

render_header('Publications');
?>
<h2>Publication Management</h2>
<div class="form-card">
    <form class="inline-form" method="post">
        <input name="name" type="text" placeholder="Publication name" required>
        <input name="address" type="text" placeholder="Address" required>
        <input name="description" type="text" placeholder="Description" required>
        <button type="submit">Add Publication</button>
    </form>
</div>

<table>
    <tr><th>Name</th><th>Address</th><th>Description</th><th>Action</th></tr>
    <?php foreach ($publications as $publication): ?>
        <tr>
            <td><?= h($publication['name']) ?></td>
            <td><?= h($publication['address']) ?></td>
            <td><?= h($publication['description']) ?></td>
            <td><a class="action-link delete-link" href="publication.php?delete=<?= urlencode($publication['name']) ?>">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php render_footer(); ?>

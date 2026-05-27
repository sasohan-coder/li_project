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
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $stmt = db()->prepare('UPDATE publications SET address = ?, description = ? WHERE name = ?');
        $stmt->execute([
            trim($_POST['address'] ?? ''),
            trim($_POST['description'] ?? ''),
            trim($_POST['name'] ?? '')
        ]);
        set_flash('Publication details updated.');
    } else {
        $stmt = db()->prepare('INSERT INTO publications (name, address, description) VALUES (?, ?, ?)');
        $stmt->execute([
            trim($_POST['name'] ?? ''),
            trim($_POST['address'] ?? ''),
            trim($_POST['description'] ?? ''),
        ]);
        set_flash('Publication added.');
    }
    redirect_to('publication.php');
}

$publications = fetch_all('SELECT * FROM publications ORDER BY name ASC');

render_header('Publications');
?>
<!-- Header & Breadcrumbs -->
<div class="section-header">
    <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <span>/</span>
        <span>Publication Management</span>
    </div>
    <button class="btn-primary">
        <i class="fas fa-plus"></i> Add New Publication
    </button>
</div>

<!-- Table Controls -->
<div class="table-controls">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search publications...">
    </div>
</div>

<!-- Table Wrapper -->
<div class="table-wrapper">
    <table class="data-table">
        <thead>
        <tr>
            <th>S.No.</th>
            <th>Publisher Name</th>
            <th>Address</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $idx = 1; foreach ($publications as $pub): ?>
            <tr>
                <td><?= $idx++ ?></td>
                <td><?= h($pub['name']) ?></td>
                <td><?= h($pub['address']) ?></td>
                <td><?= h($pub['description']) ?></td>
                <td class="action-cell">
                    <button class="btn-action edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="publication.php?delete=<?= urlencode($pub['name']) ?>" class="btn-action delete delete-link" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Publication Modal -->
<div class="modal" id="addPublicationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Publications</h2>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="publisherName">Publisher Name</label>
                    <input type="text" id="publisherName" name="name" placeholder="Enter publisher name" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter address" required>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Enter description" rows="4"></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save">Save</button>
                <button type="button" class="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Publication Modal -->
<div class="modal" id="editPublicationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Publications</h2>
            <button class="modal-close-edit">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form-edit" method="post">
            <input type="hidden" name="action" value="update">
            <div class="form-row">
                <div class="form-group">
                    <label for="editPublisherName">Publisher Name</label>
                    <!-- Name is primary key, read-only to avoid breaking DB integrity on edit -->
                    <input type="text" id="editPublisherName" name="name" readonly required>
                </div>
                <div class="form-group">
                    <label for="editAddress">Address</label>
                    <input type="text" id="editAddress" name="address" placeholder="Enter address" required>
                </div>
            </div>
            <div class="form-group">
                <label for="editDescription">Description</label>
                <textarea id="editDescription" name="description" placeholder="Enter description" rows="4"></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-update">Update</button>
                <button type="button" class="btn-cancel-edit">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php render_footer(); ?>

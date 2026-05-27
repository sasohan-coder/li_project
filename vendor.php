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
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $stmt = db()->prepare('UPDATE vendors SET email = ?, phone = ? WHERE name = ?');
        $stmt->execute([
            trim($_POST['email'] ?? ''),
            trim($_POST['phone'] ?? ''),
            trim($_POST['name'] ?? '')
        ]);
        set_flash('Vendor details updated.');
    } else {
        $stmt = db()->prepare('INSERT INTO vendors (name, email, phone) VALUES (?, ?, ?)');
        $stmt->execute([
            trim($_POST['name'] ?? ''),
            trim($_POST['email'] ?? ''),
            trim($_POST['phone'] ?? ''),
        ]);
        set_flash('Vendor added.');
    }
    redirect_to('vendor.php');
}

$vendors = fetch_all('SELECT * FROM vendors ORDER BY name ASC');

render_header('Vendor Management');
?>
<!-- Breadcrumb Header -->
<div class="section-header">
    <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <span>/</span>
        <span>Vendor Management</span>
    </div>
    <button class="btn-primary">
        <i class="fas fa-plus"></i> Add New Vendor
    </button>
</div>

<!-- Table Controls -->
<div class="table-controls">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search by vendor name, email or phone...">
    </div>
</div>

<!-- Table Wrapper -->
<div class="table-wrapper">
    <table class="data-table">
        <thead>
        <tr>
            <th>S.No.</th>
            <th>Vendor Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $idx = 1; foreach ($vendors as $vendor): ?>
            <tr>
                <td><?= $idx++ ?></td>
                <td><?= h($vendor['name']) ?></td>
                <td><?= h($vendor['email']) ?></td>
                <td><?= h($vendor['phone']) ?></td>
                <td class="action-cell">
                    <button class="btn-action edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="vendor.php?delete=<?= urlencode($vendor['name']) ?>" class="btn-action delete delete-link" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Vendor Modal -->
<div class="modal" id="addVendorModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Vendor</h2>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="vendorName">Vendor Name</label>
                    <input type="text" id="vendorName" name="name" placeholder="Enter vendor name" required>
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

<!-- Edit Vendor Modal -->
<div class="modal" id="editVendorModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Vendor</h2>
            <button class="modal-close-edit">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form-edit" method="post">
            <input type="hidden" name="action" value="update">
            <div class="form-row">
                <div class="form-group">
                    <label for="editVendorName">Vendor Name</label>
                    <!-- Name is primary key, read-only to avoid breaking DB integrity on edit -->
                    <input type="text" id="editVendorName" name="name" readonly required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" name="email" placeholder="Enter email" required>
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

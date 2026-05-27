<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

if (isset($_GET['delete'])) {
    try {
        $stmt = db()->prepare('DELETE FROM subscriptions WHERE title = ?');
        $stmt->execute([$_GET['delete']]);
        set_flash('Subscription deleted.');
    } catch (PDOException $e) {
        set_flash('Subscription cannot be deleted while linked to allotments.', 'error');
    }
    redirect_to('subscription.php');
}

if (is_post()) {
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $stmt = db()->prepare('UPDATE subscriptions SET amount = ?, number_of_days = ? WHERE title = ?');
        $stmt->execute([
            (float) ($_POST['amount'] ?? 0),
            (int) ($_POST['number_of_days'] ?? 0),
            trim($_POST['title'] ?? '')
        ]);
        set_flash('Subscription updated.');
    } else {
        $stmt = db()->prepare('INSERT INTO subscriptions (title, amount, number_of_days) VALUES (?, ?, ?)');
        $stmt->execute([
            trim($_POST['title'] ?? ''),
            (float) ($_POST['amount'] ?? 0),
            (int) ($_POST['number_of_days'] ?? 0),
        ]);
        set_flash('Subscription added.');
    }
    redirect_to('subscription.php');
}

$subscriptions = fetch_all('SELECT * FROM subscriptions ORDER BY number_of_days ASC');

render_header('Subscription Types');
?>
<!-- Breadcrumb Header -->
<div class="section-header">
    <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <span>/</span>
        <span>Subscription Type</span>
    </div>
    <button class="btn-primary">
        <i class="fas fa-plus"></i> Add New Subscription
    </button>
</div>

<!-- Table Controls -->
<div class="table-controls">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search subscription plans...">
    </div>
</div>

<!-- Table Wrapper -->
<div class="table-wrapper">
    <table class="data-table">
        <thead>
        <tr>
            <th>S.No.</th>
            <th>Title</th>
            <th>Amount</th>
            <th>Number of Days</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $idx = 1; foreach ($subscriptions as $sub): ?>
            <tr>
                <td><?= $idx++ ?></td>
                <td><?= h($sub['title']) ?></td>
                <td>৳<?= h((string) $sub['amount']) ?></td>
                <td><?= (int) $sub['number_of_days'] ?> days</td>
                <td class="action-cell">
                    <button class="btn-action edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="subscription.php?delete=<?= urlencode($sub['title']) ?>" class="btn-action delete delete-link" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Subscription Modal -->
<div class="modal" id="addSubscriptionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Subscription</h2>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" placeholder="Enter subscription title" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" placeholder="Enter amount" step="0.01" required>
                </div>
            </div>
            <div class="form-group">
                <label for="numberOfDays">Number of Days</label>
                <input type="number" id="numberOfDays" name="number_of_days" placeholder="Enter number of days" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save">Save</button>
                <button type="button" class="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Subscription Modal -->
<div class="modal" id="editSubscriptionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Subscription</h2>
            <button class="modal-close-edit">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form-edit" method="post">
            <input type="hidden" name="action" value="update">
            <div class="form-row">
                <div class="form-group">
                    <label for="editTitle">Title</label>
                    <!-- Title is primary key, read-only to avoid breaking DB integrity on edit -->
                    <input type="text" id="editTitle" name="title" readonly required>
                </div>
                <div class="form-group">
                    <label for="editAmount">Amount</label>
                    <input type="number" id="editAmount" name="amount" placeholder="Enter amount" step="0.01" required>
                </div>
            </div>
            <div class="form-group">
                <label for="editNumberOfDays">Number of Days</label>
                <input type="number" id="editNumberOfDays" name="number_of_days" placeholder="Enter number of days" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-update">Update</button>
                <button type="button" class="btn-cancel-edit">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php render_footer(); ?>

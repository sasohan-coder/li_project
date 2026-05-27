<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

if (isset($_GET['delete'])) {
    $allotmentId = (int) $_GET['delete'];
    $allotment = fetch_one('SELECT * FROM allotments WHERE id = ?', [$allotmentId]);

    if ($allotment) {
        db()->beginTransaction();
        try {
            $stmt = db()->prepare('DELETE FROM allotments WHERE id = ?');
            $stmt->execute([$allotmentId]);

            $stmt = db()->prepare('UPDATE books SET available_quantity = available_quantity + 1 WHERE book_name = ?');
            $stmt->execute([$allotment['book_name']]);

            db()->commit();
            set_flash('Allotment deleted and stock restored.');
        } catch (Throwable $e) {
            db()->rollBack();
            set_flash('Could not delete allotment.', 'error');
        }
    }

    redirect_to('allotment-history.php');
}

$allotments = fetch_all('SELECT * FROM allotments ORDER BY id DESC');

render_header('Allotment History');
?>
<!-- Breadcrumb Header -->
<div class="section-header">
    <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <span>/</span>
        <span>Allotment History</span>
    </div>
</div>

<!-- Table Controls -->
<div class="table-controls">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search by book name or student email...">
    </div>
</div>

<!-- Table Wrapper -->
<div class="table-wrapper">
    <table class="data-table">
        <thead>
        <tr>
            <th>S.No.</th>
            <th>Book Name</th>
            <th>Student Email</th>
            <th>Subscription Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Allotment Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $idx = 1; foreach ($allotments as $allotment): ?>
            <?php $statusActive = strtotime($allotment['end_date']) >= strtotime(date('Y-m-d')); ?>
            <tr>
                <td><?= $idx++ ?></td>
                <td><?= h($allotment['book_name']) ?></td>
                <td><?= h($allotment['student_email']) ?></td>
                <td>
                    <span class="subscription-badge"><?= h($allotment['subscription_title']) ?></span>
                </td>
                <td><?= h($allotment['start_date']) ?></td>
                <td><?= h($allotment['end_date']) ?></td>
                <td><?= h($allotment['allotment_date']) ?></td>
                <td>
                    <?php if ($statusActive): ?>
                        <span class="status-badge active">
                            <i class="fas fa-circle"></i> Active
                        </span>
                    <?php else: ?>
                        <span class="status-badge expired">
                            <i class="fas fa-circle"></i> Expired
                        </span>
                    <?php endif; ?>
                </td>
                <td class="action-cell">
                    <button class="btn-action view" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <a href="allotment-history.php?delete=<?= (int) $allotment['id'] ?>" class="btn-action delete delete-link" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Summary Cards -->
<div class="summary-section">
    <div class="summary-card">
        <div class="summary-icon active">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="summary-content">
            <p class="summary-label">Active Allotments</p>
            <p class="summary-value" id="activeCount">0</p>
        </div>
    </div>
    <div class="summary-card">
        <div class="summary-icon expired">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="summary-content">
            <p class="summary-label">Expired Allotments</p>
            <p class="summary-value" id="expiredCount">0</p>
        </div>
    </div>
    <div class="summary-card">
        <div class="summary-icon total">
            <i class="fas fa-book"></i>
        </div>
        <div class="summary-content">
            <p class="summary-label">Total Allotments</p>
            <p class="summary-value" id="totalCount">0</p>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal" id="viewModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Allotment Details</h2>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="detail-row">
                <label>Book Name</label>
                <p id="detailBookName"></p>
            </div>
            <div class="detail-row">
                <label>Student Email</label>
                <p id="detailStudentEmail"></p>
            </div>
            <div class="detail-row">
                <label>Subscription Type</label>
                <p id="detailSubscription"></p>
            </div>
            <div class="detail-row">
                <label>Start Date</label>
                <p id="detailStartDate"></p>
            </div>
            <div class="detail-row">
                <label>End Date</label>
                <p id="detailEndDate"></p>
            </div>
            <div class="detail-row">
                <label>Allotment Date</label>
                <p id="detailAllotmentDate"></p>
            </div>
            <div class="detail-row">
                <label>Status</label>
                <p id="detailStatus"></p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-close">Close</button>
        </div>
    </div>
</div>
<?php render_footer(); ?>

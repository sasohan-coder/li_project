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
<h2>Allotment History</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Book</th>
        <th>Student Email</th>
        <th>Subscription</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Allotment Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php foreach ($allotments as $allotment): ?>
        <?php $status = strtotime($allotment['end_date']) >= strtotime(date('Y-m-d')) ? 'Active' : 'Expired'; ?>
        <tr>
            <td><?= h((string) $allotment['id']) ?></td>
            <td><?= h($allotment['book_name']) ?></td>
            <td><?= h($allotment['student_email']) ?></td>
            <td><?= h($allotment['subscription_title']) ?></td>
            <td><?= h($allotment['start_date']) ?></td>
            <td><?= h($allotment['end_date']) ?></td>
            <td><?= h($allotment['allotment_date']) ?></td>
            <td><?= h($status) ?></td>
            <td><a class="action-link delete-link" href="allotment-history.php?delete=<?= (int) $allotment['id'] ?>">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php render_footer(); ?>

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
    $stmt = db()->prepare('INSERT INTO subscriptions (title, amount, number_of_days) VALUES (?, ?, ?)');
    $stmt->execute([
        trim($_POST['title'] ?? ''),
        (float) ($_POST['amount'] ?? 0),
        (int) ($_POST['number_of_days'] ?? 0),
    ]);
    set_flash('Subscription added.');
    redirect_to('subscription.php');
}

$subscriptions = fetch_all('SELECT * FROM subscriptions ORDER BY number_of_days ASC');

render_header('Subscriptions');
?>
<h2>Subscription Types</h2>
<div class="form-card">
    <form class="inline-form" method="post">
        <input name="title" type="text" placeholder="Subscription title" required>
        <input name="amount" type="number" step="0.01" placeholder="Amount" required>
        <input name="number_of_days" type="number" placeholder="Number of days" required>
        <button type="submit">Add Subscription</button>
    </form>
</div>

<table>
    <tr><th>Title</th><th>Amount</th><th>Days</th><th>Action</th></tr>
    <?php foreach ($subscriptions as $subscription): ?>
        <tr>
            <td><?= h($subscription['title']) ?></td>
            <td><?= h((string) $subscription['amount']) ?></td>
            <td><?= h((string) $subscription['number_of_days']) ?></td>
            <td><a class="action-link delete-link" href="subscription.php?delete=<?= urlencode($subscription['title']) ?>">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php render_footer(); ?>

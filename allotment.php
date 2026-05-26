<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

if (is_post()) {
    $bookName = trim($_POST['book_name'] ?? '');
    $studentEmail = trim($_POST['student_email'] ?? '');
    $subscriptionTitle = trim($_POST['subscription_title'] ?? '');

    $subscription = fetch_one('SELECT * FROM subscriptions WHERE title = ?', [$subscriptionTitle]);
    $book = fetch_one('SELECT * FROM books WHERE book_name = ?', [$bookName]);

    if (!$subscription || !$book) {
        set_flash('Invalid book or subscription.', 'error');
        redirect_to('allotment.php');
    }

    if ((int) $book['available_quantity'] < 1) {
        set_flash('This book is out of stock.', 'error');
        redirect_to('allotment.php');
    }

    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d', strtotime('+' . (int) $subscription['number_of_days'] . ' days'));

    db()->beginTransaction();
    try {
        $stmt = db()->prepare('INSERT INTO allotments (book_name, student_email, subscription_title, start_date, end_date, allotment_date) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$bookName, $studentEmail, $subscriptionTitle, $startDate, $endDate, $startDate]);

        $stmt = db()->prepare('UPDATE books SET available_quantity = available_quantity - 1 WHERE book_name = ?');
        $stmt->execute([$bookName]);

        db()->commit();
        set_flash('Book allotted successfully.');
    } catch (Throwable $e) {
        db()->rollBack();
        set_flash('Could not save allotment.', 'error');
    }

    redirect_to('allotment.php');
}

$books = fetch_all('SELECT book_name, available_quantity FROM books ORDER BY book_name ASC');
$students = fetch_all('SELECT email FROM students ORDER BY email ASC');
$subscriptions = fetch_all('SELECT title, number_of_days FROM subscriptions ORDER BY title ASC');

render_header('Allotment');
?>
<h2>Book Allotment</h2>
<div class="form-card">
    <form class="inline-form" method="post">
        <select name="book_name" required>
            <option value="">Select book</option>
            <?php foreach ($books as $book): ?>
                <option value="<?= h($book['book_name']) ?>"><?= h($book['book_name']) ?> (<?= (int) $book['available_quantity'] ?> available)</option>
            <?php endforeach; ?>
        </select>
        <select name="student_email" required>
            <option value="">Select student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= h($student['email']) ?>"><?= h($student['email']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="subscription_title" required>
            <option value="">Select subscription</option>
            <?php foreach ($subscriptions as $subscription): ?>
                <option value="<?= h($subscription['title']) ?>"><?= h($subscription['title']) ?> (<?= (int) $subscription['number_of_days'] ?> days)</option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Allot Book</button>
    </form>
</div>
<?php render_footer(); ?>

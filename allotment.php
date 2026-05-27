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

    $startDate = trim($_POST['start_date'] ?? '');
    if ($startDate === '') {
        $startDate = date('Y-m-d');
    }

    $endDate = trim($_POST['end_date'] ?? '');
    if ($endDate === '') {
        $endDate = date('Y-m-d', strtotime('+' . (int) $subscription['number_of_days'] . ' days'));
    }

    db()->beginTransaction();
    try {
        $stmt = db()->prepare('INSERT INTO allotments (book_name, student_email, subscription_title, start_date, end_date, allotment_date) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$bookName, $studentEmail, $subscriptionTitle, $startDate, $endDate, date('Y-m-d')]);

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

$books = fetch_all('SELECT * FROM books ORDER BY book_name ASC');
$students = fetch_all('SELECT email, name FROM students ORDER BY name ASC');
$subscriptions = fetch_all('SELECT title, number_of_days FROM subscriptions ORDER BY title ASC');

render_header('Book Allotment');
?>
<!-- Breadcrumb Header -->
<div class="section-header">
    <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <span>/</span>
        <span>Book Allotment</span>
    </div>
</div>

<!-- Search box -->
<div class="search-section">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search books by name or author...">
    </div>
</div>

<!-- Books Grid -->
<div class="books-grid">
    <?php foreach ($books as $book): ?>
        <div class="book-card">
            <div class="card-image">
                <?php if ($book['book_image']): ?>
                    <img src="<?= h($book['book_image']) ?>" alt="Book Image">
                <?php else: ?>
                    <div class="no-img-card"><i class="fas fa-book"></i></div>
                <?php endif; ?>
                <div class="quantity-badge"><?= (int) $book['available_quantity'] ?> left</div>
            </div>
            <div class="card-content">
                <h3 class="book-title"><?= h($book['book_name']) ?></h3>
                <p class="book-author">By <?= h($book['author_name']) ?></p>
                <br>
                <?php if ((int) $book['available_quantity'] > 0): ?>
                    <button class="allot-btn" data-book-name="<?= h($book['book_name']) ?>" data-book-image="<?= h($book['book_image'] ?: '') ?>">
                        <i class="fas fa-hand-holding-heart"></i> Allot Book
                    </button>
                <?php else: ?>
                    <button class="allot-btn out-of-stock" disabled>
                        <i class="fas fa-ban"></i> Out of Stock
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Allot Book Modal -->
<div class="modal" id="allotModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title-section">
                <img id="modalBookImage" src="" alt="Book" class="modal-book-image">
                <div>
                    <h2>Allot Book</h2>
                    <p class="modal-book-name" id="modalBookName"></p>
                </div>
            </div>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form" method="post">
            <input type="hidden" id="hiddenBookName" name="book_name">

            <div class="form-row">
                <div class="form-group">
                    <label for="studentName">Student</label>
                    <select id="studentName" name="student_email" required>
                        <option value="">Select Student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= h($student['email']) ?>"><?= h($student['name']) ?> (<?= h($student['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subscriptionType">Subscription Type</label>
                    <select id="subscriptionType" name="subscription_title" required>
                        <option value="">Select Subscription</option>
                        <?php foreach ($subscriptions as $sub): ?>
                            <option value="<?= h($sub['title']) ?>" data-days="<?= (int) $sub['number_of_days'] ?>"><?= h($sub['title']) ?> (<?= (int) $sub['number_of_days'] ?> days)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="startDate">Starting Date</label>
                    <input type="text" id="startDate" placeholder="Select start date" required>
                    <input type="hidden" id="hiddenStartDate" name="start_date">
                </div>
                <div class="form-group">
                    <label for="endDate">End Date</label>
                    <input type="text" id="endDate" placeholder="Select end date" required>
                    <input type="hidden" id="hiddenEndDate" name="end_date">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-allot">
                    <i class="fas fa-check"></i> Allot Book
                </button>
                <button type="button" class="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php render_footer(); ?>

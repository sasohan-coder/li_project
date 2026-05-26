<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

if (isset($_GET['delete'])) {
    $purchaseId = (int) $_GET['delete'];
    $purchase = fetch_one('SELECT * FROM purchases WHERE id = ?', [$purchaseId]);

    if ($purchase) {
        db()->beginTransaction();
        try {
            $stmt = db()->prepare('UPDATE books SET available_quantity = available_quantity - ? WHERE book_name = ? AND available_quantity >= ?');
            $stmt->execute([(int) $purchase['quantity'], $purchase['book_name'], (int) $purchase['quantity']]);

            $stmt = db()->prepare('DELETE FROM purchases WHERE id = ?');
            $stmt->execute([$purchaseId]);
            db()->commit();
            set_flash('Purchase deleted and stock adjusted.');
        } catch (Throwable $e) {
            db()->rollBack();
            set_flash('Could not delete purchase.', 'error');
        }
    }

    redirect_to('purchase.php');
}

if (is_post()) {
    $bookName = trim($_POST['book_name'] ?? '');
    $vendorName = trim($_POST['vendor_name'] ?? '');
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $perBookPrice = (float) ($_POST['per_book_price'] ?? 0);
    $totalAmount = $quantity * $perBookPrice;

    db()->beginTransaction();
    try {
        $stmt = db()->prepare('INSERT INTO purchases (book_name, vendor_name, quantity, per_book_price, total_amount, purchase_date) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$bookName, $vendorName, $quantity, $perBookPrice, $totalAmount, date('Y-m-d')]);

        $stmt = db()->prepare('UPDATE books SET available_quantity = available_quantity + ? WHERE book_name = ?');
        $stmt->execute([$quantity, $bookName]);

        db()->commit();
        set_flash('Purchase added and stock updated.');
    } catch (Throwable $e) {
        db()->rollBack();
        set_flash('Could not save purchase.', 'error');
    }

    redirect_to('purchase.php');
}

$purchases = fetch_all('SELECT * FROM purchases ORDER BY id DESC');
$books = fetch_all('SELECT book_name FROM books ORDER BY book_name ASC');
$vendors = fetch_all('SELECT name FROM vendors ORDER BY name ASC');

render_header('Purchases');
?>
<h2>Purchase Books</h2>
<div class="form-card">
    <form class="inline-form" method="post">
        <select name="book_name" required>
            <option value="">Select book</option>
            <?php foreach ($books as $book): ?>
                <option value="<?= h($book['book_name']) ?>"><?= h($book['book_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="vendor_name" required>
            <option value="">Select vendor</option>
            <?php foreach ($vendors as $vendor): ?>
                <option value="<?= h($vendor['name']) ?>"><?= h($vendor['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input name="quantity" type="number" placeholder="Quantity" required>
        <input name="per_book_price" type="number" step="0.01" placeholder="Per book price" required>
        <button type="submit">Add Purchase</button>
    </form>
</div>

<table>
    <tr><th>ID</th><th>Book</th><th>Vendor</th><th>Quantity</th><th>Price</th><th>Total</th><th>Date</th><th>Action</th></tr>
    <?php foreach ($purchases as $purchase): ?>
        <tr>
            <td><?= h((string) $purchase['id']) ?></td>
            <td><?= h($purchase['book_name']) ?></td>
            <td><?= h($purchase['vendor_name']) ?></td>
            <td><?= h((string) $purchase['quantity']) ?></td>
            <td><?= h((string) $purchase['per_book_price']) ?></td>
            <td><?= h((string) $purchase['total_amount']) ?></td>
            <td><?= h($purchase['purchase_date']) ?></td>
            <td><a class="action-link delete-link" href="purchase.php?delete=<?= (int) $purchase['id'] ?>">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php render_footer(); ?>

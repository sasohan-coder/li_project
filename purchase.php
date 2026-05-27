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
    $purchaseDate = trim($_POST['purchase_date'] ?? '');
    if ($purchaseDate === '') {
        $purchaseDate = date('Y-m-d');
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $purchaseId = (int) ($_POST['id'] ?? 0);
        $oldPurchase = fetch_one('SELECT * FROM purchases WHERE id = ?', [$purchaseId]);
        if ($oldPurchase) {
            db()->beginTransaction();
            try {
                // Restore stock from old purchase
                $stmt = db()->prepare('UPDATE books SET available_quantity = available_quantity - ? WHERE book_name = ?');
                $stmt->execute([$oldPurchase['quantity'], $oldPurchase['book_name']]);

                // Update purchase record
                $stmt = db()->prepare('UPDATE purchases SET book_name = ?, vendor_name = ?, quantity = ?, per_book_price = ?, total_amount = ?, purchase_date = ? WHERE id = ?');
                $stmt->execute([$bookName, $vendorName, $quantity, $perBookPrice, $totalAmount, $purchaseDate, $purchaseId]);

                // Add stock for new purchase
                $stmt = db()->prepare('UPDATE books SET available_quantity = available_quantity + ? WHERE book_name = ?');
                $stmt->execute([$quantity, $bookName]);

                db()->commit();
                set_flash('Purchase updated and stock adjusted.');
            } catch (Throwable $e) {
                db()->rollBack();
                set_flash('Could not update purchase.', 'error');
            }
        }
    } else {
        db()->beginTransaction();
        try {
            $stmt = db()->prepare('INSERT INTO purchases (book_name, vendor_name, quantity, per_book_price, total_amount, purchase_date) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$bookName, $vendorName, $quantity, $perBookPrice, $totalAmount, $purchaseDate]);

            $stmt = db()->prepare('UPDATE books SET available_quantity = available_quantity + ? WHERE book_name = ?');
            $stmt->execute([$quantity, $bookName]);

            db()->commit();
            set_flash('Purchase added and stock updated.');
        } catch (Throwable $e) {
            db()->rollBack();
            set_flash('Could not save purchase.', 'error');
        }
    }

    redirect_to('purchase.php');
}

$purchases = fetch_all('SELECT * FROM purchases ORDER BY id DESC');
$books = fetch_all('SELECT book_name FROM books ORDER BY book_name ASC');
$vendors = fetch_all('SELECT name FROM vendors ORDER BY name ASC');

render_header('Purchases');
?>
<!-- Breadcrumb Header -->
<div class="section-header">
    <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <span>/</span>
        <span>Purchase Books</span>
    </div>
    <button class="btn-primary">
        <i class="fas fa-plus"></i> Add New Purchase
    </button>
</div>

<!-- Table Controls -->
<div class="table-controls">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search by book name...">
    </div>
</div>

<!-- Table Wrapper -->
<div class="table-wrapper">
    <table class="data-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Book Name</th>
            <th>Vendor</th>
            <th>Quantity</th>
            <th>Per Book Price</th>
            <th>Total Amount</th>
            <th>Purchase Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($purchases as $purchase): ?>
            <tr>
                <td><?= h((string) $purchase['id']) ?></td>
                <td><?= h($purchase['book_name']) ?></td>
                <td><?= h($purchase['vendor_name']) ?></td>
                <td><?= h((string) $purchase['quantity']) ?></td>
                <td>₹<?= h((string) $purchase['per_book_price']) ?></td>
                <td>₹<?= h((string) $purchase['total_amount']) ?></td>
                <td><?= h($purchase['purchase_date']) ?></td>
                <td class="action-cell">
                    <button class="btn-action edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="purchase.php?delete=<?= (int) $purchase['id'] ?>" class="btn-action delete delete-link" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Purchase Modal -->
<div class="modal" id="addPurchaseModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Purchase</h2>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="bookName">Book Name</label>
                    <select id="bookName" name="book_name" required>
                        <option value="">Select Book</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?= h($book['book_name']) ?>"><?= h($book['book_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vendor">Vendor</label>
                    <select id="vendor" name="vendor_name" required>
                        <option value="">Select Vendor</option>
                        <?php foreach ($vendors as $vendor): ?>
                            <option value="<?= h($vendor['name']) ?>"><?= h($vendor['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" required>
                </div>
                <div class="form-group">
                    <label for="perBookPrice">Per Book Price</label>
                    <input type="number" id="perBookPrice" name="per_book_price" placeholder="Enter per book price" step="0.01" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="purchaseDate">Purchase Date</label>
                    <!-- Formatted via Flatpickr in JS -->
                    <input type="text" id="purchaseDate" name="purchase_date" placeholder="Select date" required>
                </div>
            </div>
            <div class="form-group total-group">
                <label for="totalAmount">Total Amount</label>
                <div class="total-display">
                    <span class="currency">₹</span>
                    <input type="text" id="totalAmount" placeholder="0.00" disabled>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save">Save</button>
                <button type="button" class="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Purchase Modal -->
<div class="modal" id="editPurchaseModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Purchase</h2>
            <button class="modal-close-edit">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form-edit" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="editPurchaseId" name="id">
            <div class="form-row">
                <div class="form-group">
                    <label for="editBookName">Book Name</label>
                    <select id="editBookName" name="book_name" required>
                        <option value="">Select Book</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?= h($book['book_name']) ?>"><?= h($book['book_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editVendor">Vendor</label>
                    <select id="editVendor" name="vendor_name" required>
                        <option value="">Select Vendor</option>
                        <?php foreach ($vendors as $vendor): ?>
                            <option value="<?= h($vendor['name']) ?>"><?= h($vendor['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="editQuantity">Quantity</label>
                    <input type="number" id="editQuantity" name="quantity" placeholder="Enter quantity" required>
                </div>
                <div class="form-group">
                    <label for="editPerBookPrice">Per Book Price</label>
                    <input type="number" id="editPerBookPrice" name="per_book_price" placeholder="Enter per book price" step="0.01" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="editPurchaseDate">Purchase Date</label>
                    <input type="text" id="editPurchaseDate" name="purchase_date" placeholder="Select date" required>
                </div>
            </div>
            <div class="form-group total-group">
                <label for="editTotalAmount">Total Amount</label>
                <div class="total-display">
                    <span class="currency">₹</span>
                    <input type="text" id="editTotalAmount" placeholder="0.00" disabled>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-update">Update</button>
                <button type="button" class="btn-cancel-edit">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php render_footer(); ?>

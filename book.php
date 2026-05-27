<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

if (isset($_GET['delete'])) {
    try {
        $stmt = db()->prepare('DELETE FROM books WHERE book_name = ?');
        $stmt->execute([$_GET['delete']]);
        set_flash('Book deleted.');
    } catch (PDOException $e) {
        set_flash('Book cannot be deleted while linked to purchases or allotments.', 'error');
    }
    redirect_to('book.php');
}

if (is_post()) {
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $stmt = db()->prepare('UPDATE books SET author_name = ?, publication_name = ?, book_image = ? WHERE book_name = ?');
        $stmt->execute([
            trim($_POST['author_name'] ?? ''),
            trim($_POST['publication_name'] ?? ''),
            trim($_POST['book_image'] ?? ''),
            trim($_POST['book_name'] ?? '')
        ]);
        set_flash('Book updated successfully.');
    } else {
        $stmt = db()->prepare('INSERT INTO books (book_name, book_image, author_name, available_quantity, publication_name) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            trim($_POST['book_name'] ?? ''),
            trim($_POST['book_image'] ?? ''),
            trim($_POST['author_name'] ?? ''),
            (int) ($_POST['available_quantity'] ?? 0),
            trim($_POST['publication_name'] ?? ''),
        ]);
        set_flash('Book added.');
    }
    redirect_to('book.php');
}

$books = fetch_all('SELECT * FROM books ORDER BY book_name ASC');
$publications = fetch_all('SELECT name FROM publications ORDER BY name ASC');

render_header('Manage Books');
?>
<!-- Header & Breadcrumbs -->
<div class="section-header">
    <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <span>/</span>
        <span>Manage Books</span>
    </div>
    <button class="btn-primary">
        <i class="fas fa-plus"></i> Add New Book
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
            <th>S.No.</th>
            <th>Book Name</th>
            <th>Book Image</th>
            <th>Author Name</th>
            <th>Publisher Name</th>
            <th>Available Quantity</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $idx = 1; foreach ($books as $book): ?>
            <tr>
                <td><?= $idx++ ?></td>
                <td><?= h($book['book_name']) ?></td>
                <td>
                    <?php if ($book['book_image']): ?>
                        <img src="<?= h($book['book_image']) ?>" alt="Book Image" class="book-img">
                    <?php else: ?>
                        <span class="no-img">No Image</span>
                    <?php endif; ?>
                </td>
                <td><?= h($book['author_name']) ?></td>
                <td><?= h($book['publication_name']) ?></td>
                <td><?= h((string) $book['available_quantity']) ?></td>
                <td class="action-cell">
                    <button class="btn-action edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="book.php?delete=<?= urlencode($book['book_name']) ?>" class="btn-action delete delete-link" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Book Modal -->
<div class="modal" id="addBookModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Book</h2>
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="bookName">Book Name</label>
                    <input type="text" id="bookName" name="book_name" placeholder="Enter book name" required>
                </div>
                <div class="form-group">
                    <label for="authorName">Author Name</label>
                    <input type="text" id="authorName" name="author_name" placeholder="Enter author name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="publisherName">Publisher Name</label>
                    <select id="publisherName" name="publication_name" required>
                        <option value="">Select Publisher</option>
                        <?php foreach ($publications as $pub): ?>
                            <option value="<?= h($pub['name']) ?>"><?= h($pub['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bookImage">Book Image URL</label>
                    <input type="text" id="bookImage" name="book_image" placeholder="Enter image URL or filename">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="availableQuantity">Available Quantity</label>
                    <input type="number" id="availableQuantity" name="available_quantity" placeholder="Enter available quantity" value="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save">Save</button>
                <button type="button" class="btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Book Modal -->
<div class="modal" id="editBookModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Book</h2>
            <button class="modal-close-edit">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="modal-form-edit" method="post">
            <input type="hidden" name="action" value="update">
            <div class="form-row">
                <div class="form-group">
                    <label for="editBookName">Book Name</label>
                    <input type="text" id="editBookName" name="book_name" readonly required>
                </div>
                <div class="form-group">
                    <label for="editAuthorName">Author Name</label>
                    <input type="text" id="editAuthorName" name="author_name" placeholder="Enter author name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="editPublisherName">Publisher Name</label>
                    <select id="editPublisherName" name="publication_name" required>
                        <option value="">Select Publisher</option>
                        <?php foreach ($publications as $pub): ?>
                            <option value="<?= h($pub['name']) ?>"><?= h($pub['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editBookImage">Book Image URL</label>
                    <input type="text" id="editBookImage" name="book_image" placeholder="Enter image URL or filename">
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

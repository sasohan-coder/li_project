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
    $stmt = db()->prepare('INSERT INTO books (book_name, book_image, author_name, available_quantity, publication_name) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['book_name'] ?? ''),
        trim($_POST['book_image'] ?? ''),
        trim($_POST['author_name'] ?? ''),
        (int) ($_POST['available_quantity'] ?? 0),
        trim($_POST['publication_name'] ?? ''),
    ]);
    set_flash('Book added.');
    redirect_to('book.php');
}

$books = fetch_all('SELECT * FROM books ORDER BY book_name ASC');
$publications = fetch_all('SELECT name FROM publications ORDER BY name ASC');

render_header('Books');
?>
<h2>Book Management</h2>
<div class="form-card">
    <form class="inline-form" method="post">
        <input name="book_name" type="text" placeholder="Book name" required>
        <input name="author_name" type="text" placeholder="Author name" required>
        <input name="book_image" type="text" placeholder="Image URL or file name">
        <input name="available_quantity" type="number" placeholder="Available quantity" required>
        <select name="publication_name" required>
            <option value="">Select publication</option>
            <?php foreach ($publications as $publication): ?>
                <option value="<?= h($publication['name']) ?>"><?= h($publication['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add Book</button>
    </form>
</div>

<table>
    <tr><th>Book Name</th><th>Author</th><th>Image</th><th>Available</th><th>Publication</th><th>Action</th></tr>
    <?php foreach ($books as $book): ?>
        <tr>
            <td><?= h($book['book_name']) ?></td>
            <td><?= h($book['author_name']) ?></td>
            <td><?= h($book['book_image']) ?></td>
            <td><?= h((string) $book['available_quantity']) ?></td>
            <td><?= h($book['publication_name']) ?></td>
            <td><a class="action-link delete-link" href="book.php?delete=<?= urlencode($book['book_name']) ?>">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php render_footer(); ?>

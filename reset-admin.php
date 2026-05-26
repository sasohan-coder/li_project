<?php
require_once __DIR__ . '/includes/common.php';

try {
    $hashed_pass = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Check if the admin user exists in the database
    $exists = fetch_one('SELECT email FROM users WHERE email = ?', ['admin@example.com']);
    
    if ($exists) {
        // If the admin user exists, update their password with the secure hash
        $stmt = db()->prepare('UPDATE users SET pass = ? WHERE email = ?');
        $stmt->execute([$hashed_pass, 'admin@example.com']);
        echo "<h2>Success!</h2>";
        echo "<p>Admin password has been successfully hashed and updated to <strong>admin123</strong> in your database.</p>";
    } else {
        // If the admin user does not exist, create it with the secure hash
        $stmt = db()->prepare('INSERT INTO users (email, name, pass) VALUES (?, ?, ?)');
        $stmt->execute(['admin@example.com', 'Admin User', $hashed_pass]);
        echo "<h2>Success!</h2>";
        echo "<p>Admin user ('admin@example.com') was missing, so we have created it with the secure hashed password <strong>admin123</strong>.</p>";
    }
    
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
} catch (Exception $e) {
    echo "<h2>Database Error!</h2>";
    echo "<p>Something went wrong: " . htmlspecialchars($e->getMessage()) . "</p>";
}

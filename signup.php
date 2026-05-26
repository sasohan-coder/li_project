<?php

require_once __DIR__ . '/includes/layout.php';

if (is_post()) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = trim($_POST['pass'] ?? '');

    if ($name === '' || $email === '' || $pass === '') {
        set_flash('Please fill all fields.', 'error');
        redirect_to('signup.php');
    }

    $exists = fetch_one('SELECT email FROM users WHERE email = ?', [$email]);
    if ($exists) {
        set_flash('User already exists.', 'error');
        redirect_to('signup.php');
    }

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = db()->prepare('INSERT INTO users (email, name, pass) VALUES (?, ?, ?)');
    $stmt->execute([$email, $name, $hashed_pass]);

    set_flash('Account created. Please log in.');
    redirect_to('login.php');
}

render_header('Signup');
?>
<div class="box wide">
    <h2>Create Account</h2>
    <form method="post">
        <label for="name">Full Name</label>
        <input id="name" name="name" type="text" required>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>

        <label for="pass">Password</label>
        <input id="pass" name="pass" type="password" required>

        <button class="btn-success" type="submit">Sign Up</button>
    </form>
    <p class="muted-link"><a href="login.php">Back to login</a></p>
</div>
<?php render_footer(); ?>

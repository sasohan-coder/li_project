<?php

require_once __DIR__ . '/includes/layout.php';

if (is_logged_in()) {
    redirect_to('dashboard.php');
}

if (is_post()) {
    $email = trim($_POST['email'] ?? '');
    $pass = trim($_POST['pass'] ?? '');

    $user = fetch_one('SELECT * FROM users WHERE email = ?', [$email]);

    if ($user && password_verify($pass, $user['pass'])) {
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        set_flash('Login successful.');
        redirect_to('dashboard.php');
    }

    set_flash('Wrong email or password.', 'error');
    redirect_to('login.php');
}

render_header('Login');
?>
<div class="box">
    <h2>Library Login</h2>
    <form method="post">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>

        <label for="pass">Password</label>
        <input id="pass" name="pass" type="password" required>

        <button class="btn-primary" type="submit">Sign In</button>
    </form>
    <p class="muted-link"><a href="signup.php">Create account</a></p>
</div>
<?php render_footer(); ?>

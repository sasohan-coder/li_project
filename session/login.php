<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Demo - Welcome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>
    <div class="box wide">
        <?php
            $email = trim($_POST['email'] ?? '');
            $pass = trim($_POST['pass'] ?? '');

            if($email == 'admin@gmail.com' && $pass == '123') {
                $_SESSION['email'] = $email;
                $_SESSION['pass'] = $pass;
        ?>
                <div style="text-align: center; margin-bottom: 24px;">
                    <i class="fas fa-check-circle" style="font-size: 3.5rem; color: var(--success); margin-bottom: 12px;"></i>
                    <h2>Authentication Successful</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem;">Session has been started and populated.</p>
                </div>

                <div class="message success" style="margin-bottom: 24px;">
                    Welcome back! Your session details are stored securely.
                </div>

                <div class="card" style="padding: 20px; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: #f8fafc; margin-bottom: 24px;">
                    <p style="margin: 6px 0; font-size: 1rem;"><strong>Email:</strong> <code style="color: var(--primary);"><?= htmlspecialchars($_SESSION['email']) ?></code></p>
                    <p style="margin: 6px 0; font-size: 1rem;"><strong>Password (hashed in session):</strong> <code style="color: var(--primary);"><?= htmlspecialchars($_SESSION['pass']) ?></code></p>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="file1.php" class="btn-primary" style="margin-top: 0; text-align: center; text-decoration: none; line-height: 24px;">
                        <i class="fas fa-play" style="margin-right: 8px;"></i> Go to File 1 (Set Variables)
                    </a>
                    <a href="logout (1).php" class="logout-btn">
                        <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i> Logout Session
                    </a>
                </div>
        <?php
            } else {
        ?>
                <div style="text-align: center; margin-bottom: 24px;">
                    <i class="fas fa-times-circle" style="font-size: 3.5rem; color: var(--danger); margin-bottom: 12px;"></i>
                    <h2>Access Denied</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem;">Incorrect credentials supplied.</p>
                </div>

                <div class="message error" style="margin-bottom: 24px;">
                    Wrong email or password. Please try again.
                </div>

                <a href="index (1).php" class="btn-primary" style="margin-top: 0; text-align: center; text-decoration: none; line-height: 24px;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back to Login Form
                </a>
        <?php
            }
        ?>
    </div>
</body>
</html>
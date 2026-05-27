<?php
    session_start();

    // Set session variables
    $_SESSION['game'] = 'Cricket';
    $_SESSION['color'] = 'Black';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Demo - File 1 (Set)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>
    <div class="box wide">
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="fas fa-save" style="font-size: 3.5rem; color: var(--primary); margin-bottom: 12px;"></i>
            <h2>File 1: Set Session Data</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Populating the server session memory with new variables.</p>
        </div>

        <div class="message success" style="margin-bottom: 24px;">
            <i class="fas fa-check" style="margin-right: 8px;"></i> Session variables have been set successfully!
        </div>

        <div class="card" style="padding: 20px; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: #f8fafc; margin-bottom: 24px;">
            <p style="margin: 6px 0; font-size: 1rem;"><strong>$_SESSION['game']</strong> = <span style="color: var(--primary); font-weight: 600;">"Cricket"</span></p>
            <p style="margin: 6px 0; font-size: 1rem;"><strong>$_SESSION['color']</strong> = <span style="color: var(--primary); font-weight: 600;">"Black"</span></p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="file2.php" class="btn-primary" style="margin-top: 0; text-align: center; text-decoration: none; line-height: 24px; background: var(--success);">
                Go to File 2 (Read Variables) <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </a>
            <div style="display: flex; justify-content: space-between; margin-top: 14px;">
                <a href="login.php" class="action-link"><i class="fas fa-home"></i> Dashboard</a>
                <a href="file3.php" class="action-link delete-link"><i class="fas fa-trash"></i> Destroy Session</a>
            </div>
        </div>
    </div>
</body>
</html>
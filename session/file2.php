<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Demo - File 2 (Read)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>
    <div class="box wide">
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="fas fa-eye" style="font-size: 3.5rem; color: var(--primary); margin-bottom: 12px;"></i>
            <h2>File 2: Read Session Data</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Retrieving active variables from current PHP session.</p>
        </div>

        <div class="card" style="padding: 20px; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: #f8fafc; margin-bottom: 24px;">
            <p style="margin: 8px 0; font-size: 1.1rem;">
                <strong>$_SESSION['game']:</strong> 
                <span style="font-weight: 600; padding: 2px 8px; border-radius: var(--radius-sm); <?= isset($_SESSION['game']) ? 'color: var(--primary); background: var(--primary-light);' : 'color: var(--danger); background: var(--danger-light);' ?>">
                    <?= isset($_SESSION['game']) ? htmlspecialchars($_SESSION['game']) : 'Not Set' ?>
                </span>
            </p>
            <p style="margin: 8px 0; font-size: 1.1rem; margin-top: 12px;">
                <strong>$_SESSION['color']:</strong> 
                <span style="font-weight: 600; padding: 2px 8px; border-radius: var(--radius-sm); <?= isset($_SESSION['color']) ? 'color: var(--primary); background: var(--primary-light);' : 'color: var(--danger); background: var(--danger-light);' ?>">
                    <?= isset($_SESSION['color']) ? htmlspecialchars($_SESSION['color']) : 'Not Set' ?>
                </span>
            </p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="file3.php" class="btn-primary" style="margin-top: 0; text-align: center; text-decoration: none; line-height: 24px; background: var(--danger);">
                Destroy Session <i class="fas fa-trash-alt" style="margin-left: 8px;"></i>
            </a>
            <div style="display: flex; justify-content: space-between; margin-top: 14px;">
                <a href="file1.php" class="action-link"><i class="fas fa-plus"></i> Set/Reset Variables</a>
                <a href="login.php" class="action-link"><i class="fas fa-home"></i> Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
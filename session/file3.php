<?php
    session_start();
    session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Demo - File 3 (Destroy)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>
    <div class="box wide">
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="fas fa-trash-alt" style="font-size: 3.5rem; color: var(--danger); margin-bottom: 12px;"></i>
            <h2>File 3: Session Destroyed</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Unsetting and clearing all active session data from the server.</p>
        </div>

        <div class="message error" style="margin-bottom: 24px;">
            <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i> Session has been successfully terminated.
        </div>

        <div class="card" style="padding: 20px; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: #f8fafc; margin-bottom: 24px;">
            <p style="margin: 0; color: var(--text-muted); text-align: center; font-size: 0.95rem;">
                All session variables (<code>game</code>, <code>color</code>, etc.) are now cleared from the server memory.
            </p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="file2.php" class="btn-primary" style="margin-top: 0; text-align: center; text-decoration: none; line-height: 24px; background: var(--primary);">
                <i class="fas fa-redo" style="margin-right: 8px;"></i> Verify in File 2 (Read State)
            </a>
            <div style="display: flex; justify-content: space-between; margin-top: 14px;">
                <a href="file1.php" class="action-link"><i class="fas fa-plus"></i> Set Variables again</a>
                <a href="index (1).php" class="action-link"><i class="fas fa-sign-in-alt"></i> Login again</a>
            </div>
        </div>
    </div>
</body>
</html>
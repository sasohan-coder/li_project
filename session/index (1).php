<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Demo - Login</title>
    <!-- Importing Google Fonts Inter and font-awesome for a highly premium feel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>
    <div class="box">
        <div style="text-align: center; margin-bottom: 20px;">
            <i class="fas fa-key" style="font-size: 3rem; color: var(--primary); margin-bottom: 12px;"></i>
            <h2>Session Lab Login</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Demonstrating PHP Session Management</p>
        </div>

        <form action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" placeholder="admin@gmail.com" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" placeholder="123" name="pass" required>
            
            <button type="submit" name="submit" class="btn-primary">Sign In</button>
        </form>

        <p class="muted-link">
            <span style="color: var(--text-muted);">Try credentials:</span> 
            <strong>admin@gmail.com</strong> / <strong>123</strong>
        </p>
    </div>
</body>
</html>
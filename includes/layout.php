<?php

require_once __DIR__ . '/common.php';

function render_header(string $title): void
{
    $flash = get_flash();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title) ?></title>
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
<?php if (is_logged_in()): ?>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h1 class="brand-title">Library System</h1>
                <p class="brand-subtitle">PHP + MySQL</p>
            </div>
            <nav class="sidebar-nav">
                <a class="<?= current_page() === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a class="<?= current_page() === 'student.php' ? 'active' : '' ?>" href="student.php">
                    <span class="icon">🎓</span> Students
                </a>
                <a class="<?= current_page() === 'vendor.php' ? 'active' : '' ?>" href="vendor.php">
                    <span class="icon">🏢</span> Vendors
                </a>
                <a class="<?= current_page() === 'publication.php' ? 'active' : '' ?>" href="publication.php">
                    <span class="icon">📰</span> Publications
                </a>
                <a class="<?= current_page() === 'subscription.php' ? 'active' : '' ?>" href="subscription.php">
                    <span class="icon">🔔</span> Subscriptions
                </a>
                <a class="<?= current_page() === 'book.php' ? 'active' : '' ?>" href="book.php">
                    <span class="icon">📚</span> Books
                </a>
                <a class="<?= current_page() === 'purchase.php' ? 'active' : '' ?>" href="purchase.php">
                    <span class="icon">🛒</span> Purchases
                </a>
                <a class="<?= current_page() === 'allotment.php' ? 'active' : '' ?>" href="allotment.php">
                    <span class="icon">🔑</span> Allotment
                </a>
                <a class="<?= current_page() === 'allotment-history.php' ? 'active' : '' ?>" href="allotment-history.php">
                    <span class="icon">⏳</span> History
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <span class="user-avatar"><?= strtoupper(substr(current_user_name(), 0, 1)) ?></span>
                    <div class="user-details">
                        <span class="user-name"><?= h(current_user_name()) ?></span>
                        <span class="user-role">Administrator</span>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <span class="icon">🚪</span> Logout
                </a>
            </div>
        </aside>
        <div class="main-content">
            <header class="topbar">
                <div class="topbar-row">
                    <div>
                        <h2 class="current-page-title"><?= h($title) ?></h2>
                    </div>
                    <div class="top-actions">
                        <!-- Extra action items can go here -->
                    </div>
                </div>
            </header>
<?php endif; ?>

<main class="page">
    <?php if ($flash): ?>
        <div class="message <?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
    <?php endif; ?>
<?php
}

function render_footer(): void
{
    ?>
</main>
<?php if (is_logged_in()): ?>
        </div> <!-- .main-content -->
    </div> <!-- .app-layout -->
<?php endif; ?>
<script src="js/app.js"></script>
</body>
</html>
<?php
}

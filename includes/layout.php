<?php

require_once __DIR__ . '/common.php';

function render_header(string $title): void
{
    $flash = get_flash();
    $page = current_page();
    
    // Determine if it's an auth page (login.php, signup.php)
    $is_auth = ($page === 'login.php' || $page === 'signup.php');
    
    ?>
<!DOCTYPE html>
<html lang="en" <?= $is_auth ? 'class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="" data-template="vertical-menu-template-free"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title><?= h($title) ?></title>
    
    <?php if ($is_auth): ?>
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="img/favicon/favicon.ico"/>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com"/>
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>

        <!-- Icons -->
        <link rel="stylesheet" href="vendor/fonts/boxicons.css"/>

        <!-- Core CSS -->
        <link rel="stylesheet" href="vendor/css/core.css" class="template-customizer-core-css"/>
        <link rel="stylesheet" href="vendor/css/theme-default.css" class="template-customizer-theme-css"/>
        <link rel="stylesheet" href="css/login_signup-styles.css"/>

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>

        <!-- Page CSS -->
        <link rel="stylesheet" href="vendor/css/pages/page-auth.css"/>
        
        <!-- Helpers -->
        <script src="vendor/js/helpers.js"></script>
        <script src="js/config.js"></script>
    <?php else: ?>
        <!-- Global Fonts & Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Flatpickr for allotment pages -->
        <?php if ($page === 'allotment.php' || $page === 'allotment-history.php'): ?>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
        <?php endif; ?>
        
        <!-- Dynamic Stylesheet -->
        <?php
        $style_map = [
            'dashboard.php' => 'css/dashboard-styles.css',
            'allotment.php' => 'css/allotment-styles.css',
            'allotment-history.php' => 'css/allotment-history-styles.css',
            'book.php' => 'css/book-styles.css',
            'student.php' => 'css/student-styles.css',
            'publication.php' => 'css/publication-styles.css',
            'purchase.php' => 'css/purchase-styles.css',
            'vendor.php' => 'css/vendor-styles.css',
            'subscription.php' => 'css/subscription-styles.css',
            'session.php' => 'css/common.css',
        ];
        $style = $style_map[$page] ?? 'css/common.css';
        ?>
        <link rel="stylesheet" href="<?= h($style) ?>">
    <?php endif; ?>
</head>
<body>

<?php if (is_logged_in() && !$is_auth): ?>
<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="img/logo.jpg" alt="Logo" class="logo">
            <div class="header-text">
                <h3>Library</h3>
                <p>Management</p>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="dashboard.php" class="nav-item <?= $page === 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="allotment.php" class="nav-item <?= $page === 'allotment.php' ? 'active' : '' ?>">
                <i class="fas fa-bookmark"></i>
                <span>Books Allotment</span>
            </a>

            <?php 
            $is_books_mgmt = ($page === 'book.php' || $page === 'allotment-history.php');
            ?>
            <div class="nav-item-group">
                <a href="#" class="nav-item group-toggle <?= $is_books_mgmt ? 'active open' : '' ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Books Management</span>
                    <i class="fas fa-chevron-up toggle-icon <?= $is_books_mgmt ? 'rotated' : '' ?>"></i>
                </a>
                <div class="submenu <?= $is_books_mgmt ? 'show' : '' ?>">
                    <a href="book.php" class="submenu-item <?= $page === 'book.php' ? 'active' : '' ?>">
                        <i class="fas fa-book"></i>
                        <span>Manage Books</span>
                    </a>
                    <a href="allotment-history.php" class="submenu-item <?= $page === 'allotment-history.php' ? 'active' : '' ?>">
                        <i class="fas fa-history"></i>
                        <span>Allotment History</span>
                    </a>
                </div>
            </div>

            <a href="student.php" class="nav-item <?= $page === 'student.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Student Management</span>
            </a>
            <a href="purchase.php" class="nav-item <?= $page === 'purchase.php' ? 'active' : '' ?>">
                <i class="fas fa-shopping-bag"></i>
                <span>Purchase Books</span>
            </a>
            <a href="vendor.php" class="nav-item <?= $page === 'vendor.php' ? 'active' : '' ?>">
                <i class="fas fa-building"></i>
                <span>Vendor Management</span>
            </a>
            <a href="publication.php" class="nav-item <?= $page === 'publication.php' ? 'active' : '' ?>">
                <i class="fas fa-newspaper"></i>
                <span>Publications</span>
            </a>
            <a href="subscription.php" class="nav-item <?= $page === 'subscription.php' ? 'active' : '' ?>">
                <i class="fas fa-file-invoice"></i>
                <span>Subscription Type</span>
            </a>
            <a href="session.php" class="nav-item <?= $page === 'session.php' ? 'active' : '' ?>">
                <i class="fas fa-key"></i>
                <span>Session Demo</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="logout.php" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Welcome back, <?= h(current_user_name()) ?>!</h1>
            </div>
            <div class="header-right">
                <span class="date"></span>
                <button class="cart-btn" onclick="window.location.href='purchase.php'">
                    <i class="fas fa-shopping-cart"></i>
                </button>
            </div>
        </header>

        <!-- Content Area -->
        <section class="content-area">
<?php endif; ?>

<?php if ($flash): ?>
    <div class="toast">
        <span class="toast-message"><?= h($flash['message']) ?></span>
    </div>
<?php endif; ?>
<?php
}

function render_footer(): void
{
    $page = current_page();
    $is_auth = ($page === 'login.php' || $page === 'signup.php');
    ?>

<?php if (is_logged_in() && !$is_auth): ?>
        </section> <!-- .content-area -->
    </main> <!-- .main-content -->
</div> <!-- .container -->
<?php endif; ?>

<?php if ($is_auth): ?>
    <!-- Core JS -->
    <script src="vendor/libs/jquery/jquery.js"></script>
    <script src="vendor/libs/popper/popper.js"></script>
    <script src="vendor/js/bootstrap.js"></script>
    <script src="vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="js/main.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
<?php else: ?>
    <!-- Custom Scripts -->
    <?php if ($page === 'allotment.php' || $page === 'allotment-history.php'): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <?php endif; ?>

    <?php
    $script_map = [
        'dashboard.php' => 'js/dashboard-script.js',
        'allotment.php' => 'js/allotment-script.js',
        'allotment-history.php' => 'js/allotment-history-script.js',
        'book.php' => 'js/book-script.js',
        'student.php' => 'js/student-script.js',
        'publication.php' => 'js/publication-script.js',
        'purchase.php' => 'js/purchase-script.js',
        'vendor.php' => 'js/vendor-script.js',
        'subscription.php' => 'js/subscription-script.js',
    ];
    $script = $script_map[$page] ?? null;
    if ($script):
    ?>
        <script src="<?= h($script) ?>"></script>
    <?php endif; ?>
<?php endif; ?>

<!-- Keep delete confirmation functionality from original js/app.js -->
<script src="js/app.js"></script>
</body>
</html>
<?php
}

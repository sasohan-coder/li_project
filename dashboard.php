<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

$counts = [
    'books' => (int) fetch_one('SELECT COUNT(*) AS total FROM books')['total'],
    'students' => (int) fetch_one('SELECT COUNT(*) AS total FROM students')['total'],
    'vendors' => (int) fetch_one('SELECT COUNT(*) AS total FROM vendors')['total'],
    'allotments' => (int) fetch_one('SELECT COUNT(*) AS total FROM allotments')['total'],
];

$available_books = (int) fetch_one("SELECT COALESCE(SUM(available_quantity), 0) AS total FROM books")['total'];
$allotted_books = $counts['allotments'];

render_header('Dashboard');
?>
<!-- Welcome Section -->
<div class="welcome-section">
    <div class="welcome-content">
        <h2>Library Management System</h2>
        <p>Manage your library efficiently with our comprehensive management system</p>
    </div>
</div>

<!-- Key Metrics -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon books">
            <i class="fas fa-book"></i>
        </div>
        <div class="metric-content">
            <p class="metric-label">Total Books</p>
            <p class="metric-value"><?= $counts['books'] ?></p>
            <p class="metric-detail">In your library</p>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon students">
            <i class="fas fa-users"></i>
        </div>
        <div class="metric-content">
            <p class="metric-label">Active Students</p>
            <p class="metric-value"><?= $counts['students'] ?></p>
            <p class="metric-detail">Registered students</p>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon vendors">
            <i class="fas fa-building"></i>
        </div>
        <div class="metric-content">
            <p class="metric-label">Vendors</p>
            <p class="metric-value"><?= $counts['vendors'] ?></p>
            <p class="metric-detail">Book suppliers</p>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon allotments">
            <i class="fas fa-share-alt"></i>
        </div>
        <div class="metric-content">
            <p class="metric-label">Active Allotments</p>
            <p class="metric-value"><?= $counts['allotments'] ?></p>
            <p class="metric-detail">Currently allotted</p>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-section">
    <div class="chart-card">
        <div class="chart-header">
            <h3>Books Overview</h3>
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="chart-content">
            <div class="overview-stats">
                <div class="stat-item">
                    <div class="stat-label">
                        <div class="stat-indicator available"></div>
                        <span>Available Books</span>
                    </div>
                    <div class="stat-bar">
                        <div class="stat-fill" id="availableFill" style="width: 0%;">
                            <span id="availablePercent">0%</span>
                        </div>
                    </div>
                    <div class="stat-value"><?= $available_books ?> books</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">
                        <div class="stat-indicator allotted"></div>
                        <span>Allotted Books</span>
                    </div>
                    <div class="stat-bar">
                        <div class="stat-fill allotted" id="allottedFill" style="width: 0%;">
                            <span id="allottedPercent">0%</span>
                        </div>
                    </div>
                    <div class="stat-value"><?= $allotted_books ?> books</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions-section">
    <h3>Quick Actions</h3>
    <div class="actions-grid">
        <a href="book.php" class="action-card">
            <div class="action-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-content">
                <h4>Manage Books</h4>
                <p>Add, edit or delete books</p>
            </div>
            <i class="fas fa-arrow-right"></i>
        </a>

        <a href="allotment.php" class="action-card">
            <div class="action-icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="action-content">
                <h4>Allot Books</h4>
                <p>Allot books to students</p>
            </div>
            <i class="fas fa-arrow-right"></i>
        </a>

        <a href="student.php" class="action-card">
            <div class="action-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="action-content">
                <h4>Students</h4>
                <p>Manage student information</p>
            </div>
            <i class="fas fa-arrow-right"></i>
        </a>

        <a href="vendor.php" class="action-card">
            <div class="action-icon">
                <i class="fas fa-store"></i>
            </div>
            <div class="action-content">
                <h4>Vendors</h4>
                <p>Manage book suppliers</p>
            </div>
            <i class="fas fa-arrow-right"></i>
        </a>

        <a href="purchase.php" class="action-card">
            <div class="action-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="action-content">
                <h4>Purchase Books</h4>
                <p>Record book purchases</p>
            </div>
            <i class="fas fa-arrow-right"></i>
        </a>

        <a href="allotment-history.php" class="action-card">
            <div class="action-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="action-content">
                <h4>Allotment History</h4>
                <p>View allotment records</p>
            </div>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
<?php render_footer(); ?>

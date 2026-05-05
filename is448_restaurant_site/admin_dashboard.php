<?php
// File: admin_dashboard.php
// Admin home page / dashboard

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/includes/admin_header.php';

// Get some basic counts
$categoryCount = (int)$pdo->query("SELECT COUNT(*) FROM t_IS448_F25_categories")->fetchColumn();
$productCount = (int)$pdo->query("SELECT COUNT(*) FROM t_IS448_F25_products")->fetchColumn();
$contactCount = (int)$pdo->query("SELECT COUNT(*) FROM t_IS448_F25_contact_messages")->fetchColumn();
?>
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Admin Dashboard</h1>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h5 class="card-title">Categories</h5>
                <p class="card-text display-6"><?php echo $categoryCount; ?></p>
                <a href="admin_categories.php" class="btn btn-light btn-sm">Manage Categories</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-success">
            <div class="card-body">
                <h5 class="card-title">Products</h5>
                <p class="card-text display-6"><?php echo $productCount; ?></p>
                <a href="admin_products.php" class="btn btn-light btn-sm">Manage Products</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h5 class="card-title">Contact Messages</h5>
                <p class="card-text display-6"><?php echo $contactCount; ?></p>
                <a href="admin_contacts.php" class="btn btn-light btn-sm">View Messages</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>

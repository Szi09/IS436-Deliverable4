<?php
// File: includes/admin_header.php
// Admin site header and navigation

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/functions.php';

$siteSettings = get_site_settings($pdo);

if (!isset($pageTitle)) {
    $pageTitle = 'Admin - Restaurant Store';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- File: includes/admin_header.php -->
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.php">

    <style>
        body {
            background-color: <?php echo htmlspecialchars($siteSettings['color_body_bg']); ?>;
        }
        header.site-header {
            background-color: <?php echo htmlspecialchars($siteSettings['color_header_bg']); ?>;
        }
        footer.site-footer {
            background-color: <?php echo htmlspecialchars($siteSettings['color_footer_bg']); ?>;
        }
    </style>
</head>
<body>
<header class="site-header mb-4">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_categories.php">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_contacts.php">Contacts</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_settings.php">Site Colors</a></li>
                </ul>
                <span class="navbar-text me-3">
                    Logged in as <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="admin_logout.php">Logout</a>
            </div>
        </div>
    </nav>
</header>
<main class="container mb-5">

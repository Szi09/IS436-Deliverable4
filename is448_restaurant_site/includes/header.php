<?php
// File: includes/header.php
// Public site header, navigation, and opening HTML tags

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/functions.php';

$siteSettings = get_site_settings($pdo);
$categories = get_all_categories($pdo);

// Simple page title variable
if (!isset($pageTitle)) {
    $pageTitle = 'Restaurant Store';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- File: includes/header.php -->
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS via CDN for simple, modern styling -->
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
        h1 {
            color: <?php echo htmlspecialchars($siteSettings['color_h1']); ?>;
        }
        h2 {
            color: <?php echo htmlspecialchars($siteSettings['color_h2']); ?>;
        }
        h3 {
            color: <?php echo htmlspecialchars($siteSettings['color_h3']); ?>;
        }
        p {
            color: <?php echo htmlspecialchars($siteSettings['color_p']); ?>;
        }
    </style>
</head>
<body>
<header class="site-header mb-4">
    <nav class="navbar navbar-expand-lg navbar-light container">
        <a class="navbar-brand fw-bold" href="index.php">Restaurant Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <?php if (!empty($categories)): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarCategoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Menu Categories
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarCategoryDropdown">
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a class="dropdown-item" href="index.php?category_id=<?php echo (int)$cat['id']; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>
            <form class="d-flex" method="get" action="index.php">
                <input class="form-control me-2" type="search" name="q" placeholder="Search dishes..." aria-label="Search" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
        </div>
    </nav>
</header>
<main class="container mb-5">

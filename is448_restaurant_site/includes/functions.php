<?php
// File: includes/functions.php
// Common helper functions for IS448 Restaurant Store Website

require_once __DIR__ . '/db_connect.php';

/**
 * Get site color settings. Returns associative array of colors.
 */
function get_site_settings(PDO $pdo): array {
    $defaults = [
        'color_h1' => '#333333',
        'color_h2' => '#444444',
        'color_h3' => '#555555',
        'color_p' => '#333333',
        'color_header_bg' => '#f8f9fa',
        'color_body_bg' => '#ffffff',
        'color_footer_bg' => '#f8f9fa'
    ];

    try {
        $stmt = $pdo->query("SELECT * FROM t_IS448_F25_site_settings LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return array_merge($defaults, $row);
        }
    } catch (Exception $e) {
        // On error, just fall back to defaults
    }

    return $defaults;
}

/**
 * Get all categories, ordered by name.
 */
function get_all_categories(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM t_IS448_F25_categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get products. Optionally filter by search term or category id.
 */
function get_products(PDO $pdo, ?string $search = null, ?int $category_id = null): array {
    $sql = "SELECT p.*, c.name AS category_name
            FROM t_IS448_F25_products p
            LEFT JOIN t_IS448_F25_categories c ON p.category_id = c.id
            WHERE 1=1";
    $params = [];

    if ($search !== null && $search !== '') {
        $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if ($category_id !== null) {
        $sql .= " AND p.category_id = :cid";
        $params[':cid'] = $category_id;
    }

    $sql .= " ORDER BY p.name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single product by id.
 */
function get_product_by_id(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name
                           FROM t_IS448_F25_products p
                           LEFT JOIN t_IS448_F25_categories c ON p.category_id = c.id
                           WHERE p.id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}
?>

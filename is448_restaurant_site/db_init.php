<?php
// File: db_init.php
// Helper script to create database tables for IS448 Restaurant Store Website.
// Run this once after configuring db_connect.php with your database credentials.

require_once __DIR__ . '/includes/db_connect.php';

//use is448_final_project;

$sqlStatements = [

"CREATE TABLE IF NOT EXISTS t_IS448_F25_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"CREATE TABLE IF NOT EXISTS t_IS448_F25_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"CREATE TABLE IF NOT EXISTS t_IS448_F25_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    category_id INT NOT NULL,
    image_name VARCHAR(255) NULL,
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES t_IS448_F25_categories(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"CREATE TABLE IF NOT EXISTS t_IS448_F25_contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"CREATE TABLE IF NOT EXISTS t_IS448_F25_site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    color_h1 VARCHAR(7) NOT NULL DEFAULT '#333333',
    color_h2 VARCHAR(7) NOT NULL DEFAULT '#444444',
    color_h3 VARCHAR(7) NOT NULL DEFAULT '#555555',
    color_p VARCHAR(7) NOT NULL DEFAULT '#333333',
    color_header_bg VARCHAR(7) NOT NULL DEFAULT '#f8f9fa',
    color_body_bg VARCHAR(7) NOT NULL DEFAULT '#ffffff',
    color_footer_bg VARCHAR(7) NOT NULL DEFAULT '#f8f9fa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

];

try {
    foreach ($sqlStatements as $sql) {
        $pdo->exec($sql);
    }

    // Insert default admin user if none exist
    $count = (int)$pdo->query("SELECT COUNT(*) FROM t_IS448_F25_admins")->fetchColumn();
    if ($count === 0) {
        $defaultUsername = 'admin';
        $defaultPassword = 'admin123'; // You can change this after first login
        $hash = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO t_IS448_F25_admins (username, password_hash) VALUES (:u, :p)");
        $stmt->execute([':u' => $defaultUsername, ':p' => $hash]);
    }

    echo '<p>Database tables created or verified successfully.</p>';
    echo '<p>Default admin login: <strong>admin</strong> / <strong>admin123</strong>.</p>';
} catch (Exception $e) {
    echo '<p>Error creating tables: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>

<?php
// File: product_detail.php
// Public product detail page

$pageTitle = 'Product Details';
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo '<div class="alert alert-danger">Invalid product.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name
                       FROM t_IS448_F25_products p
                       LEFT JOIN t_IS448_F25_categories c ON p.category_id = c.id
                       WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo '<div class="alert alert-danger">Product not found.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>
<div class="row">
    <div class="col-md-6">
        <?php if (!empty($product['image_name'])): ?>
            <img src="images/<?php echo htmlspecialchars($product['image_name']); ?>" class="img-fluid mb-3" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php else: ?>
            <div class="border bg-light text-center py-5 mb-3">
                <span class="text-muted">No image available</span>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <?php if (!empty($product['category_name'])): ?>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
        <?php endif; ?>
        <p><strong>Price:</strong> $<?php echo number_format((float)$product['price'], 2); ?></p>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <a href="index.php" class="btn btn-secondary">Back to Menu</a>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

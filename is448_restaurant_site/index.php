<?php
// File: index.php
// Public site home page - shows products with search and category filter

$pageTitle = 'Restaurant Store - Home';
require_once __DIR__ . '/includes/header.php';

// Read search and category filters
$search = isset($_GET['q']) ? trim($_GET['q']) : null;
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

// Build product list directly here using PDO
$sql = "SELECT p.*, c.name AS category_name
        FROM t_IS448_F25_products p
        LEFT JOIN t_IS448_F25_categories c ON p.category_id = c.id
        WHERE 1=1";
$params = [];

if ($search !== null && $search !== '') {
    $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
if ($categoryId !== null && $categoryId > 0) {
    $sql .= " AND p.category_id = :cid";
    $params[':cid'] = $categoryId;
}
$sql .= " ORDER BY p.name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="row mb-4">
    <div class="col">
        <h1 class="mb-3">Welcome to Our Restaurant Store</h1>
        <p>Browse our delicious menu items. Use the search bar or the category menu to find your favorite dishes.</p>
    </div>
</div>

<?php if ($search): ?>
    <div class="alert alert-info">
        Search results for: <strong><?php echo htmlspecialchars($search); ?></strong>
    </div>
<?php endif; ?>

<div class="row">
    <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-warning">No products found.</div>
        </div>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($product['image_name'])): ?>
                        <img src="images/<?php echo htmlspecialchars($product['image_name']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <?php if (!empty($product['category_name'])): ?>
                            <h6 class="card-subtitle mb-2 text-muted">
                                Category: <?php echo htmlspecialchars($product['category_name']); ?>
                            </h6>
                        <?php endif; ?>
                        <p class="card-text flex-grow-1">
                            <?php echo nl2br(htmlspecialchars(substr($product['description'], 0, 120))); ?>...
                        </p>
                        <p class="fw-bold mb-2">
                            $<?php echo number_format((float)$product['price'], 2); ?>
                        </p>
                        <a href="product_detail.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-primary mt-auto">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

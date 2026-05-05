<?php
// File: admin_products.php
// Manage products (add, edit, delete)

$pageTitle = 'Manage Products';
require_once __DIR__ . '/includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$action = in_array($action, ['list', 'add', 'edit', 'delete'], true) ? $action : 'list';
$message = '';

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $imageName = trim($_POST['image_name'] ?? '');

    if ($name === '' || $price <= 0 || $categoryId <= 0) {
        $message = 'Name, positive price, and category are required.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO t_IS448_F25_products (name, description, price, category_id, image_name)
                               VALUES (:name, :description, :price, :category_id, :image_name)");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':category_id' => $categoryId,
            ':image_name' => $imageName
        ]);
        header('Location: admin_products.php?msg=Product+added');
        exit;
    }
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $imageName = trim($_POST['image_name'] ?? '');

    if ($id <= 0 || $name === '' || $price <= 0 || $categoryId <= 0) {
        $message = 'Valid product, name, price, and category are required.';
    } else {
        $stmt = $pdo->prepare("UPDATE t_IS448_F25_products
                               SET name = :name, description = :description, price = :price, category_id = :category_id, image_name = :image_name
                               WHERE id = :id");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':category_id' => $categoryId,
            ':image_name' => $imageName,
            ':id' => $id
        ]);
        header('Location: admin_products.php?msg=Product+updated');
        exit;
    }
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM t_IS448_F25_products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: admin_products.php?msg=Product+deleted');
        exit;
    }
    $action = 'list';
}

// Fetch categories for forms
$categoryStmt = $pdo->query("SELECT * FROM t_IS448_F25_categories ORDER BY name");
$allCategories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Manage Products</h1>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
</div>

<?php if ($action === 'list'): ?>
    <div class="mb-3">
        <a href="admin_products.php?action=add" class="btn btn-primary">Add Product</a>
    </div>
    <?php
    $stmt = $pdo->query("SELECT p.*, c.name AS category_name
                         FROM t_IS448_F25_products p
                         LEFT JOIN t_IS448_F25_categories c ON p.category_id = c.id
                         ORDER BY p.name");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Image Name</th>
            <th style="width: 140px;">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['category_name']); ?></td>
                <td>$<?php echo number_format((float)$p['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($p['image_name']); ?></td>
                <td>
                    <a href="admin_products.php?action=edit&id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <a href="admin_products.php?action=delete&id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-danger delete-link">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif ($action === 'add'): ?>
    <form method="post" action="admin_products.php?action=add" class="card card-body">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name*</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Category*</label>
            <select id="category_id" name="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($allCategories as $cat): ?>
                    <option value="<?php echo (int)$cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price (e.g. 9.99)*</label>
            <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="image_name" class="form-label">Image File Name (e.g. pizza.jpg)</label>
            <input type="text" id="image_name" name="image_name" class="form-control">
            <div class="form-text">Upload the image manually via FTP to the <code>images</code> folder.</div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
        <a href="admin_products.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>

<?php elseif ($action === 'edit'):
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM t_IS448_F25_products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product): ?>
        <div class="alert alert-danger">Product not found.</div>
    <?php else: ?>
        <form method="post" action="admin_products.php?action=edit" class="card card-body">
            <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name*</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category*</label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($allCategories as $cat): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price (e.g. 9.99)*</label>
                <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="image_name" class="form-label">Image File Name (e.g. pizza.jpg)</label>
                <input type="text" id="image_name" name="image_name" class="form-control" value="<?php echo htmlspecialchars($product['image_name']); ?>">
                <div class="form-text">Upload the image manually via FTP to the <code>images</code> folder.</div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="admin_products.php" class="btn btn-secondary ms-2">Cancel</a>
        </form>
    <?php endif; ?>
<?php endif; ?>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>

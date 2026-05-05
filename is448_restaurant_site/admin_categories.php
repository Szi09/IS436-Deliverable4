<?php
// File: admin_categories.php
// Manage menu categories (add, edit, delete)

$pageTitle = 'Manage Categories';
require_once __DIR__ . '/includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$action = in_array($action, ['list', 'add', 'edit', 'delete'], true) ? $action : 'list';
$message = '';

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if ($name === '') {
        $message = 'Name is required.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO t_IS448_F25_categories (name, description) VALUES (:name, :description)");
        $stmt->execute([':name' => $name, ':description' => $description]);
        header('Location: admin_categories.php?msg=Category+added');
        exit;
    }
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if ($id <= 0 || $name === '') {
        $message = 'Valid category and name are required.';
    } else {
        $stmt = $pdo->prepare("UPDATE t_IS448_F25_categories SET name = :name, description = :description WHERE id = :id");
        $stmt->execute([':name' => $name, ':description' => $description, ':id' => $id]);
        header('Location: admin_categories.php?msg=Category+updated');
        exit;
    }
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM t_IS448_F25_categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: admin_categories.php?msg=Category+deleted');
        exit;
    }
    $action = 'list';
}

?>
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Manage Categories</h1>
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
        <a href="admin_categories.php?action=add" class="btn btn-primary">Add Category</a>
    </div>
    <?php
    $stmt = $pdo->query("SELECT * FROM t_IS448_F25_categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th style="width: 120px;">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($cat['description'])); ?></td>
                <td>
                    <a href="admin_categories.php?action=edit&id=<?php echo (int)$cat['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <a href="admin_categories.php?action=delete&id=<?php echo (int)$cat['id']; ?>" class="btn btn-sm btn-danger delete-link">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif ($action === 'add'): ?>
    <form method="post" action="admin_categories.php?action=add" class="card card-body">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name*</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Category</button>
        <a href="admin_categories.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>

<?php elseif ($action === 'edit'):
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM t_IS448_F25_categories WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $cat = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cat): ?>
        <div class="alert alert-danger">Category not found.</div>
    <?php else: ?>
        <form method="post" action="admin_categories.php?action=edit" class="card card-body">
            <input type="hidden" name="id" value="<?php echo (int)$cat['id']; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name*</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($cat['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($cat['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="admin_categories.php" class="btn btn-secondary ms-2">Cancel</a>
        </form>
    <?php endif; ?>
<?php endif; ?>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>

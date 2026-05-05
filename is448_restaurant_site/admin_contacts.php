<?php
// File: admin_contacts.php
// View contact messages submitted from the public site

$pageTitle = 'Contact Messages';
require_once __DIR__ . '/includes/admin_header.php';

$stmt = $pdo->query("SELECT * FROM t_IS448_F25_contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Contact Messages</h1>
    </div>
</div>
<?php if (empty($messages)): ?>
    <div class="alert alert-info">No messages yet.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($messages as $m): ?>
                <tr>
                    <td><?php echo htmlspecialchars($m['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($m['name']); ?></td>
                    <td><?php echo htmlspecialchars($m['email']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($m['message'])); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>

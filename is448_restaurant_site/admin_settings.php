<?php
// File: admin_settings.php
// Manage site colors for headers, paragraphs, and layout

$pageTitle = 'Site Color Settings';
require_once __DIR__ . '/includes/admin_header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $color_h1 = $_POST['color_h1'] ?? '#333333';
    $color_h2 = $_POST['color_h2'] ?? '#444444';
    $color_h3 = $_POST['color_h3'] ?? '#555555';
    $color_p = $_POST['color_p'] ?? '#333333';
    $color_header_bg = $_POST['color_header_bg'] ?? '#f8f9fa';
    $color_body_bg = $_POST['color_body_bg'] ?? '#ffffff';
    $color_footer_bg = $_POST['color_footer_bg'] ?? '#f8f9fa';

    // Simple upsert: if row exists, update; otherwise, insert
    $existing = $pdo->query("SELECT id FROM t_IS448_F25_site_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        $stmt = $pdo->prepare("UPDATE t_IS448_F25_site_settings
                               SET color_h1 = :color_h1,
                                   color_h2 = :color_h2,
                                   color_h3 = :color_h3,
                                   color_p = :color_p,
                                   color_header_bg = :color_header_bg,
                                   color_body_bg = :color_body_bg,
                                   color_footer_bg = :color_footer_bg
                               WHERE id = :id");
        $stmt->execute([
            ':color_h1' => $color_h1,
            ':color_h2' => $color_h2,
            ':color_h3' => $color_h3,
            ':color_p' => $color_p,
            ':color_header_bg' => $color_header_bg,
            ':color_body_bg' => $color_body_bg,
            ':color_footer_bg' => $color_footer_bg,
            ':id' => $existing['id']
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO t_IS448_F25_site_settings
            (color_h1, color_h2, color_h3, color_p, color_header_bg, color_body_bg, color_footer_bg)
            VALUES (:color_h1, :color_h2, :color_h3, :color_p, :color_header_bg, :color_body_bg, :color_footer_bg)");
        $stmt->execute([
            ':color_h1' => $color_h1,
            ':color_h2' => $color_h2,
            ':color_h3' => $color_h3,
            ':color_p' => $color_p,
            ':color_header_bg' => $color_header_bg,
            ':color_body_bg' => $color_body_bg,
            ':color_footer_bg' => $color_footer_bg
        ]);
    }

    $message = 'Site colors updated.';
    $siteSettings = get_site_settings($pdo);
} else {
    $siteSettings = get_site_settings($pdo);
}
?>
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Site Color Settings</h1>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
</div>

<form method="post" action="admin_settings.php" class="card card-body mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="color_h1" class="form-label">H1 Color</label>
            <input type="color" id="color_h1" name="color_h1" class="form-control form-control-color" value="<?php echo htmlspecialchars($siteSettings['color_h1']); ?>">
        </div>
        <div class="col-md-4">
            <label for="color_h2" class="form-label">H2 Color</label>
            <input type="color" id="color_h2" name="color_h2" class="form-control form-control-color" value="<?php echo htmlspecialchars($siteSettings['color_h2']); ?>">
        </div>
        <div class="col-md-4">
            <label for="color_h3" class="form-label">H3 Color</label>
            <input type="color" id="color_h3" name="color_h3" class="form-control form-control-color" value="<?php echo htmlspecialchars($siteSettings['color_h3']); ?>">
        </div>
        <div class="col-md-4">
            <label for="color_p" class="form-label">Paragraph (p) Color</label>
            <input type="color" id="color_p" name="color_p" class="form-control form-control-color" value="<?php echo htmlspecialchars($siteSettings['color_p']); ?>">
        </div>
        <div class="col-md-4">
            <label for="color_header_bg" class="form-label">Header Background</label>
            <input type="color" id="color_header_bg" name="color_header_bg" class="form-control form-control-color" value="<?php echo htmlspecialchars($siteSettings['color_header_bg']); ?>">
        </div>
        <div class="col-md-4">
            <label for="color_body_bg" class="form-label">Body Background</label>
            <input type="color" id="color_body_bg" name="color_body_bg" class="form-control form-control-color" value="<?php echo htmlspecialchars($siteSettings['color_body_bg']); ?>">
        </div>
        <div class="col-md-4">
            <label for="color_footer_bg" class="form-label">Footer Background</label>
            <input type="color" id="color_footer_bg" name="color_footer_bg" class="form-control form-control-color" value="<?php echo htmlspecialchars($siteSettings['color_footer_bg']); ?>">
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Save Colors</button>
</form>

<div class="card card-body">
    <h2 class="h4">Preview</h2>
    <p>This preview uses the same colors that will be applied to the public site.</p>
    <h1>Sample H1 Heading</h1>
    <h2>Sample H2 Heading</h2>
    <h3>Sample H3 Heading</h3>
    <p>Sample paragraph text to show the color selection in action.</p>
</div>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>

<?php
// File: contact.php
// Public contact page to send a message to the restaurant

$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/header.php';

$name = '';
$email = '';
$message = '';
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if ($message === '') {
        $errors[] = 'Message is required.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO t_IS448_F25_contact_messages (name, email, message, created_at)
                               VALUES (:name, :email, :message, NOW())");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message
        ]);
        $success = true;
        $name = $email = $message = '';
    }
}
?>
<div class="row">
    <div class="col-md-8">
        <h1>Contact Us</h1>
        <p>If you have any questions about our menu or services, please send us a message.</p>

        <?php if ($success): ?>
            <div class="alert alert-success">Thank you! Your message has been sent.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="contact.php" class="mb-4">
            <div class="mb-3">
                <label for="name" class="form-label">Your Name*</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Your Email*</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message*</label>
                <textarea id="message" name="message" rows="5" class="form-control" required><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
    <div class="col-md-4">
        <h2>Our Location</h2>
        <p>
            123 Main Street<br>
            Your City, MD 21234<br>
            Phone: (555) 123-4567
        </p>
        <p>
            Business Hours:<br>
            Mon-Fri: 11:00 AM - 10:00 PM<br>
            Sat-Sun: 12:00 PM - 11:00 PM
        </p>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

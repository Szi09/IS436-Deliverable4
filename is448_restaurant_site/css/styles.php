<?php
// File: css/styles.php
// Basic CSS styles for the Restaurant Store Website
header('Content-Type: text/css');
?>
/* Base layout */
body {
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

/* Make cards the same height */
.card {
    border-radius: 0.75rem;
}

/* Footer */
.site-footer {
    border-top: 1px solid #dddddd;
}

/* Admin cards */
.card.text-bg-primary,
.card.text-bg-success,
.card.text-bg-warning {
    border-radius: 0.75rem;
}

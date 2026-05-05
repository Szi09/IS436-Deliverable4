<?php
// File: js/main.php
// Basic JavaScript for Restaurant Store Website (confirm delete links)
header('Content-Type: application/javascript');
?>
document.addEventListener('DOMContentLoaded', function () {
    var deleteLinks = document.querySelectorAll('.delete-link');
    deleteLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            if (!confirm('Are you sure you want to delete this item?')) {
                event.preventDefault();
            }
        });
    });
});

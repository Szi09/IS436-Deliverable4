<?php
// File: admin_logout.php
// Log the admin out and destroy the session

session_start();
$_SESSION = [];
session_destroy();

header('Location: admin_login.php');
exit;
?>

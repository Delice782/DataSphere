<?php
require_once '../includes/session.php';

// Destroy all session data
session_start();
$_SESSION = array();
session_destroy();

// Redirect to login page with logout message
header("Location: ../views/login.php?logout=1");
exit();
?>
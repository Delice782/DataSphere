        
<?php
require_once '../includes/session.php';

// Destroy all session data
session_start();

// Store a temporary session variable to indicate logout
session_regenerate_id(true);
$_SESSION = array();
$_SESSION['temp_logout'] = true;

// Destroy the current session
session_destroy();

// Start a new temporary session just for the logout message
session_start();
$_SESSION['temp_logout'] = true;

// Redirect to login page without the parameter
header("Location: ../views/login.php");
exit();
?>

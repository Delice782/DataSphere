<?php
// Database configuration
$servername = "localhost";
$username = "datasphere_user"; // Default XAMPP username
$password = "Developer@123"; // Default XAMPP password (blank)
$dbname = "datasphere";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

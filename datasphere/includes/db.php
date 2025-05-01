<?php
// Database configuration
$servername = "localhost";
$username = "datasphere_user"; 
$password = "Developer@123";
$dbname = "datasphere";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

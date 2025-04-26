

<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (blank)
$dbname = "datasphere";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Handles profile update

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $description = $_POST['description'];
    $userId = $_SESSION['user_id']; // Assuming user ID is stored in session


    $stmt = $conn->prepare("UPDATE users SET username=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $description, $userId);
    $stmt->execute();
    $stmt->close();


    $_SESSION['message'] = "Profile updated successfully!";

}


// Handles account deletion

if (isset($_POST['delete_account'])) {
    $userId = $_SESSION['user_id'];


    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();


    session_destroy();
    header("Location: header.php"); // Redirect to a goodbye page
    exit();

}


// Fetch user data
$userId = $_SESSION['user_id'];
$result = $conn->query("SELECT username, description FROM users WHERE id='$userId'");
$userData = $result->fetch_assoc();


$conn->close();

?>

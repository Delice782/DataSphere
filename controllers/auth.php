<?php
require_once '../includes/db.php';
require_once '../includes/session.php';

// Get action from form
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Handle different form submissions
switch ($action) {
    case 'signup':
        handleSignup($conn);
        break;
    case 'login':
        handleLogin($conn);
        break;
    default:
        // Redirect to home if no valid action
        header("Location: ../views/home.php");
        exit();
}

// Handle user registration
function handleSignup($conn) {
    // Get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : 'Customer'; // Default to Customer
    
    // Validate form data
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        redirect_with_error("signup.php", "All fields are required");
    }
    
    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect_with_error("signup.php", "Invalid email format");
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        redirect_with_error("signup.php", "Passwords do not match");
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT userID FROM User WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        redirect_with_error("signup.php", "Email already registered");
    }
    
    // Generate a new user ID (find max and add 1)
    $result = $conn->query("SELECT MAX(userID) as maxID FROM User");
    $row = $result->fetch_assoc();
    $newUserID = ($row['maxID'] ?? 0) + 1;
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO User (userID, name, email, role, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $newUserID, $name, $email, $role, $hashed_password);
    
    if ($stmt->execute()) {
        // Registration successful
        header("Location: ../views/signup.php?success=1");
        exit();
    } else {
        // Registration failed
        redirect_with_error("signup.php", "Registration failed: " . $conn->error);
    }
}

// Handle user login
function handleLogin($conn) {
    // Get form data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate form data
    if (empty($email) || empty($password)) {
        redirect_with_error("login.php", "All fields are required");
    }
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT userID, name, email, role, password FROM User WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Login successful, set session variables
            $_SESSION['user_id'] = $user['userID'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect to dashboard
            header("Location: ../views/dashboard.php");
            exit();
        } else {
            // Invalid password
            redirect_with_error("login.php", "Invalid email or password");
        }
    } else {
        // User not found
        redirect_with_error("login.php", "Invalid email or password");
    }
}

// Helper function to redirect with error message
function redirect_with_error($page, $message) {
    header("Location: ../views/". $page . "?error=" . urlencode($message));
    exit();
}
?>
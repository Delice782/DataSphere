<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once '../includes/db.php';

// Function to redirect with message
function redirectWithMessage($location, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $location");
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirectWithMessage('../login.php', 'You must be logged in to perform this action.', 'error');
}

// Get the action type
$action = $_POST['action'] ?? '';

// Handle user deletion
if ($action === 'delete_user' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    // Prevent deleting yourself
    if ($user_id === $_SESSION['user_id']) {
        redirectWithMessage('manage_users.php', 'You cannot delete your own account.', 'error');
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
    
        // Delete notifications related to user's feedback
        $stmt = $conn->prepare("DELETE n FROM notification n 
                              JOIN feedback f ON n.feedbackID = f.feedbackID 
                              WHERE f.userID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete responses to user's feedback
        $stmt = $conn->prepare("DELETE r FROM response r 
                              JOIN feedback f ON r.feedbackID = f.feedbackID 
                              WHERE f.userID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete user's feedback
        $stmt = $conn->prepare("DELETE FROM feedback WHERE userID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete notifications sent to this user
        $stmt = $conn->prepare("DELETE FROM notification WHERE userID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete responses created by this user (if admin)
        $stmt = $conn->prepare("DELETE FROM response WHERE adminID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // delete the user
        $stmt = $conn->prepare("DELETE FROM user WHERE userID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction if all operations successful
        $conn->commit();
        redirectWithMessage('manage_users.php', 'User deleted successfully.', 'success');
    } catch (Exception $e) {
        // Rollback transaction if any operation fails
        $conn->rollback();
        redirectWithMessage('manage_users.php', 'Error deleting user: ' . $e->getMessage(), 'error');
    }
}

// Handle feedback deletion
elseif ($action === 'delete_feedback' && isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']);
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // First, delete related records
        // Delete notifications related to this feedback
        $stmt = $conn->prepare("DELETE FROM notification WHERE feedbackID = ?");
        $stmt->bind_param("i", $feedback_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete responses to this feedback
        $stmt = $conn->prepare("DELETE FROM response WHERE feedbackID = ?");
        $stmt->bind_param("i", $feedback_id);
        $stmt->execute();
        $stmt->close();
        
        // delete the feedback
        $stmt = $conn->prepare("DELETE FROM feedback WHERE feedbackID = ?");
        $stmt->bind_param("i", $feedback_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction if all operations successful
        $conn->commit();
        redirectWithMessage('manage_feedback.php', 'Feedback deleted successfully.', 'success');
    } catch (Exception $e) {
        // Rollback transaction if any operation fails
        $conn->rollback();
        redirectWithMessage('manage_feedback.php', 'Error deleting feedback: ' . $e->getMessage(), 'error');
    }
}

// Invalid or missing action
else {
    redirectWithMessage('../dashboard.php', 'Invalid delete request.', 'error');
}

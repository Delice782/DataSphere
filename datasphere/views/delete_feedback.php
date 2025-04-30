<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Require user to be logged in
requireLogin();

// Get the feedback ID from the URL
$feedbackID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userID = $_SESSION['user_id'];

if ($feedbackID > 0) {
    // First verify this feedback belongs to the current user
    $checkQuery = "SELECT status FROM feedback WHERE feedbackID = ? AND userID = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "ii", $feedbackID, $userID);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($result) > 0) {
        $feedback = mysqli_fetch_assoc($result);
        
        // Only allow deletion if status is 'pending'
        if ($feedback['status'] == 'pending') {
            // Delete the feedback
            $deleteQuery = "DELETE FROM feedback WHERE feedbackID = ?";
            $deleteStmt = mysqli_prepare($conn, $deleteQuery);
            mysqli_stmt_bind_param($deleteStmt, "i", $feedbackID);
            
            if (mysqli_stmt_execute($deleteStmt)) {
                // Set success message
                $_SESSION['message'] = "Feedback has been successfully deleted.";
                $_SESSION['message_type'] = "success";
            } else {
                // Set error message
                $_SESSION['message'] = "Failed to delete feedback. Please try again.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            // Set error message for non-pending feedback
            $_SESSION['message'] = "Only pending feedback can be deleted.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        // Set error message for unauthorized access
        $_SESSION['message'] = "You don't have permission to delete this feedback.";
        $_SESSION['message_type'] = "error";
    }
}

// Redirect back to the feedback page
header("Location: my_feedback.php");
exit;
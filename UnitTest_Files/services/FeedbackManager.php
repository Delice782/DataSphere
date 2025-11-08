           
<?php
class FeedbackManager {
    private $conn;

    // Constructor to initialize the database connection
    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // Get the count of feedback for a user
    public function getFeedbackCount($user_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as feedback_count FROM feedback WHERE userID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data['feedback_count'];
    }

    // Get details of specific feedback
    public function getFeedbackDetails($feedbackID) {
        $stmt = $this->conn->prepare("SELECT f.feedbackID, f.userID, f.content, f.category, f.status, f.timestamp, u.username 
                                     FROM feedback f 
                                     JOIN user u ON f.userID = u.userID 
                                     WHERE f.feedbackID = ?");
        $stmt->bind_param("i", $feedbackID);
        $stmt->execute();
        $result = $stmt->get_result();
        $feedback = $result->fetch_assoc();
        $stmt->close();
        return $feedback;
    }

    // Submit feedback
    public function submitFeedback($userID, $userName, $content, $category, $feedbackCategories) {
        $response = [
            'success' => false,
            'message' => '',
            'clearForm' => false
        ];
    
        if (empty($content)) {
            $response['message'] = "Please enter your feedback";
        } elseif (empty($category) || !array_key_exists($category, $feedbackCategories)) {
            $response['message'] = "Please select a valid feedback category";
        } else {
            try {
                if (!$this->conn || $this->conn->connect_errno) {
                    throw new Exception("Database connection is closed or invalid");
                }

                $status = 'pending';
                $query = "INSERT INTO feedback (userID, content, category, timestamp, status)
                          VALUES (?, ?, ?, NOW(), ?)";
                $stmt = $this->conn->prepare($query);
                
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $this->conn->error);
                }

                mysqli_stmt_bind_param($stmt, "isss", $userID, $content, $category, $status);
    
                if ($stmt->execute()) {
                    $feedbackID = $this->conn->insert_id;
                    $response['success'] = true;
                    $response['message'] = "Feedback submitted successfully!";
                    $response['clearForm'] = true;
    
                    // Create admin notification
                    $adminID = 1;  // Assuming admin ID is 1
                    $notificationMessage = "New feedback submitted by " . $userName;
                    $notifyQuery = "INSERT INTO notification (userID, feedbackID, message, timestamp)
                                    VALUES (?, ?, ?, NOW())";
                    $notifyStmt = $this->conn->prepare($notifyQuery);
                    mysqli_stmt_bind_param($notifyStmt, "iis", $adminID, $feedbackID, $notificationMessage);
                    
                    if (!mysqli_stmt_execute($notifyStmt)) {
                        error_log("Failed to create notification: " . mysqli_error($this->conn));
                    }
                } else {
                    throw new Exception("Error executing statement: " . $stmt->error);
                }
            } catch (Exception $e) {
                $response['message'] = "Error submitting feedback: " . $e->getMessage();
                error_log("Exception: " . $e->getMessage());
            }
        }
    
        return $response;
    }

    // Delete feedback
    public function deleteFeedback($feedbackID, $userID) {
        $response = [
            'success' => false,
            'message' => '',
            'message_type' => 'error'
        ];
    
        if ($feedbackID <= 0) {
            $response['message'] = "Invalid feedback ID.";
            return $response;
        }
    
        // Verify feedback belongs to the current user
        $checkQuery = "SELECT status FROM feedback WHERE feedbackID = ? AND userID = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $feedbackID, $userID);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
    
        if (mysqli_num_rows($result) > 0) {
            $feedback = mysqli_fetch_assoc($result);
            if ($feedback['status'] === 'pending') {
                $deleteQuery = "DELETE FROM feedback WHERE feedbackID = ?";
                $deleteStmt = $this->conn->prepare($deleteQuery);
                $deleteStmt->bind_param("i", $feedbackID);
                if ($deleteStmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Feedback has been successfully deleted.";
                    $response['message_type'] = "success";
                } else {
                    $response['message'] = "Failed to delete feedback. Please try again.";
                }
            } else {
                $response['message'] = "Only pending feedback can be deleted.";
            }
        } else {
            $response['message'] = "You don't have permission to delete this feedback.";
        }
    
        return $response;
    }

        
}
?>




















                         
<?php
class ResponseLogic {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function respondToFeedback($feedback_id, $admin_id, $response_text) {
        if (empty($response_text)) {
            throw new InvalidArgumentException("Response text cannot be empty.");
        }

        $this->conn->begin_transaction();

        try {
            // Insert response
            $stmt = $this->conn->prepare("INSERT INTO response (feedbackID, adminID, responseText, timestamp) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $feedback_id, $admin_id, $response_text);
            $stmt->execute();
            $stmt->close();

            // Update feedback status
            $stmt = $this->conn->prepare("UPDATE feedback SET status = 'responded' WHERE feedbackID = ?");
            $stmt->bind_param("i", $feedback_id);
            $stmt->execute();
            $stmt->close();

            // Get user ID
            $stmt = $this->conn->prepare("SELECT userID FROM feedback WHERE feedbackID = ?");
            $stmt->bind_param("i", $feedback_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $feedback = $result->fetch_assoc();
            $stmt->close();

            if ($feedback) {
                $user_id = $feedback['userID'];
                $notification_msg = "Your feedback has received a response";
                $stmt = $this->conn->prepare("INSERT INTO notification (userID, feedbackID, message, timestamp) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iis", $user_id, $feedback_id, $notification_msg);
                $stmt->execute();
                $stmt->close();
            }

            $this->conn->commit();
            return ['success' => true, 'message' => 'Response submitted successfully.'];

        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Failed to respond to feedback: ' . $e->getMessage()];
        }
    }
}































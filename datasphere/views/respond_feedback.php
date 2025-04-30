<?php
$pageTitle = "Respond to Feedback";
$currentPage = "manage_feedback"; 
require_once '../includes/header.php';
require_once '../includes/db.php';

// Require user to be logged in
requireLogin();

// Initialize variables
$message = '';
$feedback = null;
$responses = [];
$feedback_id = null;

// Process feedback ID from post or session (avoiding URL exposure)
if (isset($_POST['feedback_id']) && is_numeric($_POST['feedback_id'])) {
    $feedback_id = (int)$_POST['feedback_id'];
    // Store feedback_id in session for future references
    $_SESSION['current_feedback_id'] = $feedback_id;
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // We still accept ID from GET but immediately store it in session
    // and redirect to remove it from URL
    $feedback_id = (int)$_GET['id'];
    $_SESSION['current_feedback_id'] = $feedback_id;
    
    // Redirect to same page without the ID parameter
    header("Location: respond_feedback.php");
    exit;
} elseif (isset($_SESSION['current_feedback_id'])) {
    // Retrieve from session 
    $feedback_id = $_SESSION['current_feedback_id'];
}

// Check if we have a valid feedback ID
if ($feedback_id === null) {
    $message = "Error: Invalid feedback ID";
} else {
    // Get feedback details
    $stmt = $conn->prepare("SELECT f.*, u.username FROM feedback f JOIN user u ON f.userID = u.userID WHERE f.feedbackID = ?");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $feedback = $result->fetch_assoc();
    $stmt->close();
    
    if (!$feedback) {
        $message = "Error: Feedback not found";
    } else {
        // Get previous responses
        $stmt = $conn->prepare("SELECT r.*, u.username as admin_name FROM response r JOIN user u ON r.adminID = u.userID WHERE r.feedbackID = ? ORDER BY r.timestamp DESC");
        $stmt->bind_param("i", $feedback_id);
        $stmt->execute();
        $responses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_response'])) {
    $response_text = $_POST['response_text'] ?? '';
    $admin_id = $_SESSION['user_id'];
    
    // Always set status to "responded" when a response is sent
    $new_status = "responded";
    
    if (empty($response_text)) {
        $message = "Error: Response text cannot be empty";
    } else {
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert response
            $stmt = $conn->prepare("INSERT INTO response (feedbackID, adminID, responseText, timestamp) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $feedback_id, $admin_id, $response_text);
            $stmt->execute();
            $stmt->close();
            
            // Update feedback status to "responded"
            $stmt = $conn->prepare("UPDATE feedback SET status = ? WHERE feedbackID = ?");
            $stmt->bind_param("si", $new_status, $feedback_id);
            $stmt->execute();
            $stmt->close();
            
            // Add notification for user
            $stmt = $conn->prepare("SELECT userID FROM feedback WHERE feedbackID = ?");
            $stmt->bind_param("i", $feedback_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $feedback_data = $result->fetch_assoc();
            $stmt->close();
            
            if ($feedback_data) {
                $user_id = $feedback_data['userID'];
                $notification_msg = "Your feedback has received a response";
                $stmt = $conn->prepare("INSERT INTO notification (userID, feedbackID, message, timestamp) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iis", $user_id, $feedback_id, $notification_msg);
                $stmt->execute();
                $stmt->close();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Store success message in session
            $_SESSION['feedback_message'] = "Success: Response sent successfully!";
            
            // Redirect to clean URL (without exposing the ID)
            header("Location: respond_feedback.php");
            exit;
        } catch (Exception $e) {
            // Rollback in case of error
            $conn->rollback();
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Check for success message from session
if (isset($_SESSION['feedback_message'])) {
    $message = $_SESSION['feedback_message'];
    unset($_SESSION['feedback_message']); // Clear the message after displaying it
}

// Check if we already have responses (to hide the response form)
$show_response_form = count($responses) === 0;
?>

<div class="main-container">
    <!-- Include sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <div class="content-area">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>Respond to Feedback</h1>
                <div class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>

            <div class="back-button">
                <a href="manage_feedback.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to All Feedback
                </a>
            </div>
            
            <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($feedback): ?>
                <div class="feedback-card">
                    <div class="feedback-header">
                        <h2>Feedback Details</h2>
                    </div>
                    
                    <div class="feedback-detail">
                        <div class="detail-row">
                            <span class="label">From:</span>
                            <span class="value"><?php echo htmlspecialchars($feedback['username']); ?></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="label">Date:</span>
                            <span class="value"><?php echo date('M j, Y', strtotime($feedback['timestamp'])); ?></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="label">Status:</span>
                            <span class="value status-badge status-<?php echo $feedback['status']; ?>"><?php echo ucfirst($feedback['status']); ?></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="label">Content:</span>
                            <div class="feedback-content"><?php echo nl2br(htmlspecialchars($feedback['content'])); ?></div>
                        </div>
                    </div>
                    
                    <!-- Response Form - Only shown if there are no responses yet -->
                    <?php if ($show_response_form): ?>
                    <div class="response-form">
                        <h3>Your Response</h3>
                        <form method="post" action="">
                            <input type="hidden" name="feedback_id" value="<?php echo $feedback['feedbackID']; ?>">
                            
                            <div class="form-group">
                                <label for="response_text">Message:</label>
                                <textarea class="form-control" id="response_text" name="response_text" rows="5" required></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="send_response" class="btn btn-primary">
                                    <i class="fas fa-reply"></i>Send Response
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Previous Responses -->
                    <?php if (count($responses) > 0): ?>
                    <div class="previous-responses">
                        <h3>Response Submitted</h3>
                        
                        <?php foreach ($responses as $response): ?>
                        <div class="response-item">
                            <div class="response-meta">
                                <span class="admin-name"><?php echo htmlspecialchars($response['admin_name']); ?></span>
                                <span class="response-date"><?php echo date('M j, Y, g:i A', strtotime($response['timestamp'])); ?></span>
                            </div>
                            <div class="response-text">
                                <?php echo nl2br(htmlspecialchars($response['responseText'])); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                </div>
            <?php else: ?>
                <?php if (empty($message) || strpos($message, 'Invalid') !== false): ?>
                <div class="not-found">
                    <p>Feedback not found. <a href="manage_feedback.php">Return to feedback list</a>.</p>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
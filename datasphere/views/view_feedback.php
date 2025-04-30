<?php
$pageTitle = "View Feedback";
$currentPage = "manage_feedback"; // Used for highlighting active menu item
require_once '../includes/header.php';
require_once '../includes/db.php'; // Make sure you have a db connection file

// Require user to be logged in
requireLogin();

// Get the current user's ID
$userID = $_SESSION['user_id'];

// Initialize variables
$feedback_id = null;
$message = '';

// Process feedback ID from various sources
if (isset($_GET['feedback'])) {
    // We accept ID from GET initially, but store it in session
    // and redirect to remove it from URL
    $feedback_id = intval(base64_decode($_GET['feedback']));
    $_SESSION['view_feedback_id'] = $feedback_id;
    
    // Redirect to same page without the ID parameter
    header("Location: view_feedback.php");
    exit;
} elseif (isset($_SESSION['view_feedback_id'])) {
    // Retrieve from session 
    $feedback_id = $_SESSION['view_feedback_id'];
}

// Validate feedback ID
if ($feedback_id <= 0) {
    // Invalid feedback ID, redirect back to manage feedback page
    header("Location: manage_feedback.php");
    exit;
}

// Fetch feedback details
$stmt = $conn->prepare("SELECT f.*, u.username FROM feedback f 
                        JOIN user u ON f.userID = u.userID 
                        WHERE f.feedbackID = ?");
$stmt->bind_param("i", $feedback_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Feedback not found, redirect back to manage feedback page
    header("Location: manage_feedback.php");
    exit;
}

$feedback = $result->fetch_assoc();
$stmt->close();

// Update status display - change 'new' to 'pending'
if ($feedback['status'] == 'new') {
    $display_status = 'pending';
} else {
    $display_status = $feedback['status'];
}

// Get responses for this feedback
$stmt = $conn->prepare("SELECT r.*, u.username as admin_name FROM response r 
                        JOIN user u ON r.adminID = u.userID 
                        WHERE r.feedbackID = ? 
                        ORDER BY r.timestamp ASC");
$stmt->bind_param("i", $feedback_id);
$stmt->execute();
$responses_result = $stmt->get_result();
$responses = $responses_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Check if feedback has already been responded to
$has_response = count($responses) > 0;

// Handle form submissions for adding responses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_response'])) {
    // Check if feedback already has a response
    if ($has_response) {
        $message = "This feedback has already been responded to.";
    } else {
        $response_text = $_POST['response_text'] ?? '';
        $admin_id = $_SESSION['user_id'];
        
        if (empty($response_text)) {
            $message = "Response text cannot be empty";
        } else {
            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Insert response
                $stmt = $conn->prepare("INSERT INTO response (feedbackID, adminID, responseText, timestamp) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iis", $feedback_id, $admin_id, $response_text);
                $stmt->execute();
                $stmt->close();
                
                // Update feedback status to 'responded'
                $stmt = $conn->prepare("UPDATE feedback SET status = 'responded' WHERE feedbackID = ?");
                $stmt->bind_param("i", $feedback_id);
                $stmt->execute();
                $stmt->close();
                
                // Add notification for user
                $user_id = $feedback['userID'];
                $notification_msg = "Your feedback has received a response";
                $stmt = $conn->prepare("INSERT INTO notification (userID, feedbackID, message, timestamp) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iis", $user_id, $feedback_id, $notification_msg);
                $stmt->execute();
                $stmt->close();
                
                // Commit transaction
                $conn->commit();
                
                // Store success message in session
                $_SESSION['feedback_message'] = "Response sent successfully!";
                
                // Redirect to clean URL
                header("Location: view_feedback.php");
                exit;
            } catch (Exception $e) {
                // Rollback in case of error
                $conn->rollback();
                $message = "Error: " . $e->getMessage();
            }
        }
    }
}

// Check for success message from session
if (isset($_SESSION['feedback_message'])) {
    $message = $_SESSION['feedback_message'];
    unset($_SESSION['feedback_message']); // Clear the message after displaying it
    
    // Refresh feedback and response data after successful submission
    $stmt = $conn->prepare("SELECT f.*, u.username FROM feedback f 
                           JOIN user u ON f.userID = u.userID 
                           WHERE f.feedbackID = ?");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $feedback = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $stmt = $conn->prepare("SELECT r.*, u.username as admin_name FROM response r 
                           JOIN user u ON r.adminID = u.userID 
                           WHERE r.feedbackID = ? 
                           ORDER BY r.timestamp ASC");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $responses_result = $stmt->get_result();
    $responses = $responses_result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Update has_response flag
    $has_response = count($responses) > 0;
    
    // Update status display
    if ($feedback['status'] == 'new') {
        $display_status = 'pending';
    } else {
        $display_status = $feedback['status'];
    }
}

// Function to format date
function formatDate($timestamp) {
    return date('M j, Y', strtotime($timestamp));
}

// Function to format datetime with time
function formatDateTime($timestamp) {
    return date('M j, Y, g:i A', strtotime($timestamp));
}
?>

<div class="main-container">
    <!-- Include sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <div class="content-area">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>View Feedback</h1>
                <div class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>
            
            <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <div class="back-button">
                <a href="manage_feedback.php" class="action-btn secondary-btn">
                    <i class="fas fa-arrow-left"></i> Back to Feedback List
                </a>
            </div>
            
            <!-- Feedback details section -->
            <div class="feedback-details-card">
                <div class="feedback-header">
                    <h2>Feedback Details</h2>
                </div>

                <div class="feedback-meta">
                    <div class="meta-item">
                        <strong>From:</strong>
                        <span><?php echo htmlspecialchars($feedback['username']); ?></span>
                    </div>
                    <div class="meta-item">
                        <strong>Date:</strong>
                        <span><?php echo formatDate($feedback['timestamp']); ?></span>
                    </div>
                    <div class="meta-item">
                        <strong>Feedback Status:</strong>
                        <span class="status-badge status-<?php echo $display_status; ?>">
                            <?php echo ucfirst($display_status); ?>
                        </span>
                    </div>
                </div>
                
                <div class="feedback-content-container">
                    <h3>Content</h3>
                    <div class="feedback-content">
                        <?php echo nl2br(htmlspecialchars($feedback['content'])); ?>
                    </div>
                </div>
            </div>
            
            <!-- Response history section -->
            <div class="responses-section">
                <h2>Response Submited</h2>
                
                <?php if ($has_response): ?>
                    <div class="response-list">
                        <?php foreach ($responses as $response): ?>
                            <div class="response-item">
                                <div class="response-header">
                                    <div class="response-author">
                                        <strong><?php echo htmlspecialchars($response['admin_name']); ?></strong>
                                    </div>
                                    <div class="response-date">
                                        <?php echo formatDateTime($response['timestamp']); ?>
                                    </div>
                                </div>
                                <div class="response-content">
                                    <?php echo nl2br(htmlspecialchars($response['responseText'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="response-status pending">
                        <i class="fas fa-clock"></i> This feedback is awaiting response
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Add response form - Only show if no response exists yet -->
            <?php if (!$has_response): ?>
            <div class="add-response-section">
                <h2>Add Response</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <label for="response_text">Your Response:</label>
                        <textarea id="response_text" name="response_text" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="send_response" class="action-btn primary-btn">
                            <i class="fas fa-reply"></i> Send Response
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add some additional CSS for the feedback view -->
<style>
    .back-button {
        margin-bottom: 20px;
    }

    .feedback-details-card, 
    .responses-section,
    .add-response-section {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 24px;
        margin-bottom: 24px;
    }

    .feedback-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        border-bottom: 1px solid #eee;
        padding-bottom: 12px;
    }

    .feedback-header h2 {
        margin: 0;
    }

    .feedback-meta {
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
    }

    .meta-item {
        margin-right: 24px;
        margin-bottom: 8px;
    }

    .meta-item strong {
        margin-right: 8px;
        font-weight: 600;
    }

    .feedback-content-container h3 {
        margin-top: 0;
        margin-bottom: 12px;
        font-size: 16px;
        color: #555;
    }

    .feedback-content {
        background-color: #f9f9f9;
        padding: 16px;
        border-radius: 6px;
        line-height: 1.5;
        white-space: normal;
        word-break: break-word;
        max-width: 100%;
    }

    .response-list {
        margin-top: 16px;
    }

    .response-item {
        background-color: #f9f9f9;
        border-radius: 6px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .response-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .response-date {
        color: #777;
    }

    .response-content {
        line-height: 1.5;
        white-space: normal;
        word-break: break-word;
        max-width: 100%;
    }

    .no-responses {
        font-style: italic;
        color: #777;
    }

    .add-response-section h2 {
        margin-top: 0;
        margin-bottom: 16px;
    }

    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        resize: vertical;
        font-family: inherit;
        font-size: 14px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background-color: #e3f2fd;
        color: #0d47a1;
    }

    .status-responded {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    .status-text {
        font-weight: 600;
    }

    /* Add a bit of spacing to messages */
    .message {
        margin-bottom: 20px;
        padding: 12px;
        border-radius: 4px;
    }

    .message.success {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    .message.error {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }

    /* Response status indicators */
    .response-status {
        padding: 16px;
        border-radius: 6px;
        margin-bottom: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .response-status i {
        margin-right: 8px;
        font-size: 18px;
    }

    .response-status.pending {
        background-color: #e3f2fd;
        color: #0d47a1;
    }

    .response-status.responded {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
</style>

<?php require_once '../includes/footer.php'; ?>
<?php
$pageTitle = "Submit Feedback";
$currentPage = "submit_feedback";
require_once '../includes/header.php';
require_once '../includes/db.php';

// Require user to be logged in
requireLogin();

// Get the current user's ID
$userID = $_SESSION['user_id'];

// Define feedback categories
$feedbackCategories = [
    'bug' => 'Bug Report',
    'feature' => 'Feature Request',
    'ui' => 'UI/UX Improvement',
    'performance' => 'Performance Issue',
    'content' => 'Content Feedback',
    'other' => 'Other'
];

// Initialize variables
$content = '';
$category = '';
$submitMsg = '';
$submitError = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $content = $_POST['content'] ?? '';
    $category = $_POST['category'] ?? '';
    
    // Validate input
    if (empty($content)) {
        $submitError = "Please enter your feedback";
    } elseif (empty($category) || !array_key_exists($category, $feedbackCategories)) {
        $submitError = "Please select a valid feedback category";
    } else {
        // Set default status as 'pending'
        $status = 'pending';
        
        // Insert feedback first
        $query = "INSERT INTO feedback (userID, content, category, timestamp, status) 
                  VALUES (?, ?, ?, NOW(), ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isss", $userID, $content, $category, $status);
        
        try {
            if (mysqli_stmt_execute($stmt)) {
                $feedbackID = mysqli_insert_id($conn);
                $submitMsg = "Feedback submitted successfully!";
                
                // Now create notification with the valid feedbackID
                $adminID = 1;
                $notificationMessage = "New feedback submitted by " . $_SESSION['user_name'];
                
                $notifyQuery = "INSERT INTO notification (userID, feedbackID, message, timestamp) 
                          VALUES (?, ?, ?, NOW())";
                $notifyStmt = mysqli_prepare($conn, $notifyQuery);
                mysqli_stmt_bind_param($notifyStmt, "iis", $adminID, $feedbackID, $notificationMessage);
                
                // If notification fails, log it but don't show error to user
                if (!mysqli_stmt_execute($notifyStmt)) {
                    error_log("Failed to create notification: " . mysqli_error($conn));
                }
                
                // Clear form after successful submission
                $content = '';
                $category = '';
            } else {
                $submitError = "Error submitting feedback: " . mysqli_error($conn);
                error_log("Database error: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            $submitError = "Error submitting feedback: " . $e->getMessage();
            error_log("Exception: " . $e->getMessage());
        }
    }
}
?>

<div class="main-container">
    <!-- Include sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <div class="content-area">
        <div class="page-header">
            <h1>Submit Feedback</h1>
            <p class="description">Share your thoughts and experiences with us</p>
        </div>
        
        <?php if (!empty($submitMsg)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($submitMsg); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($submitError)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($submitError); ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label for="category">Feedback Category</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="" disabled <?php echo empty($category) ? 'selected' : ''; ?>>Please select a category</option>
                        <?php foreach ($feedbackCategories as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $category === $value ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="content">Your Feedback</label>
                    <textarea id="content" name="content" class="form-control" rows="6" required><?php echo isset($content) ? htmlspecialchars($content) : ''; ?></textarea>
                    <small class="form-text text-muted">Please provide detailed feedback to help us improve.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                    <a href="my_feedback.php" class="btn btn-secondary">View My Feedback</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
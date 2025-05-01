<?php
$pageTitle = "My Feedback";
$currentPage = "my_feedback"; 
require_once '../includes/header.php';
require_once '../includes/db.php';

// Require user to be logged in
requireLogin();

// Get the current user's ID
$userID = $_SESSION['user_id'];

// Pagination settings
$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Get total number of feedback entries for this user
$countQuery = "SELECT COUNT(*) AS total FROM feedback WHERE userID = ?";
$countStmt = mysqli_prepare($conn, $countQuery);
mysqli_stmt_bind_param($countStmt, "i", $userID);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalItems = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Get user's feedback with pagination
$query = "SELECT feedbackID, content, timestamp, status 
          FROM feedback 
          WHERE userID = ? 
          ORDER BY timestamp DESC 
          LIMIT ?, ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iii", $userID, $offset, $itemsPerPage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="main-container">
    <!-- Include sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <div class="content-area">
        <div class="feedback-container">
            <div class="feedback-header">
                <h1>My Feedback</h1>
                <p class="description">View and manage your submitted feedback</p>
            </div>
            <div class="action-bar">
                <a href="submit_feedback.php" class="btn-primary">Submit New Feedback</a>
            </div>
            
            <div class="feedback-history">                
                <?php if (mysqli_num_rows($result) > 0): ?>
                
                <div class="feedback-list">
                    <?php while ($feedback = mysqli_fetch_assoc($result)): ?>
                        <div class="feedback-item">
                            <div class="feedback-meta">
                                <div class="feedback-date"><?php echo date('M j, Y g:i A', strtotime($feedback['timestamp'])); ?></div>
                                <div class="feedback-status-container">
                                    <span class="status-label">Feedback Status:</span>
                                    <div class="feedback-status <?php echo $feedback['status']; ?>">
                                        <?php echo ucfirst($feedback['status']); ?>
                                    </div>
                                </div>
                                <?php if ($feedback['status'] == 'pending'): ?>
                                <div class="feedback-actions">
                                    <a href="delete_feedback.php?id=<?php echo $feedback['feedbackID']; ?>" 
                                        class="delete-btn" 
                                        onclick="return confirm('Are you sure you want to delete this feedback?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="feedback-content">
                                <?php echo nl2br(htmlspecialchars($feedback['content'])); ?>
                            </div>
                            
                            <?php
                            // Get admin responses to this feedback
                            $responseQuery = "SELECT r.responseText AS response, r.timestamp, u.username 
                            FROM response r
                            JOIN user u ON r.adminID = u.userID
                            WHERE r.feedbackID = ?
                            ORDER BY r.timestamp ASC";

                            $responseStmt = mysqli_prepare($conn, $responseQuery);
                            mysqli_stmt_bind_param($responseStmt, "i", $feedback['feedbackID']);
                            mysqli_stmt_execute($responseStmt);
                            $responseResult = mysqli_stmt_get_result($responseStmt);
                            
                            if (mysqli_num_rows($responseResult) > 0):
                            ?>
                            <div class="admin-responses">
                                <h4>Admin Responses:</h4>
                                <?php while ($response = mysqli_fetch_assoc($responseResult)): ?>
                                    <div class="response-item">
                                        <div class="response-meta">
                                            <span class="admin-name"><?php echo htmlspecialchars($response['username']); ?></span>
                                            <span class="response-date"><?php echo date('M j, Y g:i A', strtotime($response['timestamp'])); ?></span>
                                        </div>
                                        <div class="response-content">
                                            <?php echo nl2br(htmlspecialchars($response['response'])); ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <!-- Pagination controls -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo ($page - 1); ?>" class="page-nav">&laquo; Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-num <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo ($page + 1); ?>" class="page-nav">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                
                <div class="no-feedback">
                    <p>You haven't submitted any feedback yet.</p>
                </div>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>

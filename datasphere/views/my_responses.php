<?php
$pageTitle = "My Responses";
$currentPage = "feedback_responses";
require_once '../includes/header.php';
require_once '../includes/db.php';

// Get the current user's ID (customer)
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
mysqli_stmt_close($countStmt);

// Get user's feedback with pagination - removed rating from query
$query = "SELECT feedbackID, content, timestamp, status
          FROM feedback
          WHERE userID = ?
          ORDER BY timestamp DESC
          LIMIT ?, ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iii", $userID, $offset, $itemsPerPage);
mysqli_stmt_execute($stmt);
$feedbackResult = mysqli_stmt_get_result($stmt);

?>

<div class="main-container">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="content-area">
        <div class="feedback-container">
            <div class="feedback-header">
                <h1>My Feedback and Responses</h1>
                <p>View your submitted feedback and any responses from administrators.</p>
            </div>


            <div class="feedback-history">
                <?php if (mysqli_num_rows($feedbackResult) > 0): ?>

                    <div class="feedback-list">
                        <?php while ($feedback = mysqli_fetch_assoc($feedbackResult)): ?>
                            <div class="feedback-item">
                                <div class="feedback-meta">
                                    <div class="feedback-date"><?php echo date('M j, Y g:i A', strtotime($feedback['timestamp'])); ?></div>
                                    <div class="feedback-status-container">
                                        <span class="status-label">Feedback Status:</span>
                                        <div class="feedback-status <?php echo $feedback['status']; ?>">
                                            <?php echo ucfirst($feedback['status']); ?>
                                        </div>
                                    </div>
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
                        <a href="submit_feedback.php" class="btn-primary">Submit Feedback Now</a>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    /* Basic styling - feel free to enhance */
    /* Common styling for both feedback pages */
    .feedback-container {
        padding: 20px;
    }

    .feedback-header h1 {
        margin-bottom: 5px;
    }

    .feedback-history {
        margin-top: 20px;
    }

    .feedback-item {
        border: 1px solid #eee;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    .feedback-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 10px;
        align-items: center;
        font-size: 0.9em;
        color: #555;
    }

    .feedback-status {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.9em;
    }

    .feedback-status.open {
        background-color: #d4edda;
        color: #155724;
    }

    .feedback-status.closed {
        background-color: #f8d7da;
        color: #721c24;
    }

    .feedback-status.in-progress {
        background-color: #fff3cd;
        color: #856404;
    }

    .feedback-content {
        margin-bottom: 10px;
        white-space: normal;
        word-break: break-word;
        max-width: 100%;
        line-height: 1.5;
    }

    .admin-responses {
        margin-top: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff;
    }

    .response-item {
        margin-bottom: 10px;
        padding: 8px;
        border-left: 3px solid #28a745;
        background-color: #e9f9ef;
        border-radius: 3px;
    }

    .response-meta {
        font-size: 0.85em;
        color: #777;
        margin-bottom: 3px;
    }

    .admin-name {
        font-weight: bold;
        margin-right: 5px;
    }

    .response-content {
        white-space: normal;
        word-break: break-word;
        max-width: 100%;
        line-height: 1.5;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination a {
        padding: 8px 12px;
        margin: 0 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        color: #333;
    }

    .pagination a.active {
        background-color: #1a56db;
        color: white;
        border-color: #1a56db;
    }

    .pagination a:hover {
        background-color: #eee;
    }

    .no-feedback {
        text-align: center;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .action-bar {
        margin-bottom: 20px;
    }

    .btn-primary {
        padding: 8px 16px;
        background-color: #1a56db;
        color: white;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary:hover {
        background-color: #1a56db;
    }
</style>

<script>
    // Basic JavaScript for potential enhancements (currently empty)
    document.addEventListener('DOMContentLoaded', function() {
        // You can add JavaScript functionality here if needed,
        // for example, to handle dynamic loading of responses or UI interactions.
    });
</script>

<?php require_once '../includes/footer.php'; ?>
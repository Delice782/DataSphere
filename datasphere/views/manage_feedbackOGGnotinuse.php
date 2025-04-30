<?php
$pageTitle = "Manage Feedback";
$currentPage = "manage_feedback"; // Used for highlighting active menu item
require_once '../includes/header.php';
require_once '../includes/db.php'; // Make sure you have a db connection file
require_once '../includes/pagination.php'; // Include our new pagination helper

// Require user to be logged in
requireLogin();

// Get the current user's ID
$userID = $_SESSION['user_id'];

// Handle form submissions
$message = '';

// Add response to feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_response'])) {
    $feedback_id = $_POST['feedback_id'] ?? '';
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
            
            // Update feedback status to responded
            $stmt = $conn->prepare("UPDATE feedback SET status = 'responded' WHERE feedbackID = ?");
            $stmt->bind_param("i", $feedback_id);
            $stmt->execute();
            $stmt->close();
            
            // Add notification for user
            $stmt = $conn->prepare("SELECT userID FROM feedback WHERE feedbackID = ?");
            $stmt->bind_param("i", $feedback_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $feedback = $result->fetch_assoc();
            $stmt->close();
            
            if ($feedback) {
                $user_id = $feedback['userID'];
                $notification_msg = "Your feedback has received a response";
                $stmt = $conn->prepare("INSERT INTO notification (userID, feedbackID, message, timestamp) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iis", $user_id, $feedback_id, $notification_msg);
                $stmt->execute();
                $stmt->close();
            }
            
            // Commit transaction
            $conn->commit();
            $message = "Response sent successfully!";
        } catch (Exception $e) {
            // Rollback in case of error
            $conn->rollback();
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch feedback data for display
// Store the original search term for display in the form
$original_search = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';

// Base SQL for counting total records
$count_sql = "SELECT COUNT(*) AS total FROM feedback f
              JOIN user u ON f.userID = u.userID
              WHERE 1=1";

$params = [];
$types = "";

// For SQL query, add wildcards to search term
if (!empty($original_search)) {
    $search = '%' . $original_search . '%';
    $count_sql .= " AND (f.content LIKE ? OR u.username LIKE ?)";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

if (!empty($status_filter)) {
    $count_sql .= " AND f.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Set up pagination
$per_page = 5; // Number of records per page
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Use our pagination helper
$pagination = Pagination::paginate($conn, $count_sql, $params, $types, $per_page, $current_page);

// Prepare the main query with LIMIT and OFFSET
$sql = "SELECT f.*, u.username FROM feedback f
        JOIN user u ON f.userID = u.userID
        WHERE 1=1";

// Copy params for the main query
$main_params = $params;
$main_types = $types;

// For SQL query, add wildcards to search term
if (!empty($original_search)) {
    $search = '%' . $original_search . '%';
    $sql .= " AND (f.content LIKE ? OR u.username LIKE ?)";
    if (empty($params)) {
        $main_params[] = $search;
        $main_params[] = $search;
        $main_types .= "ss";
    }
}

if (!empty($status_filter)) {
    $sql .= " AND f.status = ?";
    if (!in_array($status_filter, $params)) {
        $main_params[] = $status_filter;
        $main_types .= "s";
    }
}

$sql .= " ORDER BY f.timestamp DESC LIMIT ? OFFSET ?";
$main_params[] = $pagination['limit'];
$main_params[] = $pagination['offset'];
$main_types .= "ii";

$stmt = $conn->prepare($sql);

if (!empty($main_params)) {
    $stmt->bind_param($main_types, ...$main_params);
}

$stmt->execute();
$result = $stmt->get_result();
$feedbacks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Function to format date
function formatDate($timestamp) {
    return date('M j, Y', strtotime($timestamp));
}

// Get feedback details (for AJAX request)
if (isset($_GET['get_feedback']) && isset($_GET['id'])) {
    $feedback_id = $_GET['id'];
    
    // Get feedback details
    $stmt = $conn->prepare("SELECT f.*, u.username FROM feedback f JOIN user u ON f.userID = u.userID WHERE f.feedbackID = ?");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $feedback = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Get responses
    $stmt = $conn->prepare("SELECT r.*, u.username as admin_name FROM response r JOIN user u ON r.adminID = u.userID WHERE r.feedbackID = ? ORDER BY r.timestamp ASC");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $responses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Format feedback data
    $feedback_data = [
        'id' => $feedback['feedbackID'],
        'user' => $feedback['username'],
        'content' => $feedback['content'],
        'date' => formatDate($feedback['timestamp']),
        'status' => $feedback['status'],
        'responses' => []
    ];
    
    // Format responses
    foreach ($responses as $response) {
        $feedback_data['responses'][] = [
            'admin' => $response['admin_name'],
            'text' => $response['responseText'],
            'date' => date('M j, Y, g:i A', strtotime($response['timestamp']))
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($feedback_data);
    exit;
}
?>

<div class="main-container">
    <!-- Include sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <div class="content-area">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>Manage Feedback</h1>
                <div class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>
            
            <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <!-- Search and filter form - Using original_search to avoid showing % wildcards -->
            <form method="GET" action="" class="search-filter">
                <input type="text" name="search" placeholder="Search by content or username" value="<?php echo htmlspecialchars($original_search); ?>">
                
                <div class="filter-group">
                    <select name="status_filter">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="responded" <?php echo $status_filter === 'responded' ? 'selected' : ''; ?>>Responded</option>
                    </select>
                    
                    <button type="submit">Search</button>
                </div>
            </form>
            
            <!-- Feedback listing -->
            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($feedbacks) > 0): ?>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['username']); ?></td>
                                <td><?php echo htmlspecialchars(substr($feedback['content'], 0, 50)) . (strlen($feedback['content']) > 50 ? '...' : ''); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $feedback['status']; ?>">
                                        <?php 
                                        if ($feedback['status'] == 'new') {
                                            echo 'Pending';
                                        } else {
                                            echo ucfirst($feedback['status']); 
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="feedback-actions">
                                        <button type="button" class="view-btn" onclick="window.location.href='view_feedback.php?feedback=<?php echo base64_encode($feedback['feedbackID']); ?>'">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <?php if ($feedback['status'] != 'responded'): ?>
                                        <button type="button" class="respond-btn" onclick="window.location.href='respond_feedback.php?feedback=<?php echo base64_encode($feedback['feedbackID']); ?>'">
                                            <i class="fas fa-reply"></i> Respond
                                        </button>
                                        <?php endif; ?>
                                        <!-- Add delete button -->
                                        <button type="button" class="delete-btn" onclick="prepareDeleteFeedback(<?php echo $feedback['feedbackID']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No feedback found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Pagination controls -->
            <?php 
            // Build URL pattern for pagination links
            $url_params = [];
            if (!empty($original_search)) {
                $url_params[] = 'search=' . urlencode($original_search);
            }
            if (!empty($status_filter)) {
                $url_params[] = 'status_filter=' . urlencode($status_filter);
            }
            
            $url_suffix = !empty($url_params) ? '&' . implode('&', $url_params) : '';
            $url_pattern = '?page=%d' . $url_suffix;
            
            echo Pagination::renderLinks($pagination['current_page'], $pagination['total_pages'], $url_pattern);
            ?>
        </div>
    </div>
</div>

<!-- View Feedback Modal -->
<div id="viewFeedbackModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Feedback Details</h2>
            <span class="close">&times;</span>
        </div>
        <div class="feedback-detail">
            <p><span class="label">From:</span> <span id="feedback-user"></span></p>
            <p><span class="label">Date:</span> <span id="feedback-date"></span></p>
            <p><span class="label">Status:</span> <span id="feedback-status"></span></p>
            <p><span class="label">Content:</span></p>
            <div class="feedback-content" id="feedback-content"></div>
        </div>
        
        <div class="response-history">
            <h3>Response History</h3>
            <div id="response-container">
                <!-- Response items will be inserted here via JavaScript -->
            </div>
        </div>
        
        <div class="form-actions">
            <button type="button" id="closeViewBtn" class="action-btn secondary">Close</button>
            <button type="button" id="respondBtn" class="respond-btn">Respond</button>
        </div>
    </div>
</div>

<!-- Respond to Feedback Modal -->
<div id="respondFeedbackModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Respond to Feedback</h2>
            <span class="close">&times;</span>
        </div>
        <div class="feedback-detail">
            <p><span class="label">From:</span> <span id="respond-feedback-user"></span></p>
            <p><span class="label">Content:</span></p>
            <div class="feedback-content" id="respond-feedback-content"></div>
        </div>
        
        <form id="responseForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <input type="hidden" id="feedback_id" name="feedback_id">
            
            <div class="form-group">
                <label for="response_text">Your Response:</label>
                <textarea id="response_text" name="response_text" required></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" id="cancelResponseBtn" class="action-btn secondary">Cancel</button>
                <button type="submit" name="send_response" class="action-btn">Send Response</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Feedback Confirmation Modal -->
<div id="deleteFeedbackModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirm Delete</h2>
            <span class="close">&times;</span>
        </div>
        <p>Are you sure you want to delete this feedback? This action cannot be undone.</p>
        <form method="POST" action="delete.php">
            <input type="hidden" id="delete_feedback_id" name="feedback_id">
            <input type="hidden" name="action" value="delete_feedback">
            <div class="form-actions">
                <button type="button" id="cancelDeleteFeedbackBtn" class="action-btn secondary">Cancel</button>
                <button type="submit" class="delete-btn">Yes, Delete Feedback</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Modal elements
    const viewFeedbackModal = document.getElementById('viewFeedbackModal');
    const respondFeedbackModal = document.getElementById('respondFeedbackModal');
    const closeButtons = document.querySelectorAll('.close');
    const closeViewBtn = document.getElementById('closeViewBtn');
    const cancelResponseBtn = document.getElementById('cancelResponseBtn');
    const respondBtn = document.getElementById('respondBtn');
    
    // Function to close all modals
    function closeModals() {
        viewFeedbackModal.style.display = 'none';
        respondFeedbackModal.style.display = 'none';
    }
    
    // Close buttons
    closeButtons.forEach(button => {
        button.addEventListener('click', closeModals);
    });
    
    if (closeViewBtn) {
        closeViewBtn.addEventListener('click', closeModals);
    }
    
    if (cancelResponseBtn) {
        cancelResponseBtn.addEventListener('click', closeModals);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === viewFeedbackModal || event.target === respondFeedbackModal) {
            closeModals();
        }
    });
    
    // Handle respond button in view modal
    if (respondBtn) {
        respondBtn.addEventListener('click', function() {
            // Close view modal
            viewFeedbackModal.style.display = 'none';
            
            // Get feedback ID from the view modal
            const feedbackId = document.querySelector('#feedback-user').getAttribute('data-id');
            
            // Set ID in respond modal and open it
            respondFeedback(feedbackId);
        });
    }
    
    // Make these functions available globally
    window.viewFeedback = viewFeedback;
    window.respondFeedback = respondFeedback;
});

// Function to view feedback details
function viewFeedback(feedbackId) {
    // Make AJAX request to get feedback details
    fetch(`?get_feedback=true&id=${feedbackId}`)
        .then(response => response.json())
        .then(data => {
            // Populate the view modal with feedback details
            document.getElementById('feedback-user').textContent = data.user;
            document.getElementById('feedback-user').setAttribute('data-id', data.id);
            document.getElementById('feedback-date').textContent = data.date;
            
            // Update status display
            let statusText = data.status;
            if (statusText === 'new') {
                statusText = 'Pending';
            } else {
                statusText = statusText.charAt(0).toUpperCase() + statusText.slice(1);
            }
            document.getElementById('feedback-status').textContent = statusText;
            
            document.getElementById('feedback-content').textContent = data.content;
            
            // Clear and populate response history
            const responseContainer = document.getElementById('response-container');
            responseContainer.innerHTML = '';
            
            if (data.responses.length > 0) {
                data.responses.forEach(response => {
                    const responseItem = document.createElement('div');
                    responseItem.className = 'response-item';
                    
                    const responseMeta = document.createElement('div');
                    responseMeta.className = 'response-meta';
                    responseMeta.innerHTML = `
                        <span><strong>${response.admin}</strong></span>
                        <span>${response.date}</span>
                    `;
                    
                    const responseText = document.createElement('div');
                    responseText.className = 'response-text';
                    responseText.textContent = response.text;
                    
                    responseItem.appendChild(responseMeta);
                    responseItem.appendChild(responseText);
                    responseContainer.appendChild(responseItem);
                });
            } else {
                responseContainer.innerHTML = '<p>No responses yet.</p>';
            }
            
            // Hide respond button if the feedback has already been responded to
            if (data.status === 'responded') {
                document.getElementById('respondBtn').style.display = 'none';
            } else {
                document.getElementById('respondBtn').style.display = 'inline-block';
            }
            
            // Show the modal
            document.getElementById('viewFeedbackModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching feedback details:', error);
        });
}

// Function to open respond modal
function respondFeedback(feedbackId) {
    // Make AJAX request to get feedback details
    fetch(`?get_feedback=true&id=${feedbackId}`)
        .then(response => response.json())
        .then(data => {
            // Check if the feedback has already been responded to
            if (data.status === 'responded') {
                alert('This feedback has already been responded to.');
                return;
            }
            
            // Populate the respond modal
            document.getElementById('respond-feedback-user').textContent = data.user;
            document.getElementById('respond-feedback-content').textContent = data.content;
            document.getElementById('feedback_id').value = data.id;
            
            // Clear the response text area
            document.getElementById('response_text').value = '';
            
            // Show the modal
            document.getElementById('respondFeedbackModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching feedback details:', error);
        });
}
// Function to prepare delete confirmation for feedback
function prepareDeleteFeedback(feedbackId) {
    document.getElementById('delete_feedback_id').value = feedbackId;
    document.getElementById('deleteFeedbackModal').style.display = 'block';
}

// Additional event listeners for the delete feedback modal
document.addEventListener("DOMContentLoaded", function() {
    const deleteFeedbackModal = document.getElementById('deleteFeedbackModal');
    const cancelDeleteFeedbackBtn = document.getElementById('cancelDeleteFeedbackBtn');
    
    // Close delete feedback modal with cancel button
    if (cancelDeleteFeedbackBtn) {
        cancelDeleteFeedbackBtn.addEventListener('click', function() {
            deleteFeedbackModal.style.display = 'none';
        });
    }
    
    // Add modal close behavior to existing closeModals function
    const closeModalsOriginal = closeModals;
    window.closeModals = function() {
        closeModalsOriginal();
        deleteFeedbackModal.style.display = 'none';
    };
    
    // Add modal close on click outside
    window.addEventListener('click', (event) => {
        if (event.target === deleteFeedbackModal) {
            deleteFeedbackModal.style.display = 'none';
        }
    });
});
</script>

<style>
/* Update status badge colors */
.status-badge {
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-new, .status-pending {
    background-color: #e3f2fd;
    color: #0d47a1;
}

.status-responded {
    background-color: #e8f5e9;
    color: #2e7d32;
}

/* Additional styles for pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
    gap: 10px;
}

.pagination a {
    padding: 8px 12px;
    background-color: #f5f5f5;
    border-radius: 4px;
    color: #333;
    text-decoration: none;
    transition: background-color 0.3s;
}

.pagination a:hover {
    background-color: #e0e0e0;
}

.page-info {
    margin: 0 10px;
    font-size: 0.9rem;
    color: #666;
}
</style>

<?php require_once '../includes/footer.php'; ?>
<?php
$pageTitle = "View User";
$currentPage = "manage_users"; // Keep the same active menu item
require_once '../includes/header.php';
require_once '../includes/db.php';

// Require user to be logged in
requireLogin();

// Initialize variables
$user_id = null;
$message = '';

// Process user ID from various sources
if (isset($_GET['id'])) {
    // We accept ID from GET initially, but store it in session
    // and redirect to remove it from URL
    $user_id = intval($_GET['id']);
    $_SESSION['view_user_id'] = $user_id;
    
    // Redirect to same page without the ID parameter
    header("Location: view_user.php");
    exit;
} elseif (isset($_SESSION['view_user_id'])) {
    // Retrieve from session 
    $user_id = $_SESSION['view_user_id'];
}

// Validate user ID
if ($user_id <= 0) {
    // Invalid user ID, redirect back to manage users page
    header("Location: manage_users.php");
    exit;
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM user WHERE userID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User not found, redirect back to manage users page
    header("Location: manage_users.php");
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Optional: Fetch additional user data if needed
// For example, you might want to fetch feedback submitted by this user
$stmt = $conn->prepare("SELECT COUNT(*) as feedback_count FROM feedback WHERE userID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$feedback_result = $stmt->get_result();
$feedback_data = $feedback_result->fetch_assoc();
$feedback_count = $feedback_data['feedback_count'];
$stmt->close();
?>

<div class="main-container">
    <!-- Include sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <div class="content-area">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>View User</h1>
                <div class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>
            
            <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <div class="back-button">
                <a href="manage_users.php" class="action-btn secondary-btn">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
            
            <div class="user-details-card">
                <div class="user-header">
                    <h2>User Information</h2>
                    <div class="user-actions">
                        <button type="button" class="edit-btn" 
                            data-id="<?php echo $user['userID']; ?>"
                            data-username="<?php echo htmlspecialchars($user['username']); ?>"
                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                            data-role="<?php echo htmlspecialchars($user['role']); ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
                
                <div class="user-detail">
                    <strong>Username:</strong>
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                
                <div class="user-detail">
                    <strong>Email:</strong>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                
                <div class="user-detail">
                    <strong>Role:</strong>
                    <span><?php echo htmlspecialchars($user['role']); ?></span>
                </div>
                
                <div class="user-detail">
                    <strong>Feedback Submitted:</strong>
                    <span><?php echo $feedback_count; ?></span>
                </div>
            </div>
            
            <!-- Optional: Add feedback history section -->
            <?php if ($feedback_count > 0): ?>
            <div class="feedback-history-section">
                <h2>Feedback History</h2>
                
                <?php
                // Fetch user's feedback submissions with pagination
                $per_page = 5;
                $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $offset = ($current_page - 1) * $per_page;
                
                $stmt = $conn->prepare("SELECT * FROM feedback WHERE userID = ? ORDER BY timestamp DESC LIMIT ? OFFSET ?");
                $stmt->bind_param("iii", $user_id, $per_page, $offset);
                $stmt->execute();
                $feedback_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                
                // Calculate total pages
                $total_records = $feedback_count;
                $total_pages = ceil($total_records / $per_page);
                ?>
                
                <table class="feedback-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Content</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedback_list as $feedback): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($feedback['timestamp'])); ?></td>
                            <td>
                                <?php 
                                // Truncate content if too long
                                $content = $feedback['content'];
                                echo htmlspecialchars(strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content); 
                                ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $feedback['status'] == 'new' ? 'pending' : $feedback['status']; ?>">
                                    <?php echo $feedback['status'] == 'new' ? 'Pending' : ucfirst($feedback['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_feedback.php?feedback=<?php echo base64_encode($feedback['feedbackID']); ?>" class="view-link">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Simple pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $current_page): ?>
                            <span class="current-page"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal - Copy from manage_users.php -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Edit User</h2>
            <span class="close">&times;</span>
        </div>
        <form id="userForm" method="POST" action="manage_users.php">
            <input type="hidden" id="user_id" name="user_id" value="<?php echo $user['userID']; ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="Admin" <?php echo $user['role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="Customer" <?php echo $user['role'] === 'Customer' ? 'selected' : ''; ?>>Customer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password <span id="passwordNote">(Leave blank to keep current password)</span></label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-actions">
                <button type="button" id="cancelBtn" class="action-btn secondary">Cancel</button>
                <button type="submit" name="save_user" class="action-btn">Save User</button>
            </div>
        </form>
    </div>
</div>

<!-- Add some additional CSS for the user details view -->
<style>
    .back-button {
        margin-bottom: 20px;
    }

    .user-details-card, 
    .feedback-history-section {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 24px;
        margin-bottom: 24px;
    }

    .user-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        border-bottom: 1px solid #eee;
        padding-bottom: 12px;
    }

    .user-header h2 {
        margin: 0;
    }

    .user-detail {
        display: flex;
        margin-bottom: 16px;
    }

    .user-detail strong {
        width: 150px;
        font-weight: 600;
    }

    .feedback-history-section h2 {
        margin-top: 0;
        margin-bottom: 20px;
    }

    .feedback-table {
        width: 100%;
        border-collapse: collapse;
    }

    .feedback-table th, 
    .feedback-table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .feedback-table th {
        background-color: #f5f5f5;
        font-weight: 600;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
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

    .view-link {
        color: #1a56db;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .view-link i {
        margin-right: 4px;
    }

    .pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .pagination a, 
    .pagination .current-page {
        display: inline-block;
        padding: 8px 12px;
        margin: 0 4px;
        border-radius: 4px;
        text-decoration: none;
    }

    .pagination a {
        background-color: #f5f5f5;
        color: #333;
    }

    .pagination .current-page {
        background-color: #1a56db;
        color: white;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Modal elements
    const userModal = document.getElementById('userModal');
    const closeButtons = document.querySelectorAll('.close');
    const cancelBtn = document.getElementById('cancelBtn');
    
    // Function to close modal
    function closeModal() {
        userModal.style.display = 'none';
    }
    
    // Close buttons
    closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });
    
    // Cancel button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === userModal) {
            closeModal();
        }
    });
    
    // Function to handle edit button click
    function handleEditClick() {
        userModal.style.display = 'block';
    }
    
    // Add event listener to edit button
    const editButton = document.querySelector('.edit-btn');
    if (editButton) {
        editButton.addEventListener('click', handleEditClick);
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
<?php
$pageTitle = "Manage Users";
$currentPage = "manage_users"; // Used for highlighting active menu item
require_once '../includes/header.php';
require_once '../includes/db.php'; // Make sure you have a db connection file
require_once '../includes/pagination.php'; // Include the pagination helper

// Require user to be logged in
requireLogin();

// Get the current user's ID
$userID = $_SESSION['user_id'];

// Handle form submissions
$message = '';
$messageType = 'success'; // Default to success, change to error if needed

// Add or update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $user_id = $_POST['user_id'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($email) || empty($role)) {
        $message = "Please fill in all required fields";
        $messageType = 'error';
    } else {
        if (empty($user_id)) {
            // Add new user - first check if email already exists
            $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM user WHERE email = ?");
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $check_row = $check_result->fetch_assoc();
            $check_stmt->close();
            
            if ($check_row['count'] > 0) {
                $message = "Error: Email address already exists. Please use a different email.";
                $messageType = 'error';
            } else if (empty($password)) {
                $message = "Error: Password is required for new users";
                $messageType = 'error';
            } else {
                // Validate password strength (same as in signup)
                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
                    $message = "Error: Password must be at least 6 characters long and include an uppercase letter, lowercase letter, number, and special character.";
                    $messageType = 'error';
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Get the highest userID and add 1 to ensure uniqueness
                    $result = $conn->query("SELECT MAX(userID) AS max_id FROM user");
                    $row = $result->fetch_assoc();
                    $new_user_id = ($row['max_id'] ?? 0) + 1;
                    
                    try {
                        $stmt = $conn->prepare("INSERT INTO user (userID, username, email, role, password) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("issss", $new_user_id, $username, $email, $role, $hashed_password);
                        
                        if ($stmt->execute()) {
                            $message = "User added successfully!";
                            $messageType = 'success';
                        } else {
                            $message = "Error adding user: " . $conn->error;
                            $messageType = 'error';
                        }
                        $stmt->close();
                    } catch (mysqli_sql_exception $e) {
                        // This is a fallback in case the initial check didn't catch the duplicate
                        if ($e->getCode() == 1062) { // Duplicate entry error
                            $message = "Error: Email address already exists. Please use a different email.";
                            $messageType = 'error';
                        } else {
                            $message = "Error adding user: " . $e->getMessage();
                            $messageType = 'error';
                        }
                    }
                }
            }
        } else {
            // Update existing user - first check if email already exists but belongs to a different user
            $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM user WHERE email = ? AND userID != ?");
            $check_stmt->bind_param("si", $email, $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $check_row = $check_result->fetch_assoc();
            $check_stmt->close();
            
            if ($check_row['count'] > 0) {
                $message = "Error: Email address already exists. Please use a different email.";
                $messageType = 'error';
            } else {
                try {
                    if (!empty($password)) {
                        // Validate password strength if password is being changed
                        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
                            $message = "Error: Password must be at least 6 characters long and include an uppercase letter, lowercase letter, number, and special character.";
                            $messageType = 'error';
                        } else {
                            // Update with new password
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $conn->prepare("UPDATE user SET username = ?, email = ?, role = ?, password = ? WHERE userID = ?");
                            $stmt->bind_param("ssssi", $username, $email, $role, $hashed_password, $user_id);
                            
                            if ($stmt->execute()) {
                                $message = "User updated successfully!";
                                $messageType = 'success';
                            } else {
                                $message = "Error updating user: " . $conn->error;
                                $messageType = 'error';
                            }
                            $stmt->close();
                        }
                    } else {
                        // Update without changing password
                        $stmt = $conn->prepare("UPDATE user SET username = ?, email = ?, role = ? WHERE userID = ?");
                        $stmt->bind_param("sssi", $username, $email, $role, $user_id);
                        
                        if ($stmt->execute()) {
                            $message = "User updated successfully!";
                            $messageType = 'success';
                        } else {
                            $message = "Error updating user: " . $conn->error;
                            $messageType = 'error';
                        }
                        $stmt->close();
                    }
                } catch (mysqli_sql_exception $e) {
                    if ($e->getCode() == 1062) { // Duplicate entry error
                        $message = "Error: Email address already exists. Please use a different email.";
                        $messageType = 'error';
                    } else {
                        $message = "Error updating user: " . $e->getMessage();
                        $messageType = 'error';
                    }
                }
            }
        }
    }
}

// Delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    // Get the user_id from POST and validate it
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    
    // Log the received value for debugging
    error_log("Delete request received for user ID: " . $user_id);
    
    if ($user_id > 0) { // Make sure it's a valid ID (greater than zero)
        try {
            // Simple direct deletion approach
            $stmt = $conn->prepare("DELETE FROM user WHERE userID = ?");
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                $message = "User deleted successfully!";
                $messageType = 'success';
                error_log("User deleted successfully: " . $user_id);
            } else {
                $message = "Error deleting user: " . $conn->error;
                $messageType = 'error';
                error_log("Error during DELETE execution: " . $conn->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $message = "Error deleting user: " . $e->getMessage();
            $messageType = 'error';
            error_log("Exception during user deletion: " . $e->getMessage());
        }
    } else {
        $message = "Error: User ID not provided for deletion.";
        $messageType = 'error';
        error_log("Invalid user ID provided for deletion: " . $user_id);
    }
}

// Process search and filtering
$search = '';
$role_filter = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_submit'])) {
    $search = $_POST['search'] ?? '';
    $role_filter = $_POST['role_filter'] ?? '';
    // Store search parameters in session to maintain state after redirects
    $_SESSION['user_search'] = $search;
    $_SESSION['user_role_filter'] = $role_filter;
} else {
    // Retrieve from session if available
    $search = $_SESSION['user_search'] ?? '';
    $role_filter = $_SESSION['user_role_filter'] ?? '';
}

// Pagination parameters
$per_page = 5; // Number of records per page
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Set up the count query for pagination
$count_sql = "SELECT COUNT(*) AS total FROM user WHERE userID != ?";
$count_params = [$userID];
$count_types = "i";

if (!empty($search)) {
    $search_pattern = '%' . $search . '%';
    $count_sql .= " AND (username LIKE ? OR email LIKE ?)";
    $count_params[] = $search_pattern;
    $count_params[] = $search_pattern;
    $count_types .= "ss";
}

if (!empty($role_filter)) {
    $count_sql .= " AND role = ?";
    $count_params[] = $role_filter;
    $count_types .= "s";
}

// Use the Pagination class to get pagination data
$pagination = Pagination::paginate($conn, $count_sql, $count_params, $count_types, $per_page, $current_page);

// Fetch users for display with pagination
$sql = "SELECT * FROM user WHERE userID != ?";
$params = [$userID];
$types = "i";

if (!empty($search)) {
    $search_pattern = '%' . $search . '%';
    $sql .= " AND (username LIKE ? OR email LIKE ?)";
    $params[] = $search_pattern;
    $params[] = $search_pattern;
    $types .= "ss";
}

if (!empty($role_filter)) {
    $sql .= " AND role = ?";
    $params[] = $role_filter;
    $types .= "s";
}

$sql .= " ORDER BY username ASC LIMIT ? OFFSET ?";
$params[] = $pagination['limit'];
$params[] = $pagination['offset'];
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Build the URL pattern for pagination links
$url_pattern = '?page=%d';
if (!empty($search)) {
    $url_pattern .= '&search=' . urlencode($search);
}
if (!empty($role_filter)) {
    $url_pattern .= '&role_filter=' . urlencode($role_filter);
}
?>

<div class="main-container">
    <!-- Include sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <div class="content-area">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>Manage Users</h1>
                <div class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>
            
            <!-- Display messages from PHP processing -->
            <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <!-- Display client-side validation messages -->
            <div id="clientMessage" class="message" style="display: none;"></div>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-filter">
                <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                <select name="role_filter">
                    <option value="">All Roles</option>
                    <option value="Admin" <?php echo $role_filter === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="Customer" <?php echo $role_filter === 'Customer' ? 'selected' : ''; ?>>Customer</option>
                </select>
                <button type="submit" name="search_submit">Search</button>
                
                <button type="button" class="action-btn add-user-btn primary-btn" id="addUserBtn">Add New User</button>
            </form>
            
            <div>
                <table class="feedback-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td>
                                        <div class="user-actions">
                                            <button type="button" class="view-btn" data-id="<?php echo $user['userID']; ?>">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button type="button" class="edit-btn" data-id="<?php echo $user['userID']; ?>"
                                                    data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                    data-role="<?php echo htmlspecialchars($user['role']); ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="delete-btn" 
                                                    onclick="prepareDelete(<?php echo $user['userID']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Pagination controls using the Pagination class -->
                <?php echo Pagination::renderLinks($pagination['current_page'], $pagination['total_pages'], $url_pattern); ?>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New User</h2>
            <span class="close">&times;</span>
        </div>
        <div id="modalMessage" class="message" style="display: none;"></div>
        <form id="userForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <input type="hidden" id="user_id" name="user_id">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="Admin">Admin</option>
                    <option value="Customer">Customer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password <span id="passwordNote">(Required for new users)</span></label>
                <input type="password" id="password" name="password">
                <p class="password-requirements">
                    Password must be at least 6 characters long and include an uppercase letter, 
                    lowercase letter, number, and special character.
                </p>
            </div>
            <div class="form-actions">
                <button type="button" id="cancelBtn" class="action-btn secondary">Cancel</button>
                <button type="submit" name="save_user" class="action-btn">Save User</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirm Delete</h2>
            <span class="close">&times;</span>
        </div>
        <p>Are you sure you want to delete this user? This action cannot be undone. Deleting this user will remove all their feedback and related data.</p>
        <form id="deleteForm" method="POST">
            <input type="hidden" id="delete_user_id" name="user_id">
            <input type="hidden" name="delete_user" value="1">
            <div class="form-actions">
                <button type="button" id="cancelDeleteBtn" class="action-btn secondary">Cancel</button>
                <button type="submit" class="delete-btn">Yes, I want to Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Modal elements
    const userModal = document.getElementById('userModal');
    const deleteModal = document.getElementById('deleteModal');
    const addUserBtn = document.getElementById('addUserBtn');
    const closeButtons = document.querySelectorAll('.close');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const userForm = document.getElementById('userForm');
    const modalTitle = document.getElementById('modalTitle');
    const passwordNote = document.getElementById('passwordNote');
    const viewButtons = document.querySelectorAll('.view-btn');
    const modalMessage = document.getElementById('modalMessage');
    const clientMessage = document.getElementById('clientMessage');
    
    // Client-side password validation
    const passwordInput = document.getElementById('password');
    const passwordForm = document.getElementById('userForm');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(event) {
            const userId = document.getElementById('user_id').value;
            
            // Only validate password if it's provided or if it's a new user
            if (passwordInput.value || !userId) {
                // Check if password meets requirements
                const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;
                
                if (!passwordPattern.test(passwordInput.value)) {
                    event.preventDefault();
                    showModalError("Error: Password must be at least 6 characters long and include an uppercase letter, lowercase letter, number, and special character.");
                    return false;
                }
            }
            
            // If it's a new user, password is required
            if (!userId && !passwordInput.value) {
                event.preventDefault();
                showModalError("Error: Password is required for new users.");
                return false;
            }
            
            return true;
        });
    }
    
    // Function to show styled error messages in the modal
    function showModalError(message) {
        modalMessage.textContent = message;
        modalMessage.className = "message error";
        modalMessage.style.display = "block";
        
        // Scroll to the top of the modal to ensure error is visible
        modalMessage.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Optionally hide the message after some time
        setTimeout(() => {
            modalMessage.style.display = "none";
        }, 8000);
    }
    
    // Function to show styled error messages on the main page
    function showClientError(message) {
        clientMessage.textContent = message;
        clientMessage.className = "message error";
        clientMessage.style.display = "block";
        
        // Scroll to ensure error is visible
        clientMessage.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Optionally hide the message after some time
        setTimeout(() => {
            clientMessage.style.display = "none";
        }, 8000);
    }
    
    viewButtons.forEach(button => {
        button.addEventListener('click', handleViewClick);
    });
    
    // Function to handle view button click
    function handleViewClick(event) {
        const userId = this.getAttribute('data-id');
        // Redirect to a user details page
        window.location.href = `view_user.php?id=${userId}`;
    }
    
    // Function to open user modal for adding new user
    function openAddUserModal() {
        modalTitle.textContent = 'Add New User';
        document.getElementById('user_id').value = '';
        userForm.reset();
        passwordNote.textContent = '(Required for new users)';
        modalMessage.style.display = 'none'; // Clear any previous error messages
        userModal.style.display = 'block';
    }
    
    // Add event listener for Add New User button
    if (addUserBtn) {
        addUserBtn.addEventListener('click', openAddUserModal);
    }
    
    // Function to close all modals
    function closeModals() {
        userModal.style.display = 'none';
        deleteModal.style.display = 'none';
    }
    
    // Close buttons
    closeButtons.forEach(button => {
        button.addEventListener('click', closeModals);
    });
    
    // Cancel buttons
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModals);
    }
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', closeModals);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === userModal || event.target === deleteModal) {
            closeModals();
        }
    });
    
    // Function to handle edit button click
    function handleEditClick(event) {
        const userId = this.getAttribute('data-id');
        const username = this.getAttribute('data-username');
        const email = this.getAttribute('data-email');
        const role = this.getAttribute('data-role');
        
        document.getElementById('user_id').value = userId;
        document.getElementById('username').value = username;
        document.getElementById('email').value = email;
        document.getElementById('role').value = role;
        document.getElementById('password').value = '';
        
        modalTitle.textContent = 'Edit User';
        passwordNote.textContent = '(Leave blank to keep current password)';
        modalMessage.style.display = 'none'; // Clear any previous error messages
        userModal.style.display = 'block';
    }
    
    // Add event listeners to edit buttons
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', handleEditClick);
    });
});

// Function to prepare delete confirmation - defined in global scope
function prepareDelete(userId) {
    console.log('Preparing delete for user ID:', userId);
    document.getElementById('delete_user_id').value = userId;
    document.getElementById('deleteModal').style.display = 'block';
    
    // For debugging - log the value after setting it
    setTimeout(function() {
        console.log('Hidden field value is now:', document.getElementById('delete_user_id').value);
    }, 100);
}
</script>

<style>
.password-requirements {
    font-size: 0.85rem;
    color: #666;
    margin-top: 5px;
}
</style>

<?php require_once '../includes/footer.php'; ?>
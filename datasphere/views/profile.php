<?php
$pageTitle = "Profile";
$currentPage = "profile"; // Used for highlighting active menu item
require_once '../includes/header.php';
require_once '../includes/db.php';

// Require user to be logged in
requireLogin();

// Get the current user's ID
$userID = $_SESSION['user_id'];

// Process form submission
$updateMsg = '';
$updateError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate data
    if (empty($username) || empty($email)) {
        $updateError = "Username and email are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $updateError = "Invalid email format";
    } else {
        // Start with the basic profile update
        $query = "UPDATE user SET username = ?, email = ? WHERE userID = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $userID);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['user_name'] = $username;
            $_SESSION['user_email'] = $email;
            $updateMsg = "Profile updated successfully";
            
            // Handle password change if requested
            if (!empty($currentPassword) && !empty($newPassword)) {
                // Check if passwords match
                if ($newPassword !== $confirmPassword) {
                    $updateError = "New passwords do not match";
                } else {
                    // Verify current password
                    $query = "SELECT password FROM user WHERE userID = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "i", $userID);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    
                    if ($row = mysqli_fetch_assoc($result)) {
                        if (password_verify($currentPassword, $row['password'])) {
                            // Hash new password
                            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                            
                            // Update password
                            $query = "UPDATE user SET password = ? WHERE userID = ?";
                            $stmt = mysqli_prepare($conn, $query);
                            mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $userID);
                            
                            if (mysqli_stmt_execute($stmt)) {
                                $updateMsg = "Profile and password updated successfully";
                            } else {
                                $updateError = "Error updating password: " . mysqli_error($conn);
                            }
                        } else {
                            $updateError = "Current password is incorrect";
                        }
                    }
                }
            }
        } else {
            $updateError = "Error updating profile: " . mysqli_error($conn);
        }
    }
}

// Get user data
$query = "SELECT username, email, role FROM user WHERE userID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

<div class="main-container">
    <!-- Include sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <div class="content-area">
        <div class="profile-container">
            <div class="profile-header">
                <h1>Profile Settings</h1>
                <p>Manage your account information</p>
            </div>
            
            <?php if (!empty($updateMsg)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($updateMsg); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($updateError)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($updateError); ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-card">
                <div class="profile-picture">
                    <i class="fas fa-user-circle"></i>
                    <div class="user-role"><?php echo htmlspecialchars($user['role']); ?></div>
                </div>
                
                <div class="profile-details">
                    <form method="POST" action="profile.php">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="password-section">
                            <h3>Change Password</h3>
                            <p class="password-note">Leave blank to keep your current password</p>
                            
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password">
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password">
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-save">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
           
                
                
                <?php
                // Get user's feedback count
                $query = "SELECT COUNT(*) as feedback_count FROM feedback WHERE userID = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $userID);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $feedbackStats = mysqli_fetch_assoc($result);
                
                // Get user account age
                $query = "SELECT MIN(timestamp) as join_date FROM feedback WHERE userID = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $userID);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $dateStats = mysqli_fetch_assoc($result);
                $joinDate = $dateStats['join_date'] ? date('M j, Y', strtotime($dateStats['join_date'])) : 'No activity yet';
                ?>
                
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
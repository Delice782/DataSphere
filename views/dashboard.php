<?php
$pageTitle = "Dashboard";
require_once '../includes/header.php';

// Require user to be logged in
requireLogin();
?>

<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
    
    <div class="user-info">
        <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
    </div>
    
    <?php if (isAdmin()): ?>
        <div class="admin-panel">
            <h3>Admin Panel</h3>
            <p>As an admin, you can view and respond to user feedback.</p>
            <!-- Add admin-specific features here -->
        </div>
    <?php else: ?>
        <div class="customer-panel">
            <h3>Customer Panel</h3>
            <p>As a customer, you can provide feedback to help us improve.</p>
            <!-- Add customer-specific features here -->
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
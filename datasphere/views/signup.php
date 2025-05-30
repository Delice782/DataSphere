<?php
$pageTitle = "Sign Up";
require_once '../includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="form-container">
    <h2>Create an Account</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            Account created successfully! <a href="login.php">Login now</a>.
        </div>
    <?php endif; ?>
    
    <form action="../controllers/auth.php" method="post">
        <input type="hidden" name="action" value="signup">
        
        <div class="form-group">
            <label for="userName">Full Name</label>
            <input type="text" id="name" name="userName" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        
        <!-- Role selection removed - all new users are customers by default -->
        
        <button type="submit" class="btn-primary">Sign Up</button>
    </form>
    
    <p class="form-footer">Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php require_once '../includes/footer.php'; ?>
<?php 
$pageTitle = "Login"; 
require_once '../includes/header.php';

// Check for temp logout session
$logout_success = false;
if (isset($_SESSION['temp_logout']) && $_SESSION['temp_logout'] === true) {
    $logout_success = true;
    // Remove the temporary session variable
    unset($_SESSION['temp_logout']);
}

// Redirect if already logged in 
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit(); 
} 
?>

<div class="form-container">
    <h2>Login to Your Account</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($logout_success): ?>
        <div class="success-message">
            You have been successfully logged out.
        </div>
    <?php endif; ?>
    
    <form action="../controllers/auth.php" method="post">
        <input type="hidden" name="action" value="login">
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn-primary">Login</button>
    </form>
    
    <p class="form-footer">Don't have an account? <a href="signup.php">Sign up here</a></p>
</div>

<?php require_once '../includes/footer.php'; ?>
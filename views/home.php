<?php
$pageTitle = "Home";
require_once '../includes/header.php';
?>

<div class="hero-section">
    <h2>Welcome to DataSphere</h2>
    <p>Your platform for feedback and interactions.</p>
    
    <?php if (!isLoggedIn()): ?>
        <div class="cta-buttons">
            <a href="signup.php" class="btn-primary">Sign Up</a>
            <a href="login.php" class="btn-secondary">Login</a>
        </div>
    <?php else: ?>
        <div class="cta-buttons">
            <a href="dashboard.php" class="btn-primary">Go to Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<div class="features-section">
    <h3>Our Features</h3>
    <div class="feature-grid">
        <div class="feature-card">
            <h4>Customer Feedback</h4>
            <p>Share your thoughts and experiences with us.</p>
        </div>
        <div class="feature-card">
            <h4>Admin Responses</h4>
            <p>Get personalized responses from our team.</p>
        </div>
        <div class="feature-card">
            <h4>Real-time Notifications</h4>
            <p>Stay updated on your feedback status.</p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
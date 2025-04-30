<!-- Sidebar navigation -->
<div class="sidebar">
    <div class="sidebar-header">
        <h3>DataSphere</h3>
    </div>
    <div class="sidebar-menu">
        <ul>
            <li><a href="index.php" class="<?php echo $currentPage == 'home' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> <span>Home</span>
            </a></li>
            
            <li><a href="dashboard.php" class="<?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a></li>
            
            <?php if (isAdmin()): ?>
                <!-- Admin-specific sidebar items -->
                <li><a href="manage_feedback.php" class="<?php echo $currentPage == 'manage_feedback' ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i> <span>Manage Feedback</span>
                </a></li>
                
                <li><a href="manage_users.php" class="<?php echo $currentPage == 'manage_users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> <span>Manage Users</span>
                </a></li>
            <?php else: ?>
                <!-- Customer-specific sidebar items -->
                <li><a href="submit_feedback.php" class="<?php echo $currentPage == 'submit_feedback' ? 'active' : ''; ?>">
                    <i class="fas fa-paper-plane"></i> <span>Submit Feedback</span>
                </a></li>
                
                <li><a href="my_feedback.php" class="<?php echo $currentPage == 'my_feedback' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> <span>My Feedback</span>
                </a></li>
                
                <li><a href="my_responses.php" class="<?php echo $currentPage == 'feedback_responses' ? 'active' : ''; ?>">
                    <i class="fas fa-reply-all"></i> <span>Feedback Responses</span>
                </a></li>
            <?php endif; ?>
            
            <!-- Common items for both roles -->
            <li><a href="profile.php" class="<?php echo $currentPage == 'profile' ? 'active' : ''; ?>">
                <i class="fas fa-user"></i> <span>Profile</span>
            </a></li>
            
            <li><a href="../controllers/logout.php" class="<?php echo $currentPage == 'logout' ? 'active' : ''; ?>">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a></li>
        </ul>
    </div>
</div>
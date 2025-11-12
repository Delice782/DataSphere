      
<?php
require_once __DIR__ . '/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'DataSphere'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link rel="stylesheet" href="../assets/css/delete_account.css">
    <link rel="stylesheet" href="../assets/css/submit_feedback.css">
    <link rel="stylesheet" href="../assets/css/my_feedback.css">
    <link rel="stylesheet" href="../assets/css/manage_feedback.css">
    <link rel="stylesheet" href="../assets/css/manage_users.css">
    <link rel="stylesheet" href="../assets/css/respond_feedback.css">
    <link rel="stylesheet" href="../assets/css/feedback_styles.css">
    <link rel="stylesheet" href="../assets/css/my_responses.css">
    
</head>

<body>
    <header>
        <div class="logo">
            <h1>DataSphere</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../views/index.php#home">Home</a></li>
                <li><a href="../views/index.php#features">Features</a></li>
                <li><a href="../views/index.php#testimonials">Testimonials</a></li>
                <li><a href="../views/index.php#about">About Us</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="../views/dashboard.php">Dashboard</a></li>
                    <li><a href="../controllers/logout.php" class="btn-primary">Logout</a></li>
                <?php else: ?>
                    <li><a href="../views/login.php" class="btn-primary">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>







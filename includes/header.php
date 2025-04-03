<?php
require_once __DIR__ . '/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataSphere - <?php echo isset($pageTitle) ? $pageTitle : 'Welcome'; ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>DataSphere</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../views/home.php">Home</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="../views/dashboard.php">Dashboard</a></li>
                    <li><a href="../controllers/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="../views/login.php">Login</a></li>
                    <li><a href="../views/signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
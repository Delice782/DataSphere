<?php
$pageTitle = "Home";
require_once '../includes/header.php';
include "profile_management.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile_manage.css">
    <title>Profile Management</title>
</head>

<body>
    <header>

        <h1>Profile Management</h1>
    
        <nav>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="dashboard.html">Dashboard</a></li>
                <li><a href="logout.php" onclick="logout()">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    
    <div class="container">
    
        <h2>Edit Profile</h2>
        <div class="notification" id="notification">Profile updated successfully!</div>
        <form id="profile-form">
    
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="johndoe" required>
    
            </div>
    
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" value="Web Developer" required>
    
            </div>
            <button type="submit" class="btn">Save Changes</button>
    
        </form>
        <div class="delete-account" onclick="confirmDelete()">Delete Account</div>
    
    </div>
    
    
    <footer>
        <p>&copy; 2025 Datasphere. All rights reserved.</p>
    </footer>


    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                document.querySelector('form').submit();
            }
        }
    
    </script>

</body>
</html>


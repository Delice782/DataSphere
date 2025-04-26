<?php
$pageTitle = "Dashboard";
$currentPage = "dashboard"; // Used for highlighting active menu item
require_once '../includes/header.php';
require_once '../includes/db.php'; // Make sure you have a db connection file

// Require user to be logged in
requireLogin();

// Get the current user's ID
$userID = $_SESSION['user_id'];

// Define feedback categories with their display names and colors for pie chart
$feedbackCategories = [
    'bug' => ['name' => 'Bug Report', 'color' => '#FF6384'],
    'feature' => ['name' => 'Feature Request', 'color' => '#36A2EB'],
    'ui' => ['name' => 'UI/UX Improvement', 'color' => '#FFCE56'],
    'performance' => ['name' => 'Performance Issue', 'color' => '#4BC0C0'],
    'content' => ['name' => 'Content Feedback', 'color' => '#9966FF'],
    'other' => ['name' => 'Other', 'color' => '#C9CBCF']
];
?>

<div class="main-container">
    <!-- Include sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <div class="content-area">
        <?php if (isAdmin()): ?>
            <!-- Admin Dashboard -->
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
                <div class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-details">
                        <div class="stat-title">Total Users</div>
                        <div class="stat-number">
                            <?php 
                            // Get total users count excluding the current user
                            $stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM user WHERE userID != ?");
                            $stmt->bind_param("i", $userID);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo htmlspecialchars($row['total_users']);
                            $stmt->close();
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-comments"></i></div>
                    <div class="stat-details">
                        <div class="stat-title">Total Feedback</div>
                        <div class="stat-number">
                            <?php 
                            // Get total feedback count
                            $query = "SELECT COUNT(*) as total_feedback FROM feedback";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            echo htmlspecialchars($row['total_feedback']); 
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-details">
                        <div class="stat-title">Pending Feedback</div>
                        <div class="stat-number">
                            <?php 
                            // Get pending feedback count
                            $query = "SELECT COUNT(*) as pending_feedback FROM feedback WHERE status = 'pending'";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            
                            // If there's no status field, use a placeholder
                            $pendingCount = isset($row['pending_feedback']) ? $row['pending_feedback'] : 0;
                            echo htmlspecialchars($pendingCount);
                            
                            // Check if pending feedback increased compared to previous period
                            $trend = "up"; // or "down" based on your comparison
                            ?>
                            <span class="trend-indicator <?php echo $trend; ?>">
                                <i class="fas fa-arrow-<?php echo $trend; ?>"></i>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card chart-card">
                    <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
                    <div class="stat-details">
                        <div class="stat-title">Feedback Categories</div>
                        <div class="stat-chart">
                            <?php
                            // Get category distribution
                            $query = "SELECT category, COUNT(*) as count FROM feedback GROUP BY category ORDER BY count DESC";
                            $result = mysqli_query($conn, $query);
                            
                            if (mysqli_num_rows($result) > 0) {
                                // Prepare data for pie chart
                                $chartData = [];
                                $categoryLabels = [];
                                $categoryColors = [];
                                
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $categoryKey = $row['category'];
                                    $count = (int)$row['count'];
                                    
                                    // Store the count
                                    $chartData[] = $count;
                                    
                                    // Get the display name for this category
                                    $displayName = isset($feedbackCategories[$categoryKey]) ? 
                                        $feedbackCategories[$categoryKey]['name'] : 
                                        ucfirst($categoryKey);
                                    $categoryLabels[] = $displayName;
                                    
                                    // Get the color for this category
                                    $color = isset($feedbackCategories[$categoryKey]) ? 
                                        $feedbackCategories[$categoryKey]['color'] : 
                                        '#' . substr(md5($categoryKey), 0, 6);
                                    $categoryColors[] = $color;
                                }
                                
                                // Convert PHP arrays to JSON for JavaScript
                                $chartDataJSON = json_encode($chartData);
                                $categoryLabelsJSON = json_encode($categoryLabels);
                                $categoryColorsJSON = json_encode($categoryColors);
                            ?>
                                <!-- Add canvas for Chart.js -->
                                <canvas id="feedbackPieChart"></canvas>
                                
                                <!-- Add Chart.js script -->
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const ctx = document.getElementById('feedbackPieChart').getContext('2d');
                                        const myPieChart = new Chart(ctx, {
                                            type: 'pie',
                                            data: {
                                                labels: <?php echo $categoryLabelsJSON; ?>,
                                                datasets: [{
                                                    data: <?php echo $chartDataJSON; ?>,
                                                    backgroundColor: <?php echo $categoryColorsJSON; ?>,
                                                    borderWidth: 1
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                plugins: {
                                                    legend: {
                                                        position: 'right',
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function(context) {
                                                                const label = context.label || '';
                                                                const value = context.raw || 0;
                                                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                                                const percentage = Math.round((value / total) * 100);
                                                                return `${label}: ${value} (${percentage}%)`;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    });
                                </script>
                            <?php
                            } else {
                                echo '<p>No feedback category data available</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Feedback Table -->
            <div class="recent-feedback">
                <h2>Recent Feedback</h2>
                <table class="feedback-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Category</th>
                            <th>Content</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get recent feedback with user information and response status
                        $query = "SELECT f.feedbackID, f.content, f.category, f.timestamp, f.status, 
                                u.username, u.email,
                                (SELECT COUNT(*) FROM response WHERE feedbackID = f.feedbackID) as has_response
                                FROM feedback f 
                                JOIN user u ON f.userID = u.userID 
                                WHERE f.userID != ?
                                ORDER BY f.timestamp DESC 
                                LIMIT 5";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "i", $userID);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $categoryKey = $row['category'];
                                $categoryName = isset($feedbackCategories[$categoryKey]) ? 
                                    $feedbackCategories[$categoryKey]['name'] : 
                                    ucfirst($categoryKey);
                                    
                                $hasResponse = $row['has_response'] > 0 || $row['status'] === 'responded';
                                
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['username']) . ' (' . htmlspecialchars($row['email']) . ')</td>';
                                echo '<td>' . htmlspecialchars($categoryName) . '</td>';
                                echo '<td>' . htmlspecialchars($row['content']) . '</td>';
                                echo '<td>' . date('M j, Y', strtotime($row['timestamp'])) . '</td>';
                                echo '<td>';
                                
                                if ($hasResponse) {
                                    echo '<span class="status-responded">Responded</span>';
                                } else {
                                    echo '<a href="respond_feedback.php?id=' . $row['feedbackID'] . '" class="respond-btn">Respond</a>';
                                }
                                
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5" class="no-data">No feedback available</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
        <?php else: ?>
            <!-- Customer Dashboard -->
            <div class="dashboard-header">
                <h1>Customer Dashboard</h1>
                <div class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</div>
            </div>
            
            <?php
            // Get user feedback stats
            $query = "SELECT COUNT(*) as total_feedback FROM feedback WHERE userID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $userID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $feedbackStats = mysqli_fetch_assoc($result);
            
            // Get most common category
            $query = "SELECT category, COUNT(*) as count FROM feedback WHERE userID = ? GROUP BY category ORDER BY count DESC LIMIT 1";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $userID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $categoryStats = mysqli_fetch_assoc($result);
            $topCategory = $categoryStats ? $categoryStats['category'] : 'None';
            $topCategoryName = isset($feedbackCategories[$topCategory]) ? $feedbackCategories[$topCategory]['name'] : ucfirst($topCategory);
            ?>
            
            <!-- Stats for customer -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-comment"></i></div>
                    <div class="stat-details">
                        <div class="stat-title">Total Feedback Submitted</div>
                        <div class="stat-number">
                            <?php echo htmlspecialchars($feedbackStats['total_feedback']); ?>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-tag"></i></div>
                    <div class="stat-details">
                        <div class="stat-title">Most Common Category</div>
                        <div class="stat-number">
                            <?php echo htmlspecialchars($topCategoryName); ?>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar"></i></div>
                    <div class="stat-details">
                        <div class="stat-title">Last Feedback</div>
                        <div class="stat-number">
                            <?php 
                            $query = "SELECT timestamp FROM feedback WHERE userID = ? ORDER BY timestamp DESC LIMIT 1";
                            $stmt = mysqli_prepare($conn, $query);
                            mysqli_stmt_bind_param($stmt, "i", $userID);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            
                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                echo date('M j, Y', strtotime($row['timestamp']));
                            } else {
                                echo 'No feedback yet';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Customer feedback section -->
            <div class="your-feedback">
                <h2>Your Recent Feedback</h2>
                <div class="feedback-actions">
                    <a href="submit_feedback.php" class="action-btn">Create New Feedback</a>
                    <a href="my_feedback.php" class="action-btn secondary">View All My Feedback</a>
                </div>
                
                <?php
                // Get user's recent feedback with response status
                $query = "SELECT f.*, 
                        (SELECT COUNT(*) FROM response WHERE feedbackID = f.feedbackID) as has_response 
                        FROM feedback f 
                        WHERE f.userID = ? 
                        ORDER BY f.timestamp DESC 
                        LIMIT 3";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $userID);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    echo '<table class="feedback-table">';
                    echo '<thead><tr><th>Category</th><th>Content</th><th>Date</th><th>Status</th></tr></thead>';
                    echo '<tbody>';
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        $categoryKey = $row['category'];
                        $categoryName = isset($feedbackCategories[$categoryKey]) ? 
                            $feedbackCategories[$categoryKey]['name'] : 
                            ucfirst($categoryKey);
                        
                        $hasResponse = $row['has_response'] > 0 || $row['status'] === 'responded';
                        $statusClass = $hasResponse ? 'status-responded' : 'status-pending';
                        $statusText = $hasResponse ? 'Responded' : 'Pending';
                        
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($categoryName) . '</td>';
                        echo '<td>' . htmlspecialchars($row['content']) . '</td>';
                        echo '<td>' . date('M j, Y', strtotime($row['timestamp'])) . '</td>';
                        echo '<td><span class="' . $statusClass . '">' . $statusText . '</span></td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                } else {
                    echo '<div class="empty-state">';
                    echo '<p>You haven\'t submitted any feedback yet. We\'d love to hear your thoughts!</p>';
                    echo '</div>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add some CSS for the response status styling -->
<style>
.status-responded {
    display: inline-block;
    padding: 4px 8px;
    background-color: #4CAF50;
    color: white;
    border-radius: 4px;
    font-size: 0.9em;
}

.status-pending {
    display: inline-block;
    padding: 4px 8px;
    background-color: #FF9800;
    color: white;
    border-radius: 4px;
    font-size: 0.9em;
}

.respond-btn {
    display: inline-block;
    padding: 4px 8px;
    background-color: #1a56db;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.9em;
}

.respond-btn:hover {
    background-color: #1a56db;
}

.feedback-table td {
    white-space: normal !important;
    word-break: break-word;
    max-width: 300px;
}

/* Target the content column specifically */
.feedback-table td:nth-child(3) {
    max-width: 300px;
}

/* Make sure content cells don't expand the table */
.feedback-content {
    white-space: normal;
    word-break: break-word;
    max-width: 100%;
    line-height: 1.5;
    overflow-wrap: break-word;
}
</style>

<?php require_once '../includes/footer.php'; ?>

<?php
$pageTitle = "Dashboard";
$currentPage = "dashboard"; 
require_once '../includes/header.php';
require_once '../includes/db.php'; 

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
                            
                            
                            $trend = "up"; 
                            ?>
                            <span class="trend-indicator <?php echo $trend; ?>">
                                <i class="fas fa-arrow-<?php echo $trend; ?>"></i>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
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
                                                maintainAspectRatio: false,
                                                plugins: {
                                                    legend: {
                                                        position: 'bottom',
                                                        display: true,
                                                        labels: {
                                                            padding: 20,
                                                            boxWidth: 15
                                                        }
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
                                    echo '<a href="respond_feedback.php?id=' . $row['feedbackID'] . '" class="respond-btn"><i class="fas fa-reply"></i>Respond</a>';
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
            $query = "SELECT 
                        COUNT(*) as total_feedback,
                        SUM(CASE WHEN 
                            (SELECT COUNT(*) FROM response WHERE feedbackID = f.feedbackID) > 0 
                            OR f.status = 'responded'
                            THEN 1 ELSE 0 END) as responded_count
                      FROM feedback f 
                      WHERE f.userID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $userID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $feedbackStats = mysqli_fetch_assoc($result);
            
            // Make sure we have values and not null
            $totalFeedback = $feedbackStats['total_feedback'] ?? 0;
            $respondedCount = $feedbackStats['responded_count'] ?? 0;
            
            // Calculate the percentage
            $respondedPercentage = ($totalFeedback > 0) ? round(($respondedCount / $totalFeedback) * 100) : 0;
            ?>
            
            <!-- Stats for customer -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-comment"></i></div>
                    <div class="stat-details">
                        <div class="stat-title">Total Feedback Submitted</div>
                        <div class="stat-number">
                            <?php echo htmlspecialchars($totalFeedback); ?>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-reply"></i></div>
                    <div class="stat-details">
                        <div class="stat-title">Feedback Responded</div>
                        <div class="stat-number">
                            <?php echo $respondedCount; ?> / <?php echo $totalFeedback; ?>
                            <span class="stat-percentage">(<?php echo $respondedPercentage; ?>%)</span>
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

<style>
    .content-area {
        flex: 1;
        padding: 25px;
    }

    /* Dashboard header */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .dashboard-header h1 {
        margin: 0;
        font-size: 1.8rem;
        color: #333;
    }

    .user-welcome {
        font-size: 1rem;
        color: #666;
    }

    .stat-percentage{
        font-size: 1rem;
        color: #6b7280;
        margin-left: 5px;
    }

    /* Dashboard Layout Improvements */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    /* Make stat titles bigger and bolder */
    .stat-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    /* Make stat numbers more prominent */
    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e6f0ff;
        color: #1a56db;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-right: 15px;
    }

    .stat-details {
        flex: 1;
    }

    .stat-title {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
    }

    .trend-indicator {
        font-size: 0.8rem;
        margin-left: 8px;
        display: flex;
        align-items: center;
    }

    .trend-indicator.up {
        color: #10b981;
    }

    .trend-indicator.down {
        color: #ef4444;
    }

    /* Chart styling */
    .stat-chart {
        position: relative;
        height: 200px;
        width: 100%;
        margin-top: 10px;
    }

    /* Status indicators */
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

    /* Message alerts */
    .message {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .message.success {
        background-color: #d1fae5;
        color: #065f46;
    }

    .message.error {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 8px;
        color: #6b7280;
    }

    /* Recent Feedback Sections */
    .recent-feedback,
    .your-feedback {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .recent-feedback h2,
    .your-feedback h2 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 1.4rem;
        color: #333;
    }

    /* Feedback table styling */
    .feedback-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .feedback-table thead th {
        background-color: #f8f9fa;
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #4b5563;
        border-bottom: 1px solid #e5e7eb;
    }

    .feedback-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #e5e7eb;
        color: #4b5563;
        vertical-align: top;
    }

    .feedback-table tbody tr:last-child td {
        border-bottom: none;
    }

    .feedback-table tbody tr:hover {
        background-color: #f9fafb;
    }
    /* No data message */
    .feedback-table .no-data {
        text-align: center;
        padding: 30px 15px;
        color: #6b7280;
        font-style: italic;
    }
    /* Customer feedback action buttons */
    .feedback-actions {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }

    .action-btn {
        display: inline-block;
        padding: 10px 16px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .action-btn {
        background-color: #1a56db;
        color: white;
    }

    .action-btn:hover {
        background-color: #1a56db;
        transform: translateY(-2px);
    }

    .action-btn.secondary {
        background-color: white;
        color: #1a56db;
        border: 1px solid #1a56db;
    }

    .action-btn.secondary:hover {
        background-color: #f0f5ff;
    }

    .respond-btn{
        display: inline-block;
        padding: 4px 8px;
        background-color: #1a56db;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.9em;
    }

    .respond-btn:hover {
        background-color: #059669;
    }


    /* Responsive adjustments */
    @media (max-width: 992px) {
        .main-container {
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: 1fr;
        }
        
        .main-container {
            flex-direction: column;
        }
        
        .feedback-table {
            display: block;
            overflow-x: auto;
        }
        
        .feedback-actions {
            flex-direction: column;
        }
        
        .action-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>
<?php require_once '../includes/footer.php'; ?>

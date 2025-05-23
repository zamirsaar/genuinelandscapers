<?php
/**
 * Admin dashboard for Genuine Landscapers website
 * Central hub for managing website content and features
 */

// Include configuration
require_once '../php/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: ../admin-login.php');
    exit;
}

// Get user information
$user_name = $_SESSION['user_name'] ?? 'Administrator';
$user_role = $_SESSION['user_role'] ?? 'viewer';

// Set page title
$page_title = "Admin Dashboard";

// Include admin header
include 'includes/admin-header.php';
?>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo $user_name; ?></h1>
        <p>Manage your website content and settings</p>
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <?php
            // Count pending quote requests
            try {
                $db = get_db_connection();
                $stmt = $db->query("SELECT COUNT(*) FROM quote_requests WHERE status = 'pending'");
                $quote_count = $stmt->fetchColumn();
            } catch (PDOException $e) {
                $quote_count = 0;
                log_activity('error', 'Failed to count quote requests: ' . $e->getMessage());
            }
            ?>
            <div class="stat-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $quote_count; ?></h3>
                <p>Pending Quote Requests</p>
            </div>
            <a href="quotes.php" class="stat-link">View All</a>
        </div>
        
        <div class="stat-card">
            <?php
            // Count pending callbacks
            try {
                $db = get_db_connection();
                $stmt = $db->query("SELECT COUNT(*) FROM callback_requests WHERE status = 'pending'");
                $callback_count = $stmt->fetchColumn();
            } catch (PDOException $e) {
                $callback_count = 0;
                log_activity('error', 'Failed to count callback requests: ' . $e->getMessage());
            }
            ?>
            <div class="stat-icon">
                <i class="fas fa-phone-alt"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $callback_count; ?></h3>
                <p>Pending Callbacks</p>
            </div>
            <a href="callbacks.php" class="stat-link">View All</a>
        </div>
        
        <div class="stat-card">
            <?php
            // Count active offers
            try {
                $db = get_db_connection();
                $stmt = $db->query("SELECT COUNT(*) FROM offers WHERE active = 1");
                $offer_count = $stmt->fetchColumn();
            } catch (PDOException $e) {
                $offer_count = 0;
                log_activity('error', 'Failed to count offers: ' . $e->getMessage());
            }
            ?>
            <div class="stat-icon">
                <i class="fas fa-tag"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $offer_count; ?></h3>
                <p>Active Offers</p>
            </div>
            <a href="offers.php" class="stat-link">View All</a>
        </div>
        
        <div class="stat-card">
            <?php
            // Count subscribers
            try {
                $db = get_db_connection();
                $stmt = $db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE active = 1");
                $subscriber_count = $stmt->fetchColumn();
            } catch (PDOException $e) {
                $subscriber_count = 0;
                log_activity('error', 'Failed to count subscribers: ' . $e->getMessage());
            }
            ?>
            <div class="stat-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $subscriber_count; ?></h3>
                <p>Newsletter Subscribers</p>
            </div>
            <a href="subscribers.php" class="stat-link">View All</a>
        </div>
    </div>
    
    <div class="dashboard-recent">
        <div class="recent-section">
            <h2>Recent Quote Requests</h2>
            <div class="recent-content">
                <?php
                // Get recent quote requests
                try {
                    $db = get_db_connection();
                    $stmt = $db->query("SELECT * FROM quote_requests ORDER BY created_at DESC LIMIT 5");
                    $quotes = $stmt->fetchAll();
                    
                    if (count($quotes) > 0) {
                        echo '<table class="admin-table">';
                        echo '<thead><tr><th>Name</th><th>Service</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>';
                        echo '<tbody>';
                        
                        foreach ($quotes as $quote) {
                            echo '<tr>';
                            echo '<td>' . $quote['name'] . '</td>';
                            echo '<td>' . $quote['services'] . '</td>';
                            echo '<td>' . date('M j, Y', strtotime($quote['created_at'])) . '</td>';
                            echo '<td><span class="status-badge status-' . $quote['status'] . '">' . ucfirst($quote['status']) . '</span></td>';
                            echo '<td><a href="quotes.php?id=' . $quote['id'] . '" class="btn btn-sm btn-primary">View</a></td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<p>No quote requests found.</p>';
                    }
                } catch (PDOException $e) {
                    echo '<p>Error loading recent quote requests.</p>';
                    log_activity('error', 'Failed to load recent quotes: ' . $e->getMessage());
                }
                ?>
            </div>
            <div class="section-footer">
                <a href="quotes.php" class="btn btn-outline">View All Quote Requests</a>
            </div>
        </div>
        
        <div class="recent-section">
            <h2>Recent Callback Requests</h2>
            <div class="recent-content">
                <?php
                // Get recent callback requests
                try {
                    $db = get_db_connection();
                    $stmt = $db->query("SELECT * FROM callback_requests ORDER BY created_at DESC LIMIT 5");
                    $callbacks = $stmt->fetchAll();
                    
                    if (count($callbacks) > 0) {
                        echo '<table class="admin-table">';
                        echo '<thead><tr><th>Name</th><th>Phone</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>';
                        echo '<tbody>';
                        
                        foreach ($callbacks as $callback) {
                            echo '<tr>';
                            echo '<td>' . $callback['name'] . '</td>';
                            echo '<td>' . $callback['phone'] . '</td>';
                            echo '<td>' . date('M j, Y', strtotime($callback['created_at'])) . '</td>';
                            echo '<td><span class="status-badge status-' . $callback['status'] . '">' . ucfirst($callback['status']) . '</span></td>';
                            echo '<td><a href="callbacks.php?id=' . $callback['id'] . '" class="btn btn-sm btn-primary">View</a></td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<p>No callback requests found.</p>';
                    }
                } catch (PDOException $e) {
                    echo '<p>Error loading recent callback requests.</p>';
                    log_activity('error', 'Failed to load recent callbacks: ' . $e->getMessage());
                }
                ?>
            </div>
            <div class="section-footer">
                <a href="callbacks.php" class="btn btn-outline">View All Callback Requests</a>
            </div>
        </div>
    </div>
    
    <div class="dashboard-actions">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
            <a href="tips.php" class="action-card">
                <div class="action-icon"><i class="fas fa-lightbulb"></i></div>
                <h3>Manage Tips</h3>
                <p>Add, edit, or remove landscaping tips and blog posts</p>
            </a>
            
            <a href="offers.php" class="action-card">
                <div class="action-icon"><i class="fas fa-percentage"></i></div>
                <h3>Manage Offers</h3>
                <p>Create and manage special promotions and offers</p>
            </a>
            
            <a href="testimonials.php" class="action-card">
                <div class="action-icon"><i class="fas fa-comment-alt"></i></div>
                <h3>Manage Testimonials</h3>
                <p>Review and publish customer testimonials</p>
            </a>
            
            <a href="services.php" class="action-card">
                <div class="action-icon"><i class="fas fa-tools"></i></div>
                <h3>Manage Services</h3>
                <p>Update service offerings and descriptions</p>
            </a>
            
            <a href="users.php" class="action-card">
                <div class="action-icon"><i class="fas fa-users"></i></div>
                <h3>Manage Users</h3>
                <p>Add or edit admin user accounts</p>
            </a>
            
            <a href="settings.php" class="action-card">
                <div class="action-icon"><i class="fas fa-cog"></i></div>
                <h3>Website Settings</h3>
                <p>Update general website settings and information</p>
            </a>
        </div>
    </div>
</div>

<?php
// Include admin footer
include 'includes/admin-footer.php';
?>

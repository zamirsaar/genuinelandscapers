<?php
/**
 * Admin header include for Genuine Landscapers website
 * Contains the header, navigation, and common elements for admin pages
 */

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: ../admin-login.php');
    exit;
}

// Get user role
$user_role = $_SESSION['user_role'] ?? 'viewer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Genuine Landscapers Admin</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="css/admin-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <img src="../images/logo.png" alt="Genuine Landscapers Logo" width="160">
                </a>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="quotes.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'quotes.php' ? 'active' : ''; ?>">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Quote Requests</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="callbacks.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'callbacks.php' ? 'active' : ''; ?>">
                            <i class="fas fa-phone-alt"></i>
                            <span>Callback Requests</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="tips.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tips.php' ? 'active' : ''; ?>">
                            <i class="fas fa-lightbulb"></i>
                            <span>Tips & Blog</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="offers.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'offers.php' ? 'active' : ''; ?>">
                            <i class="fas fa-percentage"></i>
                            <span>Special Offers</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="testimonials.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'testimonials.php' ? 'active' : ''; ?>">
                            <i class="fas fa-comment-alt"></i>
                            <span>Testimonials</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="services.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>">
                            <i class="fas fa-tools"></i>
                            <span>Services</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="subscribers.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'subscribers.php' ? 'active' : ''; ?>">
                            <i class="fas fa-envelope"></i>
                            <span>Newsletter</span>
                        </a>
                    </li>
                    
                    <?php if ($user_role === 'admin'): ?>
                    <li class="nav-item">
                        <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="../index.php" class="footer-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Website</span>
                </a>
                <a href="logout.php" class="footer-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        
        <!-- Admin Content -->
        <main class="admin-content">
            <header class="admin-header">
                <div class="header-search">
                    <form action="search.php" method="GET">
                        <input type="text" name="q" placeholder="Search...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="header-user">
                    <div class="user-info">
                        <span class="user-name"><?php echo $_SESSION['user_name'] ?? 'Administrator'; ?></span>
                        <span class="user-role"><?php echo ucfirst($_SESSION['user_role'] ?? 'admin'); ?></span>
                    </div>
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </header>
            
            <div class="admin-container">

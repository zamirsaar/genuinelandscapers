<?php
/**
 * Header include for Genuine Landscapers website
 * Contains the top bar, header, and navigation elements
 */

// Include configuration
$config_path = __DIR__ . '/../php/config.php';
require_once $config_path;

// Determine current page for active navigation
$current_page = basename($_SERVER['PHP_SELF']);

// Determine path depth for assets
$script_path = $_SERVER['PHP_SELF'];
$path_depth = substr_count($script_path, '/') - 1;
$base_url = $path_depth > 0 ? str_repeat('../', $path_depth) : './';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title . ' | ' . SITE_NAME : SITE_NAME . ' | Professional Landscaping Services in Windsor, ON'; ?></title>
  <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Genuine Landscapers provides professional landscaping maintenance services in Windsor and surrounding areas. We maintain, you relax with our expert lawn care, hedge trimming, and property maintenance.'; ?>">
  <meta name="keywords" content="landscaping, lawn maintenance, Windsor landscapers, property maintenance, hedge trimming, lawn care, garden maintenance">
  <link rel="canonical" href="<?php echo SITE_URL . '/' . ($current_page === 'index.php' ? '' : $current_page); ?>">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo SITE_URL . '/' . ($current_page === 'index.php' ? '' : $current_page); ?>">
  <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' | ' . SITE_NAME : SITE_NAME . ' | Professional Landscaping Services in Windsor, ON'; ?>">
  <meta property="og:description" content="<?php echo isset($page_description) ? $page_description : 'Professional landscaping maintenance services in Windsor and surrounding areas. We maintain, you relax.'; ?>">
  <meta property="og:image" content="<?php echo SITE_URL; ?>/images/og-image.jpg">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="<?php echo SITE_URL . '/' . ($current_page === 'index.php' ? '' : $current_page); ?>">
  <meta property="twitter:title" content="<?php echo isset($page_title) ? $page_title . ' | ' . SITE_NAME : SITE_NAME . ' | Professional Landscaping Services in Windsor, ON'; ?>">
  <meta property="twitter:description" content="<?php echo isset($page_description) ? $page_description : 'Professional landscaping maintenance services in Windsor and surrounding areas. We maintain, you relax.'; ?>">
  <meta property="twitter:image" content="<?php echo SITE_URL; ?>/images/og-image.jpg">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?php echo $base_url; ?>images/favicon.png">
  
  <!-- Stylesheets -->
  <link rel="stylesheet" href="<?php echo $base_url; ?>css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  
  <?php if (isset($additional_head)) echo $additional_head; ?>
</head>
<body>
  <!-- Top Bar -->
  <div class="top-bar">
    <div class="container">
      <div class="top-bar-content">
        <div class="top-bar-contact">
          <span><i class="fas fa-map-marker-alt"></i> 1609 Moy Ave Windsor ON</span>
          <span><i class="fas fa-envelope"></i> info@genuinelandscapers.ca</span>
          <span><i class="fas fa-phone"></i> 519 300 6434</span>
        </div>
        <div class="top-bar-social">
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>
  </div>

  <!-- Header/Nav -->
  <header class="site-header" id="header">
    <div class="container">
      <div class="header-container">
        <div class="logo">
          <a href="<?php echo $base_url; ?>index.php" aria-label="<?php echo SITE_NAME; ?> Home">
            <img src="<?php echo $base_url; ?>images/logo.png" alt="<?php echo SITE_NAME; ?> Logo" width="180" height="60">
          </a>
        </div>
        <nav class="navbar" aria-label="Main Navigation">
          <ul class="nav-links">
            <li><a href="<?php echo $base_url; ?>index.php" <?php echo $current_page === 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
            <li><a href="<?php echo $base_url; ?>about.php" <?php echo $current_page === 'about.php' ? 'class="active"' : ''; ?>>About</a></li>
            <li class="has-dropdown">
              <a href="<?php echo $base_url; ?>services.php" <?php echo $current_page === 'services.php' || strpos($current_page, 'services/') === 0 ? 'class="active"' : ''; ?>>Services</a>
              <ul class="dropdown">
                <li><a href="<?php echo $base_url; ?>services/residential.php">Residential</a></li>
                <li><a href="<?php echo $base_url; ?>services/commercial.php">Commercial</a></li>
                <li><a href="<?php echo $base_url; ?>services/senior.php">Senior Services</a></li>
              </ul>
            </li>
            <li><a href="<?php echo $base_url; ?>gallery.php" <?php echo $current_page === 'gallery.php' ? 'class="active"' : ''; ?>>Gallery</a></li>
            <li><a href="<?php echo $base_url; ?>tips.php" <?php echo $current_page === 'tips.php' ? 'class="active"' : ''; ?>>Tips</a></li>
            <li><a href="<?php echo $base_url; ?>testimonials.php" <?php echo $current_page === 'testimonials.php' ? 'class="active"' : ''; ?>>Testimonials</a></li>
            <li><a href="<?php echo $base_url; ?>contact.php" <?php echo $current_page === 'contact.php' ? 'class="active"' : ''; ?>>Contact</a></li>
          </ul>
          <div class="mobile-menu-toggle" aria-label="Toggle mobile menu">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </nav>
      </div>
    </div>
    <!-- Mobile Menu -->
    <div class="mobile-menu">
      <div class="container">
        <ul class="mobile-nav-links">
          <li><a href="<?php echo $base_url; ?>index.php" <?php echo $current_page === 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
          <li><a href="<?php echo $base_url; ?>about.php" <?php echo $current_page === 'about.php' ? 'class="active"' : ''; ?>>About</a></li>
          <li><a href="<?php echo $base_url; ?>services.php" <?php echo $current_page === 'services.php' ? 'class="active"' : ''; ?>>Services</a></li>
          <li><a href="<?php echo $base_url; ?>gallery.php" <?php echo $current_page === 'gallery.php' ? 'class="active"' : ''; ?>>Gallery</a></li>
          <li><a href="<?php echo $base_url; ?>tips.php" <?php echo $current_page === 'tips.php' ? 'class="active"' : ''; ?>>Tips</a></li>
          <li><a href="<?php echo $base_url; ?>testimonials.php" <?php echo $current_page === 'testimonials.php' ? 'class="active"' : ''; ?>>Testimonials</a></li>
          <li><a href="<?php echo $base_url; ?>contact.php" <?php echo $current_page === 'contact.php' ? 'class="active"' : ''; ?>>Contact</a></li>
        </ul>
      </div>
    </div>
  </header>

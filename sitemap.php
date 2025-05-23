<?php
/**
 * Stub page for Sitemap
 */

// Set page title and description
$page_title = "Sitemap";
$page_description = "Sitemap for Genuine Landscapers website. Find all pages and resources on our site.";

// Include header
include 'includes/header.php';
?>

  <!-- Page Header -->
  <section class="page-header">
    <div class="container">
      <h1>Sitemap</h1>
      <p>Find your way around our website</p>
    </div>
  </section>

  <!-- Sitemap Content -->
  <section class="sitemap-content">
    <div class="container">
      <div class="content-wrapper animate-on-scroll">
        <h2>Website Sitemap</h2>
        <p>Below you'll find a complete list of all pages available on our website.</p>
        
        <div class="sitemap-section">
          <h3>Main Pages</h3>
          <ul class="sitemap-list">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="gallery.php">Project Gallery</a></li>
            <li><a href="tips.php">Landscaping Tips & Hacks</a></li>
            <li><a href="testimonials.php">Client Testimonials</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="faq.php">Frequently Asked Questions</a></li>
          </ul>
        </div>
        
        <div class="sitemap-section">
          <h3>Service Pages</h3>
          <ul class="sitemap-list">
            <li><a href="services/residential.php">Residential Services</a></li>
            <li><a href="services/commercial.php">Commercial Services</a></li>
            <li><a href="services/senior.php">Senior Services</a></li>
          </ul>
        </div>
        
        <div class="sitemap-section">
          <h3>Legal Pages</h3>
          <ul class="sitemap-list">
            <li><a href="privacy-policy.php">Privacy Policy</a></li>
            <li><a href="terms-of-service.php">Terms of Service</a></li>
          </ul>
        </div>
        
        <div class="sitemap-section">
          <h3>Forms</h3>
          <ul class="sitemap-list">
            <li><a href="index.php#quote">Quote Request Form</a></li>
            <li><a href="index.php#schedule">Callback Request Form</a></li>
            <li><a href="contact.php">Contact Form</a></li>
          </ul>
        </div>
      </div>
    </div>
  </section>

<?php
// Include footer
include 'includes/footer.php';
?>

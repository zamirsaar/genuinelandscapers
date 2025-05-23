<?php
/**
 * Footer include for Genuine Landscapers website
 * Contains the footer, newsletter subscription, and copyright information
 */

// Include configuration if not already included
if (!defined('SITE_NAME')) {
    $config_path = __DIR__ . '/../php/config.php';
    require_once $config_path;
}

// Determine path depth for assets
$script_path = $_SERVER['PHP_SELF'];
$path_depth = substr_count($script_path, '/') - 1;
$base_url = $path_depth > 0 ? str_repeat('../', $path_depth) : './';
?>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-about">
          <a href="<?php echo $base_url; ?>index.php" class="footer-logo">
            <img src="<?php echo $base_url; ?>images/logo-footer.png" alt="<?php echo SITE_NAME; ?> Logo" width="180" height="60">
          </a>
          <p>We're your local landscaping maintenance experts, proudly serving Windsor and surrounding areas, focused solely on keeping your property looking its best year-round.</p>
          <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        <div class="footer-contact">
          <h3>Contact Info</h3>
          <p><i class="fas fa-map-marker-alt"></i> 1609 Moy Ave Windsor ON</p>
          <p><i class="fas fa-envelope"></i> <a href="mailto:info@genuinelandscapers.ca">info@genuinelandscapers.ca</a></p>
          <p><i class="fas fa-phone"></i> <a href="tel:+15193006434">519 300 6434</a></p>
          <div class="footer-hours">
            <h4>Business Hours</h4>
            <p>Monday-Friday: 8am-6pm</p>
            <p>Saturday: 9am-4pm</p>
            <p>Sunday: Closed</p>
          </div>
        </div>
        <div class="footer-newsletter">
          <h3>Newsletter</h3>
          <p>Subscribe to our newsletter for seasonal tips, special offers, and updates.</p>
          <form id="newsletterForm" action="<?php echo $base_url; ?>php/newsletter.php" method="POST" class="newsletter-form">
            <?php echo csrf_token_field(); ?>
            <div class="form-group">
              <input type="email" id="newsletter-email" name="email" placeholder="Your Email Address" required>
              <button type="submit" class="btn btn-primary">Subscribe</button>
            </div>
            <div id="newsletter-message" class="form-message"></div>
          </form>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        <div class="footer-bottom-links">
          <a href="<?php echo $base_url; ?>privacy-policy.php">Privacy Policy</a>
          <a href="<?php echo $base_url; ?>terms-of-service.php">Terms of Service</a>
          <a href="<?php echo $base_url; ?>sitemap.php">Sitemap</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Back to Top Button -->
  <a href="#" class="back-to-top" aria-label="Back to top"><i class="fas fa-chevron-up"></i></a>

  <!-- Scripts -->
  <script src="<?php echo $base_url; ?>js/main.js"></script>
  <?php if (isset($additional_scripts)) echo $additional_scripts; ?>
  
  <script>
    // Newsletter form submission
    document.addEventListener('DOMContentLoaded', function() {
      const newsletterForm = document.getElementById('newsletterForm');
      const newsletterMessage = document.getElementById('newsletter-message');
      
      if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          
          fetch(this.action, {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              newsletterMessage.innerHTML = '<div class="success-message">' + data.message + '</div>';
              newsletterForm.reset();
            } else {
              let errorHtml = '<div class="error-message">' + data.message + '</div>';
              if (data.errors) {
                for (const field in data.errors) {
                  errorHtml += '<div class="error-message">' + data.errors[field] + '</div>';
                }
              }
              newsletterMessage.innerHTML = errorHtml;
            }
          })
          .catch(error => {
            newsletterMessage.innerHTML = '<div class="error-message">An error occurred. Please try again later.</div>';
          });
        });
      }
    });
  </script>
</body>
</html>

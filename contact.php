<?php
/**
 * Contact page for Genuine Landscapers website
 */

// Set page title and description
$page_title = "Contact Us";
$page_description = "Contact Genuine Landscapers for professional landscaping services in Windsor and surrounding areas. Request a quote or schedule a consultation today.";

// Include header
include 'includes/header.php';
?>

  <!-- Page Header -->
  <section class="page-header">
    <div class="container">
      <h1>Contact Us</h1>
      <p>Get in touch with our team</p>
    </div>
  </section>

  <!-- Contact Content -->
  <section class="contact-content">
    <div class="container">
      <div class="contact-grid">
        <div class="contact-info animate-on-scroll">
          <h2>Our Information</h2>
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
              <h3>Address</h3>
              <p>1609 Moy Ave<br>Windsor, ON N8X 4C8</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-phone"></i>
            </div>
            <div class="info-content">
              <h3>Phone</h3>
              <p><a href="tel:+15193006434">519 300 6434</a></p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
              <h3>Email</h3>
              <p><a href="mailto:info@genuinelandscapers.ca">info@genuinelandscapers.ca</a></p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="info-content">
              <h3>Business Hours</h3>
              <p>Monday-Friday: 8am-6pm<br>
              Saturday: 9am-4pm<br>
              Sunday: Closed</p>
            </div>
          </div>
          <div class="social-links">
            <h3>Connect With Us</h3>
            <div class="social-icons">
              <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
              <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
        </div>
        <div class="contact-form-container animate-on-scroll">
          <h2>Send Us a Message</h2>
          <form class="contact-form" id="contactForm" action="php/contact.php" method="POST" data-ajax="true" data-message="contact-message">
            <?php echo csrf_token_field(); ?>
            <div class="form-group">
              <label for="contact-name">Your Name</label>
              <input type="text" id="contact-name" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
              <label for="contact-email">Email Address</label>
              <input type="email" id="contact-email" name="email" placeholder="Email Address" required>
            </div>
            <div class="form-group">
              <label for="contact-phone">Phone Number</label>
              <input type="tel" id="contact-phone" name="phone" placeholder="Phone Number" required>
            </div>
            <div class="form-group">
              <label for="contact-service">Service Interested In</label>
              <select id="contact-service" name="service">
                <option value="">Select a Service (Optional)</option>
                <option value="lawn-maintenance">Lawn Maintenance</option>
                <option value="hedge-trimming">Hedge Trimming</option>
                <option value="sprinkler-installation">Sprinkler Installation</option>
                <option value="seasonal-cleanup">Seasonal Cleanup</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label for="contact-message">Your Message</label>
              <textarea id="contact-message" name="message" placeholder="Your Message" rows="5" required></textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-full">Send Message</button>
            </div>
            <div id="contact-message" class="form-message"></div>
          </form>
        </div>
      </div>
      
      <div class="map-section animate-on-scroll">
        <h2>Find Us</h2>
        <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2950.6544512490137!2d-83.03857492346611!3d42.31490797129064!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x883b2d1a2c4d6e45%3A0x48d4f0e0e56db9e8!2s1609%20Moy%20Ave%2C%20Windsor%2C%20ON%20N8X%204C8%2C%20Canada!5e0!3m2!1sen!2sus!4v1684517631159!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
      
      <div class="cta-panel animate-on-scroll">
        <h2>Need a Quote?</h2>
        <p>If you're looking for a customized quote for your property, we're happy to help!</p>
        <a href="index.php#quote" class="btn btn-primary">Request a Quote</a>
      </div>
    </div>
  </section>

<?php
// Include footer
include 'includes/footer.php';
?>

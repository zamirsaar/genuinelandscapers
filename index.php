<?php
/**
 * Index page for Genuine Landscapers website
 * Landing page with hero section, tips, hacks, special offers, and more
 */

// Set page title and description
$page_title = "Professional Landscaping Services in Windsor, ON";
$page_description = "Genuine Landscapers provides professional landscaping maintenance services in Windsor and surrounding areas. We maintain, you relax with our expert lawn care, hedge trimming, and property maintenance.";

// Include header
include 'includes/header.php';
?>

  <!-- Hero Section -->
  <section class="hero" id="home">
    <div class="container">
      <div class="hero-inner">
        <div class="hero-content animate-on-scroll">
          <h1 class="hero-title">We Maintain, You Relax</h1>
          <p class="hero-subtitle">Professional landscaping services in Windsor and surrounding areas</p>
          <div class="hero-text">
            <p>Coming home from work or heading out on vacation without a worry—your property stays pristine so you can enjoy life, not maintain it. Our expert team handles everything your landscape needs.</p>
          </div>
          <div class="hero-buttons">
            <a href="#schedule" class="btn btn-primary">Schedule Free Consultation</a>
            <a href="#quote" class="btn btn-secondary">Request a Quote</a>
          </div>
        </div>
        <div class="hero-image animate-on-scroll">
          <img src="images/hero-background.webp" alt="Beautiful landscaped garden" width="600" height="400">
        </div>
      </div>
    </div>
  </section>

  <!-- Tips & Hacks Section -->
  <section class="tips-and-hacks">
    <div class="container">
      <div class="section-header">
        <h2>Landscaping Tips & Hacks</h2>
        <p>Expert advice to help you maintain a beautiful property</p>
      </div>
      <?php 
      // Set limit to 2 tips
      $limit = 2;
      include 'includes/tips.php'; 
      ?>
      <div class="section-footer">
        <a href="tips.php" class="btn btn-outline">View All Tips</a>
      </div>
    </div>
  </section>

  <!-- Special Offers -->
  <section class="special-offers">
    <div class="container">
      <div class="section-header">
        <h2>Special Offers</h2>
        <p>Take advantage of our limited-time promotions</p>
      </div>
      <?php 
      // Set limit to 2 offers
      $limit = 2;
      include 'includes/offers.php'; 
      ?>
      <div class="section-footer">
        <a href="#quote" class="btn btn-primary">Request a Quote</a>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section class="services" id="services">
    <div class="container">
      <div class="section-header">
        <h2>Our Services</h2>
        <p>Professional landscaping services tailored to your needs</p>
      </div>
      <div class="services-grid">
        <div class="service-card animate-on-scroll">
          <div class="service-icon">
            <i class="fas fa-leaf"></i>
          </div>
          <h3>Lawn Maintenance</h3>
          <p>Regular mowing, edging, and maintenance to keep your lawn looking its best year-round.</p>
          <a href="services/residential.php" class="service-link">Learn More</a>
        </div>
        <div class="service-card animate-on-scroll">
          <div class="service-icon">
            <i class="fas fa-tree"></i>
          </div>
          <h3>Hedge Trimming</h3>
          <p>Professional trimming and shaping of hedges and shrubs to enhance your property's appearance.</p>
          <a href="services/residential.php" class="service-link">Learn More</a>
        </div>
        <div class="service-card animate-on-scroll">
          <div class="service-icon">
            <i class="fas fa-tint"></i>
          </div>
          <h3>Sprinkler Systems</h3>
          <p>Installation and maintenance of efficient irrigation systems to keep your landscape hydrated.</p>
          <a href="services/residential.php" class="service-link">Learn More</a>
        </div>
        <div class="service-card animate-on-scroll">
          <div class="service-icon">
            <i class="fas fa-snowflake"></i>
          </div>
          <h3>Seasonal Cleanup</h3>
          <p>Spring and fall cleanup services to prepare your property for the changing seasons.</p>
          <a href="services/residential.php" class="service-link">Learn More</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Call Back & Reviews Section -->
  <section class="callback-and-reviews" id="schedule">
    <div class="container">
      <div class="two-column-grid">
        <div class="callback-panel animate-on-scroll">
          <h2>Get a Call Back</h2>
          <form class="callback-form" id="callbackForm" action="php/callback.php" method="POST" data-ajax="true" data-message="callback-message">
            <?php echo csrf_token_field(); ?>
            <div class="form-group">
              <label for="name">Your Name</label>
              <input type="text" id="name" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
              <label for="address">Property Address</label>
              <input type="text" id="address" name="address" placeholder="Property Address" required>
            </div>
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" placeholder="Phone" required>
            </div>
            <div class="form-group">
              <label for="service">Service Needed</label>
              <select id="service" name="service" required>
                <option value="">Choose a Service</option>
                <option value="lawn-maintenance">Lawn Maintenance</option>
                <option value="hedge-trimming">Hedge Trimming</option>
                <option value="sprinkler-installation">Sprinkler Installation</option>
                <option value="seasonal-cleanup">Seasonal Cleanup</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <textarea id="message" name="message" placeholder="Message" rows="4"></textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-full">Request Callback</button>
            </div>
            <div id="callback-message" class="form-message"></div>
          </form>
        </div>
        <div class="reviews-panel animate-on-scroll">
          <h2>What Our Clients Say</h2>
          <?php 
          // Set limit to 3 testimonials
          $limit = 3;
          include 'includes/testimonials.php'; 
          ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Quote Request Section -->
  <section class="quote-request" id="quote">
    <div class="container">
      <div class="section-header">
        <h2>Request a Free Quote</h2>
        <p>Tell us about your property and needs, and we'll provide a customized quote</p>
      </div>
      <form class="quote-form animate-on-scroll" id="quoteForm" action="php/quote-request.php" method="POST" data-ajax="true" data-message="quote-message">
        <?php echo csrf_token_field(); ?>
        <div class="form-grid">
          <div class="form-group">
            <label for="quote-name">Your Name</label>
            <input type="text" id="quote-name" name="name" placeholder="Your Name" required>
          </div>
          <div class="form-group">
            <label for="quote-email">Email Address</label>
            <input type="email" id="quote-email" name="email" placeholder="Email Address" required>
          </div>
          <div class="form-group">
            <label for="quote-phone">Phone Number</label>
            <input type="tel" id="quote-phone" name="phone" placeholder="Phone Number" required>
          </div>
          <div class="form-group">
            <label for="quote-address">Property Address</label>
            <input type="text" id="quote-address" name="address" placeholder="Property Address" required>
          </div>
          <div class="form-group">
            <label for="quote-property-type">Property Type</label>
            <select id="quote-property-type" name="property_type" required>
              <option value="">Select Property Type</option>
              <option value="residential">Residential</option>
              <option value="commercial">Commercial</option>
              <option value="multi-unit">Multi-Unit Residential</option>
            </select>
          </div>
          <div class="form-group">
            <label for="quote-property-size">Property Size (approx.)</label>
            <select id="quote-property-size" name="property_size" required>
              <option value="">Select Property Size</option>
              <option value="small">Small (under 5,000 sq ft)</option>
              <option value="medium">Medium (5,000-10,000 sq ft)</option>
              <option value="large">Large (over 10,000 sq ft)</option>
            </select>
          </div>
          <div class="form-group full-width">
            <label for="quote-services">Services Needed (select all that apply)</label>
            <div class="checkbox-group">
              <label class="checkbox-label">
                <input type="checkbox" name="services[]" value="lawn-maintenance"> Lawn Maintenance
              </label>
              <label class="checkbox-label">
                <input type="checkbox" name="services[]" value="hedge-trimming"> Hedge Trimming
              </label>
              <label class="checkbox-label">
                <input type="checkbox" name="services[]" value="sprinkler-installation"> Sprinkler Installation
              </label>
              <label class="checkbox-label">
                <input type="checkbox" name="services[]" value="seasonal-cleanup"> Seasonal Cleanup
              </label>
              <label class="checkbox-label">
                <input type="checkbox" name="services[]" value="other"> Other
              </label>
            </div>
          </div>
          <div class="form-group full-width">
            <label for="quote-message">Additional Details</label>
            <textarea id="quote-message" name="message" placeholder="Please provide any additional details about your property or service needs" rows="4"></textarea>
          </div>
          <div class="form-group full-width">
            <button type="submit" class="btn btn-primary btn-full">Submit Quote Request</button>
          </div>
          <div id="quote-message" class="form-message full-width"></div>
        </div>
      </form>
    </div>
  </section>

<?php
// Include footer
include 'includes/footer.php';
?>

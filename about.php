<?php
/**
 * About page for Genuine Landscapers website
 */

// Set page title and description
$page_title = "About Us";
$page_description = "Learn about Genuine Landscapers, your local landscaping maintenance experts serving Windsor and surrounding areas since 2015.";

// Include header
include 'includes/header.php';
?>

  <!-- Page Header -->
  <section class="page-header">
    <div class="container">
      <h1>About Us</h1>
      <p>Your local landscaping maintenance experts</p>
    </div>
  </section>

  <!-- About Content -->
  <section class="about-content">
    <div class="container">
      <div class="about-grid">
        <div class="about-image animate-on-scroll">
          <img src="images/hero-background2.jpg" alt="Genuine Landscapers team" width="500" height="350">
        </div>
        <div class="about-text animate-on-scroll">
          <h2>Our Story</h2>
          <p>Genuine Landscapers was founded in 2015 with a simple mission: to provide reliable, high-quality landscaping maintenance services that allow our clients to enjoy their outdoor spaces without the hassle of upkeep.</p>
          <p>What started as a small operation has grown into a trusted landscaping company serving Windsor and surrounding areas, including LaSalle, Tecumseh, and Lakeshore.</p>
          <p>Our focus remains solely on maintenance services, allowing us to perfect our craft and deliver consistent results that exceed expectations.</p>
        </div>
      </div>
      
      <div class="values-section animate-on-scroll">
        <h2>Our Values</h2>
        <div class="values-grid">
          <div class="value-card">
            <div class="value-icon">
              <i class="fas fa-handshake"></i>
            </div>
            <h3>Reliability</h3>
            <p>We show up when promised and deliver consistent results, every time. Our clients can count on us to maintain their properties with the same level of care throughout the seasons.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">
              <i class="fas fa-star"></i>
            </div>
            <h3>Quality</h3>
            <p>We don't cut corners. Our team takes pride in their work and pays attention to the details that make a difference in the appearance and health of your landscape.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">
              <i class="fas fa-leaf"></i>
            </div>
            <h3>Sustainability</h3>
            <p>We implement eco-friendly practices whenever possible, from proper pruning techniques to responsible waste disposal, helping to preserve our environment for future generations.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">
              <i class="fas fa-users"></i>
            </div>
            <h3>Community</h3>
            <p>As a locally owned business, we're committed to serving and improving our community. We hire local talent and participate in community beautification projects.</p>
          </div>
        </div>
      </div>
      
      <div class="team-section animate-on-scroll">
        <h2>Our Team</h2>
        <p>Our experienced team consists of landscaping professionals who are passionate about their work and committed to delivering exceptional service.</p>
        <p>All team members undergo thorough training in proper landscaping techniques, equipment operation, and customer service to ensure they represent our company values in every interaction.</p>
        <p>We're proud to maintain a team of dedicated individuals who take pride in transforming and maintaining beautiful outdoor spaces throughout Windsor and surrounding areas.</p>
      </div>
      
      <div class="cta-panel animate-on-scroll">
        <h2>Ready to experience the Genuine difference?</h2>
        <p>Let us take care of your property maintenance needs so you can focus on enjoying your outdoor space.</p>
        <div class="cta-buttons">
          <a href="contact.php" class="btn btn-primary">Contact Us Today</a>
          <a href="services.php" class="btn btn-secondary">Explore Our Services</a>
        </div>
      </div>
    </div>
  </section>

<?php
// Include footer
include 'includes/footer.php';
?>

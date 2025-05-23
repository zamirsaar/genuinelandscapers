<?php
/**
 * Gallery page for Genuine Landscapers website
 */

// Set page title and description
$page_title = "Project Gallery";
$page_description = "View our portfolio of landscaping projects in Windsor and surrounding areas. See examples of our lawn maintenance, hedge trimming, and property care.";

// Additional scripts for gallery functionality
$additional_scripts = '<script src="js/gallery.js"></script>';

// Include header
include 'includes/header.php';
?>

  <!-- Page Header -->
  <section class="page-header">
    <div class="container">
      <h1>Project Gallery</h1>
      <p>See our work in action</p>
    </div>
  </section>

  <!-- Gallery Content -->
  <section class="gallery-content">
    <div class="container">
      <div class="gallery-intro animate-on-scroll">
        <h2>Our Landscaping Portfolio</h2>
        <p>Browse through our collection of completed projects to see the quality and care we bring to every property. These images showcase our work in lawn maintenance, hedge trimming, garden care, and seasonal cleanup throughout Windsor and surrounding areas.</p>
      </div>
      
      <div class="gallery-filter animate-on-scroll">
        <button class="filter-btn active" data-filter="all">All Projects</button>
        <button class="filter-btn" data-filter="residential">Residential</button>
        <button class="filter-btn" data-filter="commercial">Commercial</button>
        <button class="filter-btn" data-filter="seasonal">Seasonal</button>
      </div>
      
      <div class="gallery-grid animate-on-scroll">
        <div class="gallery-item" data-category="residential">
          <div class="gallery-image">
            <img src="images/project1.jpg" alt="Residential Lawn Maintenance" loading="lazy">
            <div class="gallery-overlay">
              <div class="gallery-info">
                <h3>Residential Lawn Maintenance</h3>
                <p>Windsor, ON</p>
                <button class="gallery-zoom-btn" data-image="images/project1.jpg" data-title="Residential Lawn Maintenance" data-description="Weekly maintenance program for a residential property in Windsor, including mowing, edging, and trimming.">View Larger</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="gallery-item" data-category="residential">
          <div class="gallery-image">
            <img src="images/project2.jpg" alt="Hedge Trimming Project" loading="lazy">
            <div class="gallery-overlay">
              <div class="gallery-info">
                <h3>Hedge Trimming Project</h3>
                <p>LaSalle, ON</p>
                <button class="gallery-zoom-btn" data-image="images/project2.jpg" data-title="Hedge Trimming Project" data-description="Precision hedge trimming and shaping for a residential property in LaSalle.">View Larger</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="gallery-item" data-category="commercial">
          <div class="gallery-image">
            <img src="images/project3.jpg" alt="Commercial Property Maintenance" loading="lazy">
            <div class="gallery-overlay">
              <div class="gallery-info">
                <h3>Commercial Property Maintenance</h3>
                <p>Tecumseh, ON</p>
                <button class="gallery-zoom-btn" data-image="images/project3.jpg" data-title="Commercial Property Maintenance" data-description="Ongoing maintenance program for a commercial property in Tecumseh, including lawn care, hedge trimming, and seasonal cleanup.">View Larger</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="gallery-item" data-category="seasonal">
          <div class="gallery-image">
            <img src="images/project4.jpg" alt="Fall Cleanup Project" loading="lazy">
            <div class="gallery-overlay">
              <div class="gallery-info">
                <h3>Fall Cleanup Project</h3>
                <p>Windsor, ON</p>
                <button class="gallery-zoom-btn" data-image="images/project4.jpg" data-title="Fall Cleanup Project" data-description="Comprehensive fall cleanup service including leaf removal, final mowing, and garden bed preparation for winter.">View Larger</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="gallery-item" data-category="residential">
          <div class="gallery-image">
            <img src="images/project5.jpg" alt="Garden Bed Maintenance" loading="lazy">
            <div class="gallery-overlay">
              <div class="gallery-info">
                <h3>Garden Bed Maintenance</h3>
                <p>LaSalle, ON</p>
                <button class="gallery-zoom-btn" data-image="images/project5.jpg" data-title="Garden Bed Maintenance" data-description="Garden bed weeding, mulching, and maintenance for a residential property in LaSalle.">View Larger</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="gallery-item" data-category="commercial">
          <div class="gallery-image">
            <img src="images/project6.jpg" alt="Commercial Landscape Renovation" loading="lazy">
            <div class="gallery-overlay">
              <div class="gallery-info">
                <h3>Commercial Landscape Renovation</h3>
                <p>Windsor, ON</p>
                <button class="gallery-zoom-btn" data-image="images/project6.jpg" data-title="Commercial Landscape Renovation" data-description="Complete landscape renovation for a commercial property in Windsor, including new plantings and ongoing maintenance.">View Larger</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Gallery Modal -->
      <div class="gallery-modal">
        <div class="modal-content">
          <span class="modal-close">&times;</span>
          <img id="modal-image" src="" alt="">
          <div class="modal-caption">
            <h3 id="modal-title"></h3>
            <p id="modal-description"></p>
          </div>
          <button class="modal-prev"><i class="fas fa-chevron-left"></i></button>
          <button class="modal-next"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
      
      <div class="cta-panel animate-on-scroll">
        <h2>Ready to transform your property?</h2>
        <p>Contact us today to discuss your landscaping needs and receive a customized maintenance plan.</p>
        <div class="cta-buttons">
          <a href="index.php#quote" class="btn btn-primary">Request a Quote</a>
          <a href="contact.php" class="btn btn-secondary">Contact Us</a>
        </div>
      </div>
    </div>
  </section>

<?php
// Include footer
include 'includes/footer.php';
?>

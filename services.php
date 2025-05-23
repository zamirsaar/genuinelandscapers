<?php
/**
 * Service detail page for Genuine Landscapers website
 * Displays detailed information about a specific service
 */

// Include configuration
require_once 'php/config.php';

// Get service slug from URL
$slug = isset($_GET['slug']) ? sanitize_input($_GET['slug']) : '';

// Initialize variables
$service = null;
$related_services = [];
$error = '';

// Get service details
if (!empty($slug)) {
    try {
        $db = get_db_connection();
        
        // Get service details
        $stmt = $db->prepare('SELECT * FROM services WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $service = $stmt->fetch();
        
        if ($service) {
            // Get related services (excluding current service)
            $stmt = $db->prepare('SELECT * FROM services WHERE id != :id ORDER BY RAND() LIMIT 3');
            $stmt->execute(['id' => $service['id']]);
            $related_services = $stmt->fetchAll();
        } else {
            $error = 'Service not found.';
        }
    } catch (PDOException $e) {
        $error = 'Error loading service details.';
        log_activity('error', 'Database error in service detail page: ' . $e->getMessage());
    }
} else {
    $error = 'Invalid service request.';
}

// Set page title and description
$page_title = $service ? $service['name'] : 'Service Not Found';
$page_description = $service ? substr(strip_tags($service['description']), 0, 160) : 'Service details not available.';

// Include header
include 'includes/header.php';
?>

<section class="service-detail-hero">
    <div class="container">
        <?php if ($error): ?>
            <div class="error-message">
                <h1>Service Not Found</h1>
                <p><?php echo $error; ?></p>
                <a href="services.php" class="btn btn-primary">View All Services</a>
            </div>
        <?php else: ?>
            <div class="service-hero-content">
                <h1><?php echo $service['name']; ?></h1>
                <div class="service-icon">
                    <i class="fas <?php echo $service['icon']; ?>"></i>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($service): ?>
    <section class="service-detail-content">
        <div class="container">
            <div class="service-content">
                <div class="service-description">
                    <?php echo $service['description']; ?>
                </div>
                
                <div class="service-cta">
                    <h2>Ready to get started?</h2>
                    <p>Contact us today for a free quote on our <?php echo $service['name']; ?> services.</p>
                    <div class="cta-buttons">
                        <a href="#quote" class="btn btn-primary">Request a Quote</a>
                        <a href="#callback" class="btn btn-secondary">Request a Callback</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php if (!empty($related_services)): ?>
        <section class="related-services">
            <div class="container">
                <h2>Other Services You Might Like</h2>
                <div class="services-grid">
                    <?php foreach ($related_services as $related): ?>
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas <?php echo $related['icon']; ?>"></i>
                            </div>
                            <h3><?php echo $related['name']; ?></h3>
                            <p><?php echo substr(strip_tags($related['description']), 0, 100) . '...'; ?></p>
                            <a href="services.php?slug=<?php echo $related['slug']; ?>" class="read-more-btn">Learn More</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
    
    <section class="quote-section" id="quote">
        <div class="container">
            <div class="quote-container">
                <div class="quote-header">
                    <h2>Request a Free Quote</h2>
                    <p>Fill out the form below to request a personalized quote for our <?php echo $service['name']; ?> services.</p>
                </div>
                
                <form id="quoteForm" class="quote-form" action="php/quote-request.php" method="POST">
                    <?php echo csrf_token_field(); ?>
                    <input type="hidden" name="service_requested" value="<?php echo $service['name']; ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Property Address</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="property_type">Property Type</label>
                            <select id="property_type" name="property_type" required>
                                <option value="">Select Property Type</option>
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                                <option value="multi-unit">Multi-Unit Residential</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="property_size">Property Size</label>
                            <select id="property_size" name="property_size" required>
                                <option value="">Select Property Size</option>
                                <option value="small">Small (Less than 1/4 acre)</option>
                                <option value="medium">Medium (1/4 to 1/2 acre)</option>
                                <option value="large">Large (1/2 to 1 acre)</option>
                                <option value="extra-large">Extra Large (More than 1 acre)</option>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Services Needed</label>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="services[]" value="<?php echo $service['slug']; ?>" checked>
                                    <?php echo $service['name']; ?>
                                </label>
                                
                                <?php foreach ($related_services as $related): ?>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="services[]" value="<?php echo $related['slug']; ?>">
                                        <?php echo $related['name']; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="message">Additional Details</label>
                            <textarea id="message" name="message" rows="4"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Submit Quote Request</button>
                    </div>
                    
                    <div id="quoteFormResponse" class="form-response"></div>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
// Include footer
include 'includes/footer.php';
?>

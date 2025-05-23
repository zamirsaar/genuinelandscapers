<?php
/**
 * Database-driven testimonials component for Genuine Landscapers website
 * Displays customer testimonials from the database
 * 
 * @param int $limit Number of testimonials to display (default: all)
 * @param bool $featured Whether to only show featured testimonials (default: false)
 */

// Include configuration if not already included
if (!function_exists('get_db_connection')) {
    require_once __DIR__ . '/../php/config.php';
}

// Initialize testimonials array
$testimonials = [];

try {
    // Get database connection
    $db = get_db_connection();
    
    // Build query based on parameters
    $query = "SELECT * FROM testimonials WHERE approved = 1";
    $params = [];
    
    // Add featured filter if specified
    if (isset($featured) && $featured) {
        $query .= " AND featured = 1";
    }
    
    // Add order by
    $query .= " ORDER BY featured DESC, rating DESC, created_at DESC";
    
    // Add limit if specified
    if (isset($limit) && $limit > 0) {
        $query .= " LIMIT :limit";
        $params['limit'] = $limit;
    }
    
    // Prepare and execute query
    $stmt = $db->prepare($query);
    
    // Bind parameters if any
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    
    // Fetch all testimonials
    $testimonials = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // Log error
    log_activity('error', 'Failed to fetch testimonials: ' . $e->getMessage());
    
    // Fallback to hardcoded testimonials if database query fails
    $testimonials = [
        [
            'text' => "Amazing service! The team at Genuine Landscapers is professional, punctual, and does exceptional work. My lawn has never looked better. Highly recommended!",
            'author' => "John D.",
            'location' => "Windsor",
            'rating' => 5
        ],
        [
            'text' => "They keep my yard spotless all year round! As a senior, I appreciate their reliability and attention to detail. The team is always courteous and goes above and beyond.",
            'author' => "Sarah K.",
            'location' => "LaSalle",
            'rating' => 5
        ],
        [
            'text' => "Genuine Landscapers transformed our commercial property. Their attention to detail and consistent maintenance has improved our business's curb appeal significantly.",
            'author' => "Michael R.",
            'location' => "Tecumseh",
            'rating' => 4.5
        ]
    ];
    
    // Limit the fallback testimonials if needed
    if (isset($limit) && $limit > 0 && $limit < count($testimonials)) {
        $testimonials = array_slice($testimonials, 0, $limit);
    }
}
?>

<div class="reviews-carousel">
    <?php if (empty($testimonials)): ?>
        <div class="no-content">
            <p>No testimonials available at this time.</p>
        </div>
    <?php else: ?>
        <?php foreach ($testimonials as $index => $testimonial): ?>
            <div class="review-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <div class="review-content">
                    <div class="review-stars">
                        <?php
                        // Display full stars
                        $full_stars = floor($testimonial['rating']);
                        for ($i = 0; $i < $full_stars; $i++) {
                            echo '<i class="fas fa-star"></i>';
                        }
                        
                        // Display half star if needed
                        if ($testimonial['rating'] - $full_stars >= 0.5) {
                            echo '<i class="fas fa-star-half-alt"></i>';
                        }
                        
                        // Display empty stars
                        $empty_stars = 5 - ceil($testimonial['rating']);
                        for ($i = 0; $i < $empty_stars; $i++) {
                            echo '<i class="far fa-star"></i>';
                        }
                        ?>
                    </div>
                    <p class="review-text">"<?php echo $testimonial['text']; ?>"</p>
                    <div class="review-author">
                        <p>- <?php echo $testimonial['author']; ?>, <?php echo $testimonial['location']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($testimonials)): ?>
    <div class="carousel-controls">
        <button class="prev-review" aria-label="Previous review"><i class="fas fa-chevron-left"></i></button>
        <div class="carousel-dots">
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
            <?php endforeach; ?>
        </div>
        <button class="next-review" aria-label="Next review"><i class="fas fa-chevron-right"></i></button>
    </div>
<?php endif; ?>

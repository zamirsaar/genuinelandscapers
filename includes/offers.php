<?php
/**
 * Database-driven offers component for Genuine Landscapers website
 * Displays special offers and promotions from the database
 * 
 * @param int $limit Number of offers to display (default: all)
 * @param bool $active_only Whether to only show active offers (default: true)
 */

// Include configuration if not already included
if (!function_exists('get_db_connection')) {
    require_once __DIR__ . '/../php/config.php';
}

// Initialize offers array
$offers = [];

try {
    // Get database connection
    $db = get_db_connection();
    
    // Build query based on parameters
    $query = "SELECT * FROM offers";
    $params = [];
    $where_clauses = [];
    
    // Add active filter (default or if specified)
    if (!isset($active_only) || $active_only) {
        $where_clauses[] = "active = 1";
    }
    
    // Add expiry date filter
    $where_clauses[] = "(expiry_date IS NULL OR expiry_date >= CURDATE())";
    
    // Combine where clauses
    if (!empty($where_clauses)) {
        $query .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    // Add order by
    $query .= " ORDER BY expiry_date IS NULL, expiry_date ASC";
    
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
    
    // Fetch all offers
    $offers = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // Log error
    log_activity('error', 'Failed to fetch offers: ' . $e->getMessage());
    
    // Fallback to hardcoded offers if database query fails
    $offers = [
        [
            'title' => "Spring Cleanup Special",
            'content' => "Get 20% off your first seasonal cleanup when you book before May 31st. Our comprehensive spring cleanup includes leaf removal, bed preparation, and initial lawn care to get your property ready for the growing season.",
            'expiry_date' => "May 31, 2025",
            'button_text' => "Claim Offer",
            'button_link' => "#quote"
        ],
        [
            'title' => "Referral Rewards",
            'content' => "Refer a friend and receive $50 off your next service when they become a customer. There's no limit to how many friends you can refer, so spread the word about our quality service and save on your landscaping needs.",
            'expiry_date' => "Ongoing",
            'button_text' => "Learn More",
            'button_link' => "promotions.php"
        ]
    ];
    
    // Limit the fallback offers if needed
    if (isset($limit) && $limit > 0 && $limit < count($offers)) {
        $offers = array_slice($offers, 0, $limit);
    }
}
?>

<div class="offers-grid">
    <?php if (empty($offers)): ?>
        <div class="no-content">
            <p>No special offers available at this time. Check back soon for new promotions!</p>
        </div>
    <?php else: ?>
        <?php foreach ($offers as $offer): ?>
            <div class="offer-card">
                <div class="offer-content">
                    <h3><?php echo $offer['title']; ?></h3>
                    <p><?php echo $offer['content']; ?></p>
                    <div class="offer-expiry">
                        <p><strong>Expires:</strong> <?php echo isset($offer['expiry_date']) && $offer['expiry_date'] ? date('F j, Y', strtotime($offer['expiry_date'])) : 'Ongoing'; ?></p>
                    </div>
                    <div class="offer-cta">
                        <a href="<?php echo $offer['button_link']; ?>" class="btn btn-accent"><?php echo $offer['button_text']; ?></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

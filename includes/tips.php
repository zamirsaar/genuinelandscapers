<?php
/**
 * Database-driven tips component for Genuine Landscapers website
 * Displays landscaping tips and hacks from the database
 * 
 * @param int $limit Number of tips to display (default: all)
 * @param bool $featured Whether to only show featured tips (default: false)
 */

// Include configuration if not already included
if (!function_exists('get_db_connection')) {
    require_once __DIR__ . '/../php/config.php';
}

// Initialize tips array
$tips = [];

try {
    // Get database connection
    $db = get_db_connection();
    
    // Build query based on parameters
    $query = "SELECT * FROM tips WHERE published = 1";
    $params = [];
    
    // Add featured filter if specified
    if (isset($featured) && $featured) {
        $query .= " AND featured = 1";
    }
    
    // Add order by
    $query .= " ORDER BY featured DESC, created_at DESC";
    
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
    
    // Fetch all tips
    $tips = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // Log error
    log_activity('error', 'Failed to fetch tips: ' . $e->getMessage());
    
    // Fallback to hardcoded tips if database query fails
    $tips = [
        [
            'title' => "Summer Lawn Care Tips",
            'content' => "Keep your lawn healthy during hot summer months by watering deeply but infrequently, mowing at a higher setting, and avoiding fertilization during extreme heat. Early morning watering reduces evaporation and helps prevent fungal diseases.",
            'icon' => "fa-sun",
            'slug' => "summer-lawn-care"
        ],
        [
            'title' => "When to Prune Your Hedges",
            'content' => "Learn the best times of year to prune different types of hedges for optimal growth. Most deciduous hedges should be pruned in late winter while dormant, while spring-flowering shrubs should be pruned immediately after flowering.",
            'icon' => "fa-cut",
            'slug' => "hedge-pruning"
        ]
    ];
    
    // Limit the fallback tips if needed
    if (isset($limit) && $limit > 0 && $limit < count($tips)) {
        $tips = array_slice($tips, 0, $limit);
    }
}
?>

<div class="tips-grid">
    <?php if (empty($tips)): ?>
        <div class="no-content">
            <p>No tips available at this time. Check back soon for landscaping advice and tips!</p>
        </div>
    <?php else: ?>
        <?php foreach ($tips as $tip): ?>
            <article class="tip-card">
                <div class="tip-icon">
                    <i class="fas <?php echo $tip['icon']; ?>"></i>
                </div>
                <h3><?php echo $tip['title']; ?></h3>
                <p><?php echo substr($tip['content'], 0, 150) . (strlen($tip['content']) > 150 ? '...' : ''); ?></p>
                <a href="tips.php?slug=<?php echo $tip['slug']; ?>" class="read-more-btn">Read More</a>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

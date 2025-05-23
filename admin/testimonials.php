<?php
/**
 * Admin testimonials management page for Genuine Landscapers website
 * Handles CRUD operations for customer testimonials
 */

// Include configuration
require_once '../php/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: ../admin-login.php');
    exit;
}

// Initialize variables
$testimonials = [];
$error = '';
$success = '';
$edit_mode = false;
$testimonial = [
    'id' => '',
    'text' => '',
    'author' => '',
    'location' => '',
    'rating' => 5.0,
    'approved' => 0,
    'featured' => 0
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Security validation failed. Please try again.';
        log_activity('error', 'CSRF validation failed on testimonials form');
    } else {
        // Get form data
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create' || $action === 'update') {
            // Validate form data
            if (empty($_POST['text'])) {
                $error = 'Testimonial text is required';
            } elseif (empty($_POST['author'])) {
                $error = 'Author name is required';
            } elseif (empty($_POST['location'])) {
                $error = 'Location is required';
            } elseif (!isset($_POST['rating']) || $_POST['rating'] < 1 || $_POST['rating'] > 5) {
                $error = 'Rating must be between 1 and 5';
            } else {
                // Sanitize input
                $text = sanitize_input($_POST['text']);
                $author = sanitize_input($_POST['author']);
                $location = sanitize_input($_POST['location']);
                $rating = (float)$_POST['rating'];
                $approved = isset($_POST['approved']) ? 1 : 0;
                $featured = isset($_POST['featured']) ? 1 : 0;
                
                try {
                    $db = get_db_connection();
                    
                    if ($action === 'create') {
                        // Insert new testimonial
                        $stmt = $db->prepare('INSERT INTO testimonials (text, author, location, rating, approved, featured) VALUES (:text, :author, :location, :rating, :approved, :featured)');
                        $stmt->execute([
                            'text' => $text,
                            'author' => $author,
                            'location' => $location,
                            'rating' => $rating,
                            'approved' => $approved,
                            'featured' => $featured
                        ]);
                        
                        $success = 'Testimonial created successfully!';
                        log_activity('admin', 'Created new testimonial from ' . $author);
                        
                        // Reset form
                        $testimonial = [
                            'id' => '',
                            'text' => '',
                            'author' => '',
                            'location' => '',
                            'rating' => 5.0,
                            'approved' => 0,
                            'featured' => 0
                        ];
                    } elseif ($action === 'update') {
                        $id = (int)$_POST['id'];
                        
                        // Update testimonial
                        $stmt = $db->prepare('UPDATE testimonials SET text = :text, author = :author, location = :location, rating = :rating, approved = :approved, featured = :featured WHERE id = :id');
                        $stmt->execute([
                            'text' => $text,
                            'author' => $author,
                            'location' => $location,
                            'rating' => $rating,
                            'approved' => $approved,
                            'featured' => $featured,
                            'id' => $id
                        ]);
                        
                        $success = 'Testimonial updated successfully!';
                        log_activity('admin', 'Updated testimonial from ' . $author);
                        
                        // Reset form and exit edit mode
                        $edit_mode = false;
                        $testimonial = [
                            'id' => '',
                            'text' => '',
                            'author' => '',
                            'location' => '',
                            'rating' => 5.0,
                            'approved' => 0,
                            'featured' => 0
                        ];
                    }
                } catch (PDOException $e) {
                    $error = 'Database error. Please try again later.';
                    log_activity('error', 'Database error in testimonials management: ' . $e->getMessage());
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            try {
                $db = get_db_connection();
                
                // Get testimonial author for logging
                $stmt = $db->prepare('SELECT author FROM testimonials WHERE id = :id LIMIT 1');
                $stmt->execute(['id' => $id]);
                $author = $stmt->fetchColumn();
                
                // Delete testimonial
                $stmt = $db->prepare('DELETE FROM testimonials WHERE id = :id');
                $stmt->execute(['id' => $id]);
                
                $success = 'Testimonial deleted successfully!';
                log_activity('admin', 'Deleted testimonial from ' . $author);
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                log_activity('error', 'Database error in testimonials deletion: ' . $e->getMessage());
            }
        } elseif ($action === 'approve' || $action === 'unapprove') {
            $id = (int)$_POST['id'];
            $approved = ($action === 'approve') ? 1 : 0;
            
            try {
                $db = get_db_connection();
                
                // Get testimonial author for logging
                $stmt = $db->prepare('SELECT author FROM testimonials WHERE id = :id LIMIT 1');
                $stmt->execute(['id' => $id]);
                $author = $stmt->fetchColumn();
                
                // Update approval status
                $stmt = $db->prepare('UPDATE testimonials SET approved = :approved WHERE id = :id');
                $stmt->execute([
                    'approved' => $approved,
                    'id' => $id
                ]);
                
                $success = 'Testimonial ' . ($approved ? 'approved' : 'unapproved') . ' successfully!';
                log_activity('admin', ($approved ? 'Approved' : 'Unapproved') . ' testimonial from ' . $author);
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                log_activity('error', 'Database error in testimonials approval: ' . $e->getMessage());
            }
        }
    }
}

// Handle edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $db = get_db_connection();
        $stmt = $db->prepare('SELECT * FROM testimonials WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $testimonial = $result;
            $edit_mode = true;
        }
    } catch (PDOException $e) {
        $error = 'Database error. Please try again later.';
        log_activity('error', 'Database error in testimonials edit: ' . $e->getMessage());
    }
}

// Get all testimonials
try {
    $db = get_db_connection();
    $stmt = $db->query('SELECT * FROM testimonials ORDER BY approved DESC, featured DESC, created_at DESC');
    $testimonials = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Database error. Please try again later.';
    log_activity('error', 'Database error in testimonials listing: ' . $e->getMessage());
}

// Set page title
$page_title = "Manage Testimonials";

// Include admin header
include 'includes/admin-header.php';
?>

<div class="admin-testimonials">
    <div class="page-header">
        <h1><?php echo $edit_mode ? 'Edit Testimonial' : 'Manage Testimonials'; ?></h1>
        <p><?php echo $edit_mode ? 'Update existing testimonial' : 'Review and manage customer testimonials'; ?></p>
    </div>
    
    <?php if ($error): ?>
        <div class="flash-message flash-error">
            <?php echo $error; ?>
            <button class="close">&times;</button>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="flash-message flash-success">
            <?php echo $success; ?>
            <button class="close">&times;</button>
        </div>
    <?php endif; ?>
    
    <div class="admin-content-wrapper">
        <div class="admin-form-container">
            <form class="admin-form" method="POST" action="testimonials.php">
                <?php echo csrf_token_field(); ?>
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="author">Author Name</label>
                        <input type="text" id="author" name="author" value="<?php echo $testimonial['author']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo $testimonial['location']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="rating">Rating (1-5)</label>
                        <select id="rating" name="rating" required>
                            <option value="5" <?php echo $testimonial['rating'] == 5 ? 'selected' : ''; ?>>5 Stars</option>
                            <option value="4.5" <?php echo $testimonial['rating'] == 4.5 ? 'selected' : ''; ?>>4.5 Stars</option>
                            <option value="4" <?php echo $testimonial['rating'] == 4 ? 'selected' : ''; ?>>4 Stars</option>
                            <option value="3.5" <?php echo $testimonial['rating'] == 3.5 ? 'selected' : ''; ?>>3.5 Stars</option>
                            <option value="3" <?php echo $testimonial['rating'] == 3 ? 'selected' : ''; ?>>3 Stars</option>
                            <option value="2.5" <?php echo $testimonial['rating'] == 2.5 ? 'selected' : ''; ?>>2.5 Stars</option>
                            <option value="2" <?php echo $testimonial['rating'] == 2 ? 'selected' : ''; ?>>2 Stars</option>
                            <option value="1.5" <?php echo $testimonial['rating'] == 1.5 ? 'selected' : ''; ?>>1.5 Stars</option>
                            <option value="1" <?php echo $testimonial['rating'] == 1 ? 'selected' : ''; ?>>1 Star</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="approved" value="1" <?php echo $testimonial['approved'] ? 'checked' : ''; ?>>
                                Approved
                            </label>
                            
                            <label class="checkbox-label">
                                <input type="checkbox" name="featured" value="1" <?php echo $testimonial['featured'] ? 'checked' : ''; ?>>
                                Featured
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="text">Testimonial Text</label>
                        <textarea id="text" name="text" rows="6" required><?php echo $testimonial['text']; ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <?php if ($edit_mode): ?>
                        <a href="testimonials.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update Testimonial' : 'Add Testimonial'; ?></button>
                </div>
            </form>
        </div>
        
        <div class="admin-list-container">
            <h2>All Testimonials</h2>
            
            <?php if (empty($testimonials)): ?>
                <p>No testimonials found. Add your first testimonial using the form.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Author</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $item): ?>
                            <tr>
                                <td>
                                    <?php echo $item['author']; ?>, <?php echo $item['location']; ?>
                                    <?php if ($item['featured']): ?>
                                        <span class="badge badge-featured">Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    // Display stars based on rating
                                    $full_stars = floor($item['rating']);
                                    $half_star = $item['rating'] - $full_stars >= 0.5;
                                    
                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '<i class="fas fa-star"></i>';
                                    }
                                    
                                    if ($half_star) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    }
                                    
                                    $empty_stars = 5 - ceil($item['rating']);
                                    for ($i = 0; $i < $empty_stars; $i++) {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($item['approved']): ?>
                                        <span class="status-badge status-approved">Approved</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($item['created_at'])); ?></td>
                                <td class="actions-cell">
                                    <?php if (!$item['approved']): ?>
                                        <form method="POST" action="testimonials.php" class="inline-form">
                                            <?php echo csrf_token_field(); ?>
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-primary">Approve</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="testimonials.php" class="inline-form">
                                            <?php echo csrf_token_field(); ?>
                                            <input type="hidden" name="action" value="unapprove">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline">Unapprove</button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <a href="testimonials.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    
                                    <form method="POST" action="testimonials.php" class="inline-form">
                                        <?php echo csrf_token_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include admin footer
include 'includes/admin-footer.php';
?>

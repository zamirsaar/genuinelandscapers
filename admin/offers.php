<?php
/**
 * Admin offers management page for Genuine Landscapers website
 * Handles CRUD operations for special offers and promotions
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
$offers = [];
$error = '';
$success = '';
$edit_mode = false;
$offer = [
    'id' => '',
    'title' => '',
    'content' => '',
    'expiry_date' => '',
    'button_text' => '',
    'button_link' => '',
    'active' => 1
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Security validation failed. Please try again.';
        log_activity('error', 'CSRF validation failed on offers form');
    } else {
        // Get form data
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create' || $action === 'update') {
            // Validate form data
            if (empty($_POST['title'])) {
                $error = 'Title is required';
            } elseif (empty($_POST['content'])) {
                $error = 'Content is required';
            } elseif (empty($_POST['button_text'])) {
                $error = 'Button text is required';
            } elseif (empty($_POST['button_link'])) {
                $error = 'Button link is required';
            } else {
                // Sanitize input
                $title = sanitize_input($_POST['title']);
                $content = $_POST['content']; // Don't sanitize content to preserve HTML
                $expiry_date = !empty($_POST['expiry_date']) ? sanitize_input($_POST['expiry_date']) : null;
                $button_text = sanitize_input($_POST['button_text']);
                $button_link = sanitize_input($_POST['button_link']);
                $active = isset($_POST['active']) ? 1 : 0;
                
                try {
                    $db = get_db_connection();
                    
                    if ($action === 'create') {
                        // Insert new offer
                        $stmt = $db->prepare('INSERT INTO offers (title, content, expiry_date, button_text, button_link, active, created_by) VALUES (:title, :content, :expiry_date, :button_text, :button_link, :active, :created_by)');
                        $stmt->execute([
                            'title' => $title,
                            'content' => $content,
                            'expiry_date' => $expiry_date,
                            'button_text' => $button_text,
                            'button_link' => $button_link,
                            'active' => $active,
                            'created_by' => $_SESSION['user_id']
                        ]);
                        
                        $success = 'Offer created successfully!';
                        log_activity('admin', 'Created new offer: ' . $title);
                        
                        // Reset form
                        $offer = [
                            'id' => '',
                            'title' => '',
                            'content' => '',
                            'expiry_date' => '',
                            'button_text' => '',
                            'button_link' => '',
                            'active' => 1
                        ];
                    } elseif ($action === 'update') {
                        $id = (int)$_POST['id'];
                        
                        // Update offer
                        $stmt = $db->prepare('UPDATE offers SET title = :title, content = :content, expiry_date = :expiry_date, button_text = :button_text, button_link = :button_link, active = :active WHERE id = :id');
                        $stmt->execute([
                            'title' => $title,
                            'content' => $content,
                            'expiry_date' => $expiry_date,
                            'button_text' => $button_text,
                            'button_link' => $button_link,
                            'active' => $active,
                            'id' => $id
                        ]);
                        
                        $success = 'Offer updated successfully!';
                        log_activity('admin', 'Updated offer: ' . $title);
                        
                        // Reset form and exit edit mode
                        $edit_mode = false;
                        $offer = [
                            'id' => '',
                            'title' => '',
                            'content' => '',
                            'expiry_date' => '',
                            'button_text' => '',
                            'button_link' => '',
                            'active' => 1
                        ];
                    }
                } catch (PDOException $e) {
                    $error = 'Database error. Please try again later.';
                    log_activity('error', 'Database error in offers management: ' . $e->getMessage());
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            try {
                $db = get_db_connection();
                
                // Get offer title for logging
                $stmt = $db->prepare('SELECT title FROM offers WHERE id = :id LIMIT 1');
                $stmt->execute(['id' => $id]);
                $offer_title = $stmt->fetchColumn();
                
                // Delete offer
                $stmt = $db->prepare('DELETE FROM offers WHERE id = :id');
                $stmt->execute(['id' => $id]);
                
                $success = 'Offer deleted successfully!';
                log_activity('admin', 'Deleted offer: ' . $offer_title);
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                log_activity('error', 'Database error in offers deletion: ' . $e->getMessage());
            }
        }
    }
}

// Handle edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $db = get_db_connection();
        $stmt = $db->prepare('SELECT * FROM offers WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $offer = $result;
            $edit_mode = true;
        }
    } catch (PDOException $e) {
        $error = 'Database error. Please try again later.';
        log_activity('error', 'Database error in offers edit: ' . $e->getMessage());
    }
}

// Get all offers
try {
    $db = get_db_connection();
    $stmt = $db->query('SELECT o.*, u.username as author FROM offers o LEFT JOIN users u ON o.created_by = u.id ORDER BY o.active DESC, o.expiry_date IS NULL, o.expiry_date ASC');
    $offers = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Database error. Please try again later.';
    log_activity('error', 'Database error in offers listing: ' . $e->getMessage());
}

// Set page title
$page_title = "Manage Offers";

// Include admin header
include 'includes/admin-header.php';
?>

<div class="admin-offers">
    <div class="page-header">
        <h1><?php echo $edit_mode ? 'Edit Offer' : 'Manage Special Offers'; ?></h1>
        <p><?php echo $edit_mode ? 'Update existing offer details' : 'Create and manage special promotions and offers'; ?></p>
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
            <form class="admin-form" method="POST" action="offers.php">
                <?php echo csrf_token_field(); ?>
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo $offer['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" value="<?php echo $offer['title']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="date" id="expiry_date" name="expiry_date" value="<?php echo $offer['expiry_date']; ?>">
                        <small>Leave blank for ongoing offers</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="button_text">Button Text</label>
                        <input type="text" id="button_text" name="button_text" value="<?php echo $offer['button_text']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="button_link">Button Link</label>
                        <input type="text" id="button_link" name="button_link" value="<?php echo $offer['button_link']; ?>" required>
                        <small>Example: "#quote" or "services.php"</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="active" value="1" <?php echo $offer['active'] ? 'checked' : ''; ?>>
                                Active
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" rows="6" required><?php echo $offer['content']; ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <?php if ($edit_mode): ?>
                        <a href="offers.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-secondary preview-btn">Preview</button>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update Offer' : 'Create Offer'; ?></button>
                </div>
            </form>
        </div>
        
        <div class="admin-list-container">
            <h2>All Offers</h2>
            
            <?php if (empty($offers)): ?>
                <p>No offers found. Create your first offer using the form.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Expiry</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $item): ?>
                            <tr>
                                <td><?php echo $item['title']; ?></td>
                                <td>
                                    <?php if ($item['active']): ?>
                                        <span class="status-badge status-active">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($item['expiry_date']) {
                                        echo date('M j, Y', strtotime($item['expiry_date']));
                                        
                                        // Check if expired
                                        if (strtotime($item['expiry_date']) < time()) {
                                            echo ' <span class="status-badge status-expired">Expired</span>';
                                        }
                                    } else {
                                        echo 'Ongoing';
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($item['created_at'])); ?></td>
                                <td class="actions-cell">
                                    <a href="offers.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    <form method="POST" action="offers.php" class="inline-form">
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

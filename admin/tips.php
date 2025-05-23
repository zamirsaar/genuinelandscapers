<?php
/**
 * Admin tips management page for Genuine Landscapers website
 * Handles CRUD operations for landscaping tips and blog posts
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
$tips = [];
$error = '';
$success = '';
$edit_mode = false;
$tip = [
    'id' => '',
    'title' => '',
    'content' => '',
    'icon' => '',
    'slug' => '',
    'featured' => 0,
    'published' => 1
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Security validation failed. Please try again.';
        log_activity('error', 'CSRF validation failed on tips form');
    } else {
        // Get form data
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create' || $action === 'update') {
            // Validate form data
            if (empty($_POST['title'])) {
                $error = 'Title is required';
            } elseif (empty($_POST['content'])) {
                $error = 'Content is required';
            } elseif (empty($_POST['icon'])) {
                $error = 'Icon is required';
            } elseif (empty($_POST['slug'])) {
                $error = 'Slug is required';
            } else {
                // Sanitize input
                $title = sanitize_input($_POST['title']);
                $content = $_POST['content']; // Don't sanitize content to preserve HTML
                $icon = sanitize_input($_POST['icon']);
                $slug = sanitize_input($_POST['slug']);
                $featured = isset($_POST['featured']) ? 1 : 0;
                $published = isset($_POST['published']) ? 1 : 0;
                
                try {
                    $db = get_db_connection();
                    
                    if ($action === 'create') {
                        // Check if slug already exists
                        $stmt = $db->prepare('SELECT id FROM tips WHERE slug = :slug LIMIT 1');
                        $stmt->execute(['slug' => $slug]);
                        $exists = $stmt->fetch();
                        
                        if ($exists) {
                            $error = 'A tip with this slug already exists. Please choose a different slug.';
                        } else {
                            // Insert new tip
                            $stmt = $db->prepare('INSERT INTO tips (title, content, icon, slug, featured, published, created_by) VALUES (:title, :content, :icon, :slug, :featured, :published, :created_by)');
                            $stmt->execute([
                                'title' => $title,
                                'content' => $content,
                                'icon' => $icon,
                                'slug' => $slug,
                                'featured' => $featured,
                                'published' => $published,
                                'created_by' => $_SESSION['user_id']
                            ]);
                            
                            $success = 'Tip created successfully!';
                            log_activity('admin', 'Created new tip: ' . $title);
                            
                            // Reset form
                            $tip = [
                                'id' => '',
                                'title' => '',
                                'content' => '',
                                'icon' => '',
                                'slug' => '',
                                'featured' => 0,
                                'published' => 1
                            ];
                        }
                    } elseif ($action === 'update') {
                        $id = (int)$_POST['id'];
                        
                        // Check if slug already exists for other tips
                        $stmt = $db->prepare('SELECT id FROM tips WHERE slug = :slug AND id != :id LIMIT 1');
                        $stmt->execute([
                            'slug' => $slug,
                            'id' => $id
                        ]);
                        $exists = $stmt->fetch();
                        
                        if ($exists) {
                            $error = 'Another tip with this slug already exists. Please choose a different slug.';
                        } else {
                            // Update tip
                            $stmt = $db->prepare('UPDATE tips SET title = :title, content = :content, icon = :icon, slug = :slug, featured = :featured, published = :published WHERE id = :id');
                            $stmt->execute([
                                'title' => $title,
                                'content' => $content,
                                'icon' => $icon,
                                'slug' => $slug,
                                'featured' => $featured,
                                'published' => $published,
                                'id' => $id
                            ]);
                            
                            $success = 'Tip updated successfully!';
                            log_activity('admin', 'Updated tip: ' . $title);
                            
                            // Reset form and exit edit mode
                            $edit_mode = false;
                            $tip = [
                                'id' => '',
                                'title' => '',
                                'content' => '',
                                'icon' => '',
                                'slug' => '',
                                'featured' => 0,
                                'published' => 1
                            ];
                        }
                    }
                } catch (PDOException $e) {
                    $error = 'Database error. Please try again later.';
                    log_activity('error', 'Database error in tips management: ' . $e->getMessage());
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            try {
                $db = get_db_connection();
                
                // Get tip title for logging
                $stmt = $db->prepare('SELECT title FROM tips WHERE id = :id LIMIT 1');
                $stmt->execute(['id' => $id]);
                $tip_title = $stmt->fetchColumn();
                
                // Delete tip
                $stmt = $db->prepare('DELETE FROM tips WHERE id = :id');
                $stmt->execute(['id' => $id]);
                
                $success = 'Tip deleted successfully!';
                log_activity('admin', 'Deleted tip: ' . $tip_title);
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                log_activity('error', 'Database error in tips deletion: ' . $e->getMessage());
            }
        }
    }
}

// Handle edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $db = get_db_connection();
        $stmt = $db->prepare('SELECT * FROM tips WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $tip = $result;
            $edit_mode = true;
        }
    } catch (PDOException $e) {
        $error = 'Database error. Please try again later.';
        log_activity('error', 'Database error in tips edit: ' . $e->getMessage());
    }
}

// Get all tips
try {
    $db = get_db_connection();
    $stmt = $db->query('SELECT t.*, u.username as author FROM tips t LEFT JOIN users u ON t.created_by = u.id ORDER BY t.featured DESC, t.created_at DESC');
    $tips = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Database error. Please try again later.';
    log_activity('error', 'Database error in tips listing: ' . $e->getMessage());
}

// Set page title
$page_title = "Manage Tips & Blog";

// Include admin header
include 'includes/admin-header.php';
?>

<div class="admin-tips">
    <div class="page-header">
        <h1><?php echo $edit_mode ? 'Edit Tip' : 'Manage Tips & Blog'; ?></h1>
        <p><?php echo $edit_mode ? 'Update existing tip content' : 'Create and manage landscaping tips and blog posts'; ?></p>
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
            <form class="admin-form" method="POST" action="tips.php">
                <?php echo csrf_token_field(); ?>
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo $tip['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" value="<?php echo $tip['title']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" value="<?php echo $tip['slug']; ?>" required>
                        <small>Used in URLs. Example: "summer-lawn-care"</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <input type="text" id="icon" name="icon" value="<?php echo $tip['icon']; ?>" required>
                        <small>Font Awesome icon class. Example: "fa-sun"</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="featured" value="1" <?php echo $tip['featured'] ? 'checked' : ''; ?>>
                                Featured
                            </label>
                            
                            <label class="checkbox-label">
                                <input type="checkbox" name="published" value="1" <?php echo $tip['published'] ? 'checked' : ''; ?>>
                                Published
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" rows="10" required><?php echo $tip['content']; ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <?php if ($edit_mode): ?>
                        <a href="tips.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-secondary preview-btn">Preview</button>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update Tip' : 'Create Tip'; ?></button>
                </div>
            </form>
        </div>
        
        <div class="admin-list-container">
            <h2>All Tips</h2>
            
            <?php if (empty($tips)): ?>
                <p>No tips found. Create your first tip using the form.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Author</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tips as $item): ?>
                            <tr>
                                <td>
                                    <?php echo $item['title']; ?>
                                    <?php if ($item['featured']): ?>
                                        <span class="badge badge-featured">Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['published']): ?>
                                        <span class="status-badge status-published">Published</span>
                                    <?php else: ?>
                                        <span class="status-badge status-draft">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $item['author'] ?? 'Unknown'; ?></td>
                                <td><?php echo date('M j, Y', strtotime($item['created_at'])); ?></td>
                                <td class="actions-cell">
                                    <a href="../tips.php?slug=<?php echo $item['slug']; ?>" class="btn btn-sm btn-outline" target="_blank">View</a>
                                    <a href="tips.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    <form method="POST" action="tips.php" class="inline-form">
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

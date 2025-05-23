<?php
/**
 * Admin newsletter subscribers management page for Genuine Landscapers website
 * Handles management of newsletter subscribers
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
$subscribers = [];
$error = '';
$success = '';
$edit_mode = false;
$subscriber = [
    'id' => '',
    'email' => '',
    'name' => '',
    'active' => 1,
    'confirmed' => 0
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Security validation failed. Please try again.';
        log_activity('error', 'CSRF validation failed on subscribers form');
    } else {
        // Get form data
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create' || $action === 'update') {
            // Validate form data
            if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Valid email is required';
            } else {
                // Sanitize input
                $email = sanitize_input($_POST['email']);
                $name = sanitize_input($_POST['name']);
                $active = isset($_POST['active']) ? 1 : 0;
                $confirmed = isset($_POST['confirmed']) ? 1 : 0;
                
                try {
                    $db = get_db_connection();
                    
                    if ($action === 'create') {
                        // Check if email already exists
                        $stmt = $db->prepare('SELECT id FROM newsletter_subscribers WHERE email = :email LIMIT 1');
                        $stmt->execute(['email' => $email]);
                        $exists = $stmt->fetch();
                        
                        if ($exists) {
                            $error = 'A subscriber with this email already exists.';
                        } else {
                            // Generate confirmation token
                            $confirmation_token = bin2hex(random_bytes(16));
                            
                            // Insert new subscriber
                            $stmt = $db->prepare('INSERT INTO newsletter_subscribers (email, name, active, confirmation_token, confirmed) VALUES (:email, :name, :active, :confirmation_token, :confirmed)');
                            $stmt->execute([
                                'email' => $email,
                                'name' => $name,
                                'active' => $active,
                                'confirmation_token' => $confirmation_token,
                                'confirmed' => $confirmed
                            ]);
                            
                            $success = 'Subscriber added successfully!';
                            log_activity('admin', 'Added new subscriber: ' . $email);
                            
                            // Reset form
                            $subscriber = [
                                'id' => '',
                                'email' => '',
                                'name' => '',
                                'active' => 1,
                                'confirmed' => 0
                            ];
                        }
                    } elseif ($action === 'update') {
                        $id = (int)$_POST['id'];
                        
                        // Check if email already exists for other subscribers
                        $stmt = $db->prepare('SELECT id FROM newsletter_subscribers WHERE email = :email AND id != :id LIMIT 1');
                        $stmt->execute([
                            'email' => $email,
                            'id' => $id
                        ]);
                        $exists = $stmt->fetch();
                        
                        if ($exists) {
                            $error = 'Another subscriber with this email already exists.';
                        } else {
                            // Update subscriber
                            $stmt = $db->prepare('UPDATE newsletter_subscribers SET email = :email, name = :name, active = :active, confirmed = :confirmed WHERE id = :id');
                            $stmt->execute([
                                'email' => $email,
                                'name' => $name,
                                'active' => $active,
                                'confirmed' => $confirmed,
                                'id' => $id
                            ]);
                            
                            $success = 'Subscriber updated successfully!';
                            log_activity('admin', 'Updated subscriber: ' . $email);
                            
                            // Reset form and exit edit mode
                            $edit_mode = false;
                            $subscriber = [
                                'id' => '',
                                'email' => '',
                                'name' => '',
                                'active' => 1,
                                'confirmed' => 0
                            ];
                        }
                    }
                } catch (PDOException $e) {
                    $error = 'Database error. Please try again later.';
                    log_activity('error', 'Database error in subscribers management: ' . $e->getMessage());
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            try {
                $db = get_db_connection();
                
                // Get subscriber email for logging
                $stmt = $db->prepare('SELECT email FROM newsletter_subscribers WHERE id = :id LIMIT 1');
                $stmt->execute(['id' => $id]);
                $email = $stmt->fetchColumn();
                
                // Delete subscriber
                $stmt = $db->prepare('DELETE FROM newsletter_subscribers WHERE id = :id');
                $stmt->execute(['id' => $id]);
                
                $success = 'Subscriber deleted successfully!';
                log_activity('admin', 'Deleted subscriber: ' . $email);
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                log_activity('error', 'Database error in subscribers deletion: ' . $e->getMessage());
            }
        } elseif ($action === 'export') {
            try {
                $db = get_db_connection();
                
                // Get all active and confirmed subscribers
                $stmt = $db->query('SELECT email, name FROM newsletter_subscribers WHERE active = 1 AND confirmed = 1 ORDER BY email');
                $export_subscribers = $stmt->fetchAll();
                
                if (count($export_subscribers) > 0) {
                    // Create CSV content
                    $csv_content = "Email,Name\n";
                    foreach ($export_subscribers as $sub) {
                        $csv_content .= '"' . $sub['email'] . '","' . $sub['name'] . "\"\n";
                    }
                    
                    // Set headers for download
                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    
                    // Output CSV content
                    echo $csv_content;
                    exit;
                } else {
                    $error = 'No active and confirmed subscribers to export.';
                }
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                log_activity('error', 'Database error in subscribers export: ' . $e->getMessage());
            }
        }
    }
}

// Handle edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $db = get_db_connection();
        $stmt = $db->prepare('SELECT * FROM newsletter_subscribers WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $subscriber = $result;
            $edit_mode = true;
        }
    } catch (PDOException $e) {
        $error = 'Database error. Please try again later.';
        log_activity('error', 'Database error in subscribers edit: ' . $e->getMessage());
    }
}

// Get all subscribers
try {
    $db = get_db_connection();
    $stmt = $db->query('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC');
    $subscribers = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Database error. Please try again later.';
    log_activity('error', 'Database error in subscribers listing: ' . $e->getMessage());
}

// Set page title
$page_title = "Newsletter Subscribers";

// Include admin header
include 'includes/admin-header.php';
?>

<div class="admin-subscribers">
    <div class="page-header">
        <h1><?php echo $edit_mode ? 'Edit Subscriber' : 'Newsletter Subscribers'; ?></h1>
        <p><?php echo $edit_mode ? 'Update existing subscriber details' : 'Manage newsletter subscribers'; ?></p>
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
            <form class="admin-form" method="POST" action="subscribers.php">
                <?php echo csrf_token_field(); ?>
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo $subscriber['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $subscriber['email']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $subscriber['name']; ?>">
                        <small>Optional</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="active" value="1" <?php echo $subscriber['active'] ? 'checked' : ''; ?>>
                                Active
                            </label>
                            
                            <label class="checkbox-label">
                                <input type="checkbox" name="confirmed" value="1" <?php echo $subscriber['confirmed'] ? 'checked' : ''; ?>>
                                Confirmed
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <?php if ($edit_mode): ?>
                        <a href="subscribers.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update Subscriber' : 'Add Subscriber'; ?></button>
                </div>
            </form>
            
            <div class="export-section">
                <h3>Export Subscribers</h3>
                <p>Export all active and confirmed subscribers to a CSV file.</p>
                <form method="POST" action="subscribers.php">
                    <?php echo csrf_token_field(); ?>
                    <input type="hidden" name="action" value="export">
                    <button type="submit" class="btn btn-secondary">Export to CSV</button>
                </form>
            </div>
        </div>
        
        <div class="admin-list-container">
            <h2>All Subscribers</h2>
            
            <?php if (empty($subscribers)): ?>
                <p>No subscribers found. Add your first subscriber using the form.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Subscribed On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscribers as $item): ?>
                            <tr>
                                <td><?php echo $item['email']; ?></td>
                                <td><?php echo $item['name'] ?: '-'; ?></td>
                                <td>
                                    <?php if (!$item['active']): ?>
                                        <span class="status-badge status-inactive">Inactive</span>
                                    <?php elseif (!$item['confirmed']): ?>
                                        <span class="status-badge status-pending">Unconfirmed</span>
                                    <?php else: ?>
                                        <span class="status-badge status-active">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($item['created_at'])); ?></td>
                                <td class="actions-cell">
                                    <a href="subscribers.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    <form method="POST" action="subscribers.php" class="inline-form">
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

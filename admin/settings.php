<?php
/**
 * Admin settings management page for Genuine Landscapers website
 * Handles website configuration settings
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
$settings = [];
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Security validation failed. Please try again.';
        log_activity('error', 'CSRF validation failed on settings form');
    } else {
        // Get form data
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_general') {
            // Update general settings
            try {
                $db = get_db_connection();
                
                // Update site name
                update_setting('site_name', sanitize_input($_POST['site_name']), 'general');
                
                // Update site description
                update_setting('site_description', sanitize_input($_POST['site_description']), 'general');
                
                $success = 'General settings updated successfully!';
                log_activity('admin', 'Updated general settings');
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                log_activity('error', 'Database error in settings update: ' . $e->getMessage());
            }
        } elseif ($action === 'update_contact') {
            // Update contact settings
            try {
                $db = get_db_connection();
                
                // Update contact email
                update_setting('contact_email', sanitize_input($_POST['contact_email']), 'contact');
                
                // Update contact phone
                update_setting('contact_phone', sanitize_input($_POST['contact_phone']), 'contact');
                
                // Update contact address
                update_setting('contact_address', sanitize_input($_POST['contact_address']), 'contact');
                
                // Update business hours
                update_setting('business_hours', $_POST['business_hours'], 'contact');
                
                $success = 'Contact settings updated successfully!';
                log_activity('admin', 'Updated contact settings');
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                log_activity('error', 'Database error in settings update: ' . $e->getMessage());
            }
        } elseif ($action === 'update_social') {
            // Update social media settings
            try {
                $db = get_db_connection();
                
                // Update Facebook URL
                update_setting('facebook_url', sanitize_input($_POST['facebook_url']), 'social');
                
                // Update Instagram URL
                update_setting('instagram_url', sanitize_input($_POST['instagram_url']), 'social');
                
                // Update LinkedIn URL
                update_setting('linkedin_url', sanitize_input($_POST['linkedin_url']), 'social');
                
                $success = 'Social media settings updated successfully!';
                log_activity('admin', 'Updated social media settings');
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                log_activity('error', 'Database error in settings update: ' . $e->getMessage());
            }
        }
    }
}

// Get all settings
try {
    $db = get_db_connection();
    $stmt = $db->query('SELECT * FROM settings ORDER BY setting_group, setting_key');
    $settings_data = $stmt->fetchAll();
    
    // Organize settings by group
    $settings = [];
    foreach ($settings_data as $setting) {
        $settings[$setting['setting_group']][$setting['setting_key']] = $setting['setting_value'];
    }
} catch (PDOException $e) {
    $error = 'Database error. Please try again later.';
    log_activity('error', 'Database error in settings retrieval: ' . $e->getMessage());
}

// Set page title
$page_title = "Website Settings";

// Include admin header
include 'includes/admin-header.php';
?>

<div class="admin-settings">
    <div class="page-header">
        <h1>Website Settings</h1>
        <p>Configure general website settings and information</p>
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
    
    <div class="settings-tabs">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="general">General</button>
            <button class="tab-btn" data-tab="contact">Contact Information</button>
            <button class="tab-btn" data-tab="social">Social Media</button>
        </div>
        
        <div class="tabs-content">
            <!-- General Settings -->
            <div class="tab-pane active" id="general-tab">
                <form class="admin-form" method="POST" action="settings.php">
                    <?php echo csrf_token_field(); ?>
                    <input type="hidden" name="action" value="update_general">
                    
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" id="site_name" name="site_name" value="<?php echo $settings['general']['site_name'] ?? 'Genuine Landscapers'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_description">Site Description</label>
                        <textarea id="site_description" name="site_description" rows="3" required><?php echo $settings['general']['site_description'] ?? 'Professional landscaping services in Windsor and surrounding areas'; ?></textarea>
                        <small>Used in meta tags and search results</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save General Settings</button>
                    </div>
                </form>
            </div>
            
            <!-- Contact Settings -->
            <div class="tab-pane" id="contact-tab">
                <form class="admin-form" method="POST" action="settings.php">
                    <?php echo csrf_token_field(); ?>
                    <input type="hidden" name="action" value="update_contact">
                    
                    <div class="form-group">
                        <label for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" value="<?php echo $settings['contact']['contact_email'] ?? 'info@genuinelandscapers.ca'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_phone">Contact Phone</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="<?php echo $settings['contact']['contact_phone'] ?? '519-300-6434'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_address">Address</label>
                        <input type="text" id="contact_address" name="contact_address" value="<?php echo $settings['contact']['contact_address'] ?? '1609 Moy Ave, Windsor, ON N8X 4C8'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="business_hours">Business Hours</label>
                        <textarea id="business_hours" name="business_hours" rows="5" required><?php echo $settings['contact']['business_hours'] ?? "Monday - Friday: 8:00 AM - 6:00 PM\nSaturday: 9:00 AM - 4:00 PM\nSunday: Closed"; ?></textarea>
                        <small>Enter each line of hours separately</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Contact Settings</button>
                    </div>
                </form>
            </div>
            
            <!-- Social Media Settings -->
            <div class="tab-pane" id="social-tab">
                <form class="admin-form" method="POST" action="settings.php">
                    <?php echo csrf_token_field(); ?>
                    <input type="hidden" name="action" value="update_social">
                    
                    <div class="form-group">
                        <label for="facebook_url">Facebook URL</label>
                        <input type="url" id="facebook_url" name="facebook_url" value="<?php echo $settings['social']['facebook_url'] ?? 'https://www.facebook.com/genuinelandscapers'; ?>">
                        <small>Leave blank to hide Facebook link</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="instagram_url">Instagram URL</label>
                        <input type="url" id="instagram_url" name="instagram_url" value="<?php echo $settings['social']['instagram_url'] ?? 'https://www.instagram.com/genuinelandscapers'; ?>">
                        <small>Leave blank to hide Instagram link</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="linkedin_url">LinkedIn URL</label>
                        <input type="url" id="linkedin_url" name="linkedin_url" value="<?php echo $settings['social']['linkedin_url'] ?? 'https://www.linkedin.com/company/genuine-landscapers'; ?>">
                        <small>Leave blank to hide LinkedIn link</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Social Media Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and panes
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));
                
                // Add active class to clicked button and corresponding pane
                button.classList.add('active');
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId + '-tab').classList.add('active');
            });
        });
    });
</script>

<?php
// Include admin footer
include 'includes/admin-footer.php';
?>

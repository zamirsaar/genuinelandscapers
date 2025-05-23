<?php
/**
 * Admin users management page for Genuine Landscapers website
 * Handles CRUD operations for admin users
 */

// Include configuration
require_once '../php/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: ../admin-login.php');
    exit;
}

// Check if user has admin role
if ($_SESSION['user_role'] !== 'admin') {
    // Redirect to dashboard with error
    $_SESSION['flash_error'] = 'You do not have permission to access the user management page.';
    header('Location: dashboard.php');
    exit;
}

// Initialize variables
$users = [];
$error = '';
$success = '';
$edit_mode = false;
$user = [
    'id' => '',
    'username' => '',
    'email' => '',
    'first_name' => '',
    'last_name' => '',
    'role' => 'viewer',
    'password' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Security validation failed. Please try again.';
        log_activity('error', 'CSRF validation failed on users form');
    } else {
        // Get form data
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create' || $action === 'update') {
            // Validate form data
            if (empty($_POST['username'])) {
                $error = 'Username is required';
            } elseif (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Valid email is required';
            } elseif (empty($_POST['first_name'])) {
                $error = 'First name is required';
            } elseif (empty($_POST['last_name'])) {
                $error = 'Last name is required';
            } elseif (empty($_POST['role'])) {
                $error = 'Role is required';
            } elseif ($action === 'create' && empty($_POST['password'])) {
                $error = 'Password is required for new users';
            } elseif ($action === 'update' && !empty($_POST['password']) && strlen($_POST['password']) < 8) {
                $error = 'Password must be at least 8 characters long';
            } else {
                // Sanitize input
                $username = sanitize_input($_POST['username']);
                $email = sanitize_input($_POST['email']);
                $first_name = sanitize_input($_POST['first_name']);
                $last_name = sanitize_input($_POST['last_name']);
                $role = sanitize_input($_POST['role']);
                $password = $_POST['password']; // Don't sanitize password
                
                try {
                    $db = get_db_connection();
                    
                    if ($action === 'create') {
                        // Check if username or email already exists
                        $stmt = $db->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
                        $stmt->execute([
                            'username' => $username,
                            'email' => $email
                        ]);
                        $exists = $stmt->fetch();
                        
                        if ($exists) {
                            $error = 'A user with this username or email already exists.';
                        } else {
                            // Hash password
                            $password_hash = password_hash($password, PASSWORD_DEFAULT);
                            
                            // Insert new user
                            $stmt = $db->prepare('INSERT INTO users (username, password, email, first_name, last_name, role) VALUES (:username, :password, :email, :first_name, :last_name, :role)');
                            $stmt->execute([
                                'username' => $username,
                                'password' => $password_hash,
                                'email' => $email,
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'role' => $role
                            ]);
                            
                            $success = 'User created successfully!';
                            log_activity('admin', 'Created new user: ' . $username);
                            
                            // Reset form
                            $user = [
                                'id' => '',
                                'username' => '',
                                'email' => '',
                                'first_name' => '',
                                'last_name' => '',
                                'role' => 'viewer',
                                'password' => ''
                            ];
                        }
                    } elseif ($action === 'update') {
                        $id = (int)$_POST['id'];
                        
                        // Check if username or email already exists for other users
                        $stmt = $db->prepare('SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id LIMIT 1');
                        $stmt->execute([
                            'username' => $username,
                            'email' => $email,
                            'id' => $id
                        ]);
                        $exists = $stmt->fetch();
                        
                        if ($exists) {
                            $error = 'Another user with this username or email already exists.';
                        } else {
                            // Update user
                            if (!empty($password)) {
                                // Update with new password
                                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                                $stmt = $db->prepare('UPDATE users SET username = :username, password = :password, email = :email, first_name = :first_name, last_name = :last_name, role = :role WHERE id = :id');
                                $stmt->execute([
                                    'username' => $username,
                                    'password' => $password_hash,
                                    'email' => $email,
                                    'first_name' => $first_name,
                                    'last_name' => $last_name,
                                    'role' => $role,
                                    'id' => $id
                                ]);
                            } else {
                                // Update without changing password
                                $stmt = $db->prepare('UPDATE users SET username = :username, email = :email, first_name = :first_name, last_name = :last_name, role = :role WHERE id = :id');
                                $stmt->execute([
                                    'username' => $username,
                                    'email' => $email,
                                    'first_name' => $first_name,
                                    'last_name' => $last_name,
                                    'role' => $role,
                                    'id' => $id
                                ]);
                            }
                            
                            $success = 'User updated successfully!';
                            log_activity('admin', 'Updated user: ' . $username);
                            
                            // Reset form and exit edit mode
                            $edit_mode = false;
                            $user = [
                                'id' => '',
                                'username' => '',
                                'email' => '',
                                'first_name' => '',
                                'last_name' => '',
                                'role' => 'viewer',
                                'password' => ''
                            ];
                        }
                    }
                } catch (PDOException $e) {
                    $error = 'Database error. Please try again later.';
                    log_activity('error', 'Database error in users management: ' . $e->getMessage());
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            // Prevent self-deletion
            if ($id === (int)$_SESSION['user_id']) {
                $error = 'You cannot delete your own account.';
            } else {
                try {
                    $db = get_db_connection();
                    
                    // Get user username for logging
                    $stmt = $db->prepare('SELECT username FROM users WHERE id = :id LIMIT 1');
                    $stmt->execute(['id' => $id]);
                    $username = $stmt->fetchColumn();
                    
                    // Delete user
                    $stmt = $db->prepare('DELETE FROM users WHERE id = :id');
                    $stmt->execute(['id' => $id]);
                    
                    $success = 'User deleted successfully!';
                    log_activity('admin', 'Deleted user: ' . $username);
                } catch (PDOException $e) {
                    $error = 'Database error. Please try again later.';
                    log_activity('error', 'Database error in users deletion: ' . $e->getMessage());
                }
            }
        }
    }
}

// Handle edit request
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $db = get_db_connection();
        $stmt = $db->prepare('SELECT id, username, email, first_name, last_name, role FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $user = $result;
            $user['password'] = ''; // Don't show password
            $edit_mode = true;
        }
    } catch (PDOException $e) {
        $error = 'Database error. Please try again later.';
        log_activity('error', 'Database error in users edit: ' . $e->getMessage());
    }
}

// Get all users
try {
    $db = get_db_connection();
    $stmt = $db->query('SELECT id, username, email, first_name, last_name, role, last_login FROM users ORDER BY role, username');
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Database error. Please try again later.';
    log_activity('error', 'Database error in users listing: ' . $e->getMessage());
}

// Set page title
$page_title = "Manage Users";

// Include admin header
include 'includes/admin-header.php';
?>

<div class="admin-users">
    <div class="page-header">
        <h1><?php echo $edit_mode ? 'Edit User' : 'Manage Users'; ?></h1>
        <p><?php echo $edit_mode ? 'Update existing user details' : 'Create and manage admin user accounts'; ?></p>
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
            <form class="admin-form" method="POST" action="users.php">
                <?php echo csrf_token_field(); ?>
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="editor" <?php echo $user['role'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
                            <option value="viewer" <?php echo $user['role'] === 'viewer' ? 'selected' : ''; ?>>Viewer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><?php echo $edit_mode ? 'Password (leave blank to keep current)' : 'Password'; ?></label>
                        <input type="password" id="password" name="password" <?php echo $edit_mode ? '' : 'required'; ?>>
                        <?php if ($edit_mode): ?>
                            <small>Leave blank to keep current password</small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-actions">
                    <?php if ($edit_mode): ?>
                        <a href="users.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update User' : 'Create User'; ?></button>
                </div>
            </form>
        </div>
        
        <div class="admin-list-container">
            <h2>All Users</h2>
            
            <?php if (empty($users)): ?>
                <p>No users found. Create your first user using the form.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $item): ?>
                            <tr>
                                <td><?php echo $item['username']; ?></td>
                                <td><?php echo $item['first_name'] . ' ' . $item['last_name']; ?></td>
                                <td><?php echo $item['email']; ?></td>
                                <td>
                                    <?php if ($item['role'] === 'admin'): ?>
                                        <span class="status-badge status-admin">Admin</span>
                                    <?php elseif ($item['role'] === 'editor'): ?>
                                        <span class="status-badge status-editor">Editor</span>
                                    <?php else: ?>
                                        <span class="status-badge status-viewer">Viewer</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $item['last_login'] ? date('M j, Y H:i', strtotime($item['last_login'])) : 'Never'; ?></td>
                                <td class="actions-cell">
                                    <a href="users.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    
                                    <?php if ($item['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" action="users.php" class="inline-form">
                                            <?php echo csrf_token_field(); ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger delete-btn">Delete</button>
                                        </form>
                                    <?php endif; ?>
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

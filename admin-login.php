<?php
/**
 * Admin login page for Genuine Landscapers website
 * Handles authentication for admin users
 */

// Include configuration
require_once 'php/config.php';

// Initialize variables
$error = '';
$username = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to admin dashboard
    header('Location: admin/dashboard.php');
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $error = 'Security validation failed. Please try again.';
        log_activity('error', 'CSRF validation failed on admin login');
    } else {
        // Validate form data
        if (empty($_POST['username'])) {
            $error = 'Username is required';
        } elseif (empty($_POST['password'])) {
            $error = 'Password is required';
        } else {
            // Sanitize input
            $username = sanitize_input($_POST['username']);
            $password = $_POST['password']; // Don't sanitize password before verification
            
            try {
                // Get database connection
                $db = get_db_connection();
                
                // Check if user exists
                $stmt = $db->prepare('SELECT id, username, password, first_name, last_name, role FROM users WHERE username = :username LIMIT 1');
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    
                    // Update last login time
                    $update_stmt = $db->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
                    $update_stmt->execute(['id' => $user['id']]);
                    
                    // Log successful login
                    log_activity('auth', 'User ' . $username . ' logged in successfully');
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Redirect to admin dashboard
                    header('Location: admin/dashboard.php');
                    exit;
                } else {
                    // Invalid credentials
                    $error = 'Invalid username or password';
                    log_activity('auth', 'Failed login attempt for user ' . $username);
                }
            } catch (PDOException $e) {
                // Database error
                $error = 'Authentication failed. Please try again later.';
                log_activity('error', 'Database error during login: ' . $e->getMessage());
            }
        }
    }
}

// Set page title and description
$page_title = "Admin Login";
$page_description = "Secure login for Genuine Landscapers administrators.";

// Include header (with minimal styling for login page)
include 'includes/header.php';
?>

<section class="admin-login">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1>Admin Login</h1>
                <p>Enter your credentials to access the admin dashboard</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <?php echo csrf_token_field(); ?>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-full">Login</button>
                </div>
            </form>
            
            <div class="login-footer">
                <p><a href="index.php">Return to Homepage</a></p>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>

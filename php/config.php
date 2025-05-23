<?php
/**
 * Configuration file for Genuine Landscapers website
 * Contains database connection settings and global configuration
 */

// Error reporting settings (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define base path for includes
define('BASE_PATH', realpath(dirname(__FILE__) . '/..'));

// Site configuration
define('SITE_NAME', 'Genuine Landscapers');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);
define('ADMIN_EMAIL', 'info@genuinelandscapers.ca');

// Email settings
define('MAIL_FROM', 'noreply@genuinelandscapers.ca');
define('MAIL_FROM_NAME', 'Genuine Landscapers');

// Form security
define('FORM_TOKEN_TIMEOUT', 3600); // 1 hour in seconds

// Time zone
date_default_timezone_set('America/Toronto');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'genuine_landscapers');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get database connection
 * 
 * @return PDO Database connection object
 */
function get_db_connection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error but don't expose details
            log_activity('error', 'Database connection failed');
            // In production, you might want to redirect to an error page
            // header('Location: /error.php');
            // exit;
        }
    }
    
    return $pdo;
}

// CSRF Protection
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Generate CSRF token field for forms
 * 
 * @return string HTML input field with CSRF token
 */
function csrf_token_field() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Sanitize user input
 * 
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate CSRF token
 * 
 * @param string $token Token from form submission
 * @return bool True if token is valid
 */
function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Generate a new CSRF token
 * 
 * @return string New CSRF token
 */
function refresh_csrf_token() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

/**
 * Log form submissions and errors
 * 
 * @param string $type Type of log entry (form, error, etc.)
 * @param string $message Log message
 * @return void
 */
function log_activity($type, $message) {
    $log_file = BASE_PATH . '/logs/' . date('Y-m-d') . '.log';
    $log_dir = dirname($log_file);
    
    // Create logs directory if it doesn't exist
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $log_entry = "[$timestamp] [$type] [$ip] $message" . PHP_EOL;
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Get the correct path for assets based on current page depth
 * 
 * @param string $path Relative path to the asset
 * @return string Corrected path to the asset
 */
function get_asset_path($path) {
    // Get the current script path relative to the document root
    $current_path = $_SERVER['PHP_SELF'];
    $depth = substr_count($current_path, '/') - 1;
    
    // If we're in a subdirectory, adjust the path
    if ($depth > 0) {
        return str_repeat('../', $depth) . $path;
    }
    
    return $path;
}

/**
 * Get setting value from database
 * 
 * @param string $key Setting key
 * @param string $default Default value if setting not found
 * @return string Setting value
 */
function get_setting($key, $default = '') {
    try {
        $db = get_db_connection();
        $stmt = $db->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch();
        
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        log_activity('error', 'Failed to get setting: ' . $key);
        return $default;
    }
}

/**
 * Update setting value in database
 * 
 * @param string $key Setting key
 * @param string $value Setting value
 * @param string $group Setting group
 * @return bool True if successful
 */
function update_setting($key, $value, $group = 'general') {
    try {
        $db = get_db_connection();
        
        // Check if setting exists
        $stmt = $db->prepare('SELECT id FROM settings WHERE setting_key = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing setting
            $stmt = $db->prepare('UPDATE settings SET setting_value = :value, setting_group = :group WHERE setting_key = :key');
            $stmt->execute([
                'key' => $key,
                'value' => $value,
                'group' => $group
            ]);
        } else {
            // Insert new setting
            $stmt = $db->prepare('INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (:key, :value, :group)');
            $stmt->execute([
                'key' => $key,
                'value' => $value,
                'group' => $group
            ]);
        }
        
        return true;
    } catch (PDOException $e) {
        log_activity('error', 'Failed to update setting: ' . $key);
        return false;
    }
}

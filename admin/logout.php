<?php
/**
 * Admin logout script for Genuine Landscapers website
 * Handles user logout and session destruction
 */

// Include configuration
require_once '../php/config.php';

// Destroy session
session_start();
$_SESSION = array();

// If a session cookie is used, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Log the logout activity
log_activity('auth', 'User logged out');

// Redirect to login page
header('Location: ../admin-login.php');
exit;

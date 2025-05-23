<?php
/**
 * Form Utilities for Genuine Landscapers
 * Common functions for form handling and validation
 */

// Include configuration
require_once 'config.php';

/**
 * Generate CSRF token input field
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
 * Display form error message
 * 
 * @param array $errors Array of form errors
 * @param string $field Field name to check for errors
 * @return string HTML error message if field has error
 */
function form_error($errors, $field) {
    if (isset($errors[$field])) {
        return '<div class="error-message">' . $errors[$field] . '</div>';
    }
    return '';
}

/**
 * Validate form input
 * 
 * @param array $data Form data to validate
 * @param array $rules Validation rules
 * @return array Array of validation errors
 */
function validate_form($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        // Required field validation
        if (strpos($rule, 'required') !== false && (empty($data[$field]) || $data[$field] === '')) {
            $errors[$field] = 'This field is required';
            continue;
        }
        
        // Skip other validations if field is empty and not required
        if (empty($data[$field]) && strpos($rule, 'required') === false) {
            continue;
        }
        
        // Email validation
        if (strpos($rule, 'email') !== false && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $errors[$field] = 'Please enter a valid email address';
        }
        
        // Phone validation
        if (strpos($rule, 'phone') !== false) {
            $phone_pattern = '/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/';
            if (!preg_match($phone_pattern, $data[$field])) {
                $errors[$field] = 'Please enter a valid phone number';
            }
        }
        
        // Date validation
        if (strpos($rule, 'date') !== false) {
            $date_pattern = '/^\d{4}-\d{2}-\d{2}$/';
            if (!preg_match($date_pattern, $data[$field])) {
                $errors[$field] = 'Please enter a valid date in YYYY-MM-DD format';
            }
        }
        
        // Future date validation
        if (strpos($rule, 'future_date') !== false) {
            try {
                $date = new DateTime($data[$field]);
                $today = new DateTime('today');
                if ($date < $today) {
                    $errors[$field] = 'Please select a future date';
                }
            } catch (Exception $e) {
                $errors[$field] = 'Invalid date format';
            }
        }
    }
    
    return $errors;
}

/**
 * Format service name for display
 * 
 * @param string $service Service name in slug format
 * @return string Formatted service name
 */
function format_service_name($service) {
    return ucwords(str_replace('-', ' ', $service));
}

/**
 * Format date for display
 * 
 * @param string $date Date in YYYY-MM-DD format
 * @return string Formatted date
 */
function format_date($date) {
    try {
        $date_obj = new DateTime($date);
        return $date_obj->format('l, F j, Y');
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Generate reference number
 * 
 * @param string $prefix Prefix for reference number
 * @param string $data Data to use for unique hash
 * @return string Reference number
 */
function generate_reference($prefix, $data) {
    return $prefix . '-' . date('Ymd') . '-' . substr(md5($data . time()), 0, 6);
}

<?php
/**
 * Callback Request Handler for Genuine Landscapers
 * Processes callback request form submissions
 */

// Include configuration
require_once 'config.php';
require_once 'form-utils.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $response['message'] = 'Security validation failed. Please try again.';
        log_activity('error', 'CSRF validation failed on callback form');
        echo json_encode($response);
        exit;
    }
    
    // Define validation rules
    $rules = [
        'name' => 'required',
        'phone' => 'required|phone',
        'address' => 'required',
        'service' => 'required'
    ];
    
    // Validate form data
    $errors = validate_form($_POST, $rules);
    
    // If there are validation errors
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = 'Please correct the errors and try again.';
        echo json_encode($response);
        exit;
    }
    
    // Sanitize input data
    $name = sanitize_input($_POST['name']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $service = sanitize_input($_POST['service']);
    $message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
    
    // Format service name
    $service_formatted = format_service_name($service);
    
    // Generate reference number
    $reference = generate_reference('CALLBACK', $phone);
    
    // Prepare email content
    $email_subject = "New Callback Request - $reference";
    $email_body = "
        <html>
        <head>
            <title>New Callback Request</title>
        </head>
        <body>
            <h2>New Callback Request</h2>
            <p><strong>Reference:</strong> $reference</p>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Address:</strong> $address</p>
            <p><strong>Service:</strong> $service_formatted</p>
            <p><strong>Message:</strong> $message</p>
            <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
        </body>
        </html>
    ";
    
    // Email headers
    $headers = "From: " . MAIL_FROM . "\r\n";
    $headers .= "Reply-To: noreply@genuinelandscapers.ca\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Send email notification to admin
    $mail_sent = mail(ADMIN_EMAIL, $email_subject, $email_body, $headers);
    
    if ($mail_sent) {
        // Log successful submission
        log_activity('form', "Callback request from $name ($phone)");
        
        // Set success response
        $response['success'] = true;
        $response['message'] = 'Thank you for your callback request! We will contact you within 24 hours.';
        $response['reference'] = $reference;
    } else {
        // Log error
        log_activity('error', "Failed to process callback request for $name ($phone)");
        
        // Set error response
        $response['message'] = 'Sorry, there was a problem processing your callback request. Please try again later.';
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

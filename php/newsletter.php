<?php
/**
 * Newsletter Subscription Handler for Genuine Landscapers
 * Processes newsletter subscription requests
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
        log_activity('error', 'CSRF validation failed on newsletter form');
        echo json_encode($response);
        exit;
    }
    
    // Validate email
    $errors = [];
    if (empty($_POST['email'])) {
        $errors['email'] = 'Email address is required';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    // If there are validation errors
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = 'Please correct the errors and try again.';
        echo json_encode($response);
        exit;
    }
    
    // Sanitize input data
    $email = sanitize_input($_POST['email']);
    
    // Prepare email content
    $email_subject = "New Newsletter Subscription";
    $email_body = "
        <html>
        <head>
            <title>New Newsletter Subscription</title>
        </head>
        <body>
            <h2>New Newsletter Subscription</h2>
            <p><strong>Email:</strong> $email</p>
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
        log_activity('form', "Newsletter subscription from $email");
        
        // Send confirmation email to subscriber
        $confirmation_subject = "Welcome to Genuine Landscapers Newsletter";
        $confirmation_body = "
            <html>
            <head>
                <title>Newsletter Subscription Confirmation</title>
            </head>
            <body>
                <h2>Thank you for subscribing to our newsletter!</h2>
                <p>You have successfully subscribed to the Genuine Landscapers newsletter.</p>
                <p>You'll now receive seasonal landscaping tips, special offers, and company updates directly to your inbox.</p>
                <p>If you didn't subscribe to our newsletter or wish to unsubscribe, please click the unsubscribe link at the bottom of any newsletter email.</p>
                <p>Best regards,<br>The Genuine Landscapers Team</p>
            </body>
            </html>
        ";
        
        $confirmation_headers = "From: " . MAIL_FROM . "\r\n";
        $confirmation_headers .= "MIME-Version: 1.0\r\n";
        $confirmation_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        mail($email, $confirmation_subject, $confirmation_body, $confirmation_headers);
        
        // Set success response
        $response['success'] = true;
        $response['message'] = 'Thank you for subscribing to our newsletter!';
    } else {
        // Log error
        log_activity('error', "Failed to process newsletter subscription for $email");
        
        // Set error response
        $response['message'] = 'Sorry, there was a problem processing your subscription. Please try again later.';
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

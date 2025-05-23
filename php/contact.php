<?php
/**
 * Contact Form Handler for Genuine Landscapers
 * Processes contact form submissions
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
        log_activity('error', 'CSRF validation failed on contact form');
        echo json_encode($response);
        exit;
    }
    
    // Define validation rules
    $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'required|phone',
        'message' => 'required'
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
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $message = sanitize_input($_POST['message']);
    $service = isset($_POST['service']) ? sanitize_input($_POST['service']) : 'Not specified';
    
    // Generate reference number
    $reference = generate_reference('CONTACT', $email);
    
    // Prepare email content
    $email_subject = "New Contact Form Submission - $reference";
    $email_body = "
        <html>
        <head>
            <title>New Contact Form Submission</title>
        </head>
        <body>
            <h2>New Contact Form Submission</h2>
            <p><strong>Reference:</strong> $reference</p>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Service:</strong> $service</p>
            <p><strong>Message:</strong> $message</p>
            <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
        </body>
        </html>
    ";
    
    // Email headers
    $headers = "From: " . MAIL_FROM . "\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Send email notification to admin
    $mail_sent = mail(ADMIN_EMAIL, $email_subject, $email_body, $headers);
    
    if ($mail_sent) {
        // Log successful submission
        log_activity('form', "Contact form submission from $name ($email)");
        
        // Send confirmation email to user
        $confirmation_subject = "Thank you for contacting Genuine Landscapers - $reference";
        $confirmation_body = "
            <html>
            <head>
                <title>Contact Form Confirmation</title>
            </head>
            <body>
                <h2>Thank you for contacting us!</h2>
                <p>Dear $name,</p>
                <p>We have received your message and will get back to you as soon as possible.</p>
                <p>Your reference number is: <strong>$reference</strong></p>
                <p>Here's a summary of your message:</p>
                <p><strong>Service:</strong> $service</p>
                <p><strong>Message:</strong> $message</p>
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
        $response['message'] = 'Thank you for your message! We will get back to you soon.';
        $response['reference'] = $reference;
    } else {
        // Log error
        log_activity('error', "Failed to process contact form for $name ($email)");
        
        // Set error response
        $response['message'] = 'Sorry, there was a problem sending your message. Please try again later.';
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

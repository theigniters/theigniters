<?php
// submit_contact.php

header("Content-Type: application/json"); // Set header to return JSON response
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin (for development, restrict in production)
header("Access-Control-Allow-Methods: POST"); // Allow only POST requests
header("Access-Control-Allow-Headers: Content-Type"); // Allow Content-Type header

// Initialize response array
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true); // Decode JSON into an associative array

    // Validate and sanitize input data
    $name = isset($data['name']) ? htmlspecialchars(trim($data['name'])) : '';
    $email = isset($data['email']) ? htmlspecialchars(trim($data['email'])) : '';
    $message = isset($data['message']) ? htmlspecialchars(trim($data['message'])) : '';

    // Basic server-side validation
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
    } else {
        // --- Email Sending Logic ---
        $to = 'your_email@example.com'; // ** IMPORTANT: Replace with your actual email address **
        $subject = 'New Contact Form Submission from The Igniters Website';
        $email_body = "Name: " . $name . "\n" .
                      "Email: " . $email . "\n\n" .
                      "Message:\n" . $message;
        $headers = 'From: webmaster@yourdomain.com' . "\r\n" . // ** IMPORTANT: Replace with your website's email address **
                   'Reply-To: ' . $email . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        // Attempt to send the email
        if (mail($to, $subject, $email_body, $headers)) {
            $response['success'] = true;
            $response['message'] = 'Message sent successfully!';
        } else {
            $response['message'] = 'Failed to send message. Please try again later.';
            // For debugging, you might log mail() errors on your server
            // error_log("Mail sending failed from contact form. To: $to, Subject: $subject, From: $headers");
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Send the JSON response back to the client
echo json_encode($response);
?>

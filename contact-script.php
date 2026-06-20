<?php
// Database connection
include('sql.php'); // Make sure this file contains your database connection

// Set header to return JSON
header('Content-Type: application/json');

// Check if form was submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and sanitize form data
    $name = isset($_POST['user_name']) ? mysqli_real_escape_string($conn, $_POST['user_name']) : '';
    $email = isset($_POST['user_email']) ? mysqli_real_escape_string($conn, $_POST['user_email']) : '';
    $mobile = isset($_POST['user_mobile']) ? mysqli_real_escape_string($conn, $_POST['user_mobile']) : '';
    $subject = isset($_POST['user_subject']) ? mysqli_real_escape_string($conn, $_POST['user_subject']) : '';
    $address = isset($_POST['user_address']) ? mysqli_real_escape_string($conn, $_POST['user_address']) : '';
    $message = isset($_POST['user_message']) ? mysqli_real_escape_string($conn, $_POST['user_message']) : '';
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($mobile) || empty($subject) || empty($address) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required. Please fill all fields.'
        ]);
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid email address.'
        ]);
        exit;
    }
    
    // Validate mobile number (10 digits)
    if (!preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid 10-digit mobile number.'
        ]);
        exit;
    }
    
    // Prepare the combined message (subject + actual message)
    $full_message = "Subject: " . $subject . "\n\n" . $message;
    
    // Insert data into contactus table
    $query = "INSERT INTO contactus (c_name, c_mobile, c_email, c_address, c_message, user_type, is_read) 
              VALUES ('$name', '$mobile', '$email', '$address', '$full_message', 'customer', 0)";
    
    if (mysqli_query($conn, $query)) {
        // Get the inserted ID
        $inserted_id = mysqli_insert_id($conn);
        
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for contacting us! We will get back to you soon.',
            'inquiry_id' => $inserted_id
        ]);
    } else {
        // Log error for debugging
        error_log("Database Error: " . mysqli_error($conn));
        
        echo json_encode([
            'success' => false,
            'message' => 'Database error. Please try again later. Error: ' . mysqli_error($conn)
        ]);
    }
    
} else {
    // If not POST request
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Please submit the form properly.'
    ]);
}
?>
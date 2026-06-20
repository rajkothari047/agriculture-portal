<?php
// floginScript.php - Complete Login Script
session_start();
$error = '';

require('../sql.php');

if(isset($_POST['farmerlogin'])) {
    // Get and sanitize inputs
    $farmer_email = mysqli_real_escape_string($conn, trim($_POST['farmer_email']));
    $farmer_password = $_POST['farmer_password'];
    
    // Debug - Uncomment to see what's being sent (remove after testing)
    // error_log("Login attempt for: " . $farmer_email);
    
    if(empty($farmer_email) || empty($farmer_password)) {
        $error = "Please enter both email and password!";
    } else {
        // Query to get user by email
        $farmerquery = "SELECT * FROM `farmerlogin` WHERE email='$farmer_email'";
        $result = mysqli_query($conn, $farmerquery);
        
        if(mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            
            // Verify password
            if(password_verify($farmer_password, $row['password'])) {
                // Password correct - set session and redirect
                $_SESSION['farmer_login_user'] = $farmer_email;
                $_SESSION['farmer_id'] = $row['farmer_id'];
                $_SESSION['farmer_name'] = $row['farmer_name'];
                
                // Clear any existing OTP data
                unset($_SESSION['otp_attempts']);
                unset($_SESSION['otp_locked_until']);
                
                header("Location: ftwostep.php");
                exit();
            } else {
                $error = "Invalid email or password!";
                // Debug - log failed password attempt
                error_log("Failed password attempt for: " . $farmer_email);
            }
        } else {
            $error = "Invalid email or password!";
            // Debug - log email not found
            error_log("Email not found: " . $farmer_email);
        }
    }
}
?>
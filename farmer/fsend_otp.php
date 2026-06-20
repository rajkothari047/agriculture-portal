<?php
// fsend_otp.php - Generate and send OTP via email
session_start();
require('../sql.php');
require('email_config.php');

if(!isset($_SESSION['farmer_login_user'])){
    echo "error";
    exit();
}

$user = $_SESSION['farmer_login_user'];

// Get user details
$query = "SELECT farmer_id, farmer_name, email FROM farmerlogin WHERE email = '$user' OR phone_no = '$user'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 1) {
    $user_data = mysqli_fetch_assoc($result);
    $farmer_id = $user_data['farmer_id'];
    $farmer_name = $user_data['farmer_name'];
    $farmer_email = $user_data['email'];
    
    // Generate 5-digit OTP
    $otp = rand(10000, 99999);
    
    // Set OTP expiry time (25 minutes from now)
    $expiry_time = date('Y-m-d H:i:s', strtotime('+25 minutes'));
    
    // Update OTP in database - Clear any old OTP first
    $clear_query = "UPDATE farmerlogin SET otp = NULL, otp_expiry = NULL WHERE farmer_id = '$farmer_id'";
    mysqli_query($conn, $clear_query);
    
    // Insert new OTP
    $update_query = "UPDATE farmerlogin 
                     SET otp = '$otp', 
                         otp_expiry = '$expiry_time',
                         last_otp_sent = NOW() 
                     WHERE farmer_id = '$farmer_id'";
    
    if(mysqli_query($conn, $update_query)) {
        // Verify the OTP was saved correctly
        $verify_query = "SELECT otp FROM farmerlogin WHERE farmer_id = '$farmer_id'";
        $verify_result = mysqli_query($conn, $verify_query);
        $verify_row = mysqli_fetch_assoc($verify_result);
        
        if($verify_row['otp'] == $otp) {
            // Send OTP via email
            $email_result = sendOTPToEmail($farmer_email, $farmer_name, $otp);
            
            if($email_result['success']) {
                $_SESSION['otp_sent_time'] = time();
                $_SESSION['otp_attempts'] = 0;
                $_SESSION['current_otp'] = $otp; // Store in session for debugging
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
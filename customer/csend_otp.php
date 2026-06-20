<?php
// csend_otp.php - Generate and send OTP via email for customers
session_start();
require('../sql.php');
require('email_config_customer.php');

if(!isset($_SESSION['customer_login_user'])){
    echo "error";
    exit();
}

$user = $_SESSION['customer_login_user'];

// Get customer details from database
$query = "SELECT cust_id, cust_name, email FROM custlogin WHERE email = '$user' OR phone_no = '$user'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 1) {
    $user_data = mysqli_fetch_assoc($result);
    $cust_id = $user_data['cust_id'];
    $cust_name = $user_data['cust_name'];
    $cust_email = $user_data['email'];
    
    // Generate 5-digit OTP
    $otp = rand(10000, 99999);
    
    // Set OTP expiry time (25 minutes from now)
    $expiry_time = date('Y-m-d H:i:s', strtotime('+25 minutes'));
    
    // Clear any old OTP first
    $clear_query = "UPDATE custlogin SET otp = NULL, otp_expiry = NULL WHERE cust_id = '$cust_id'";
    mysqli_query($conn, $clear_query);
    
    // Insert new OTP
    $update_query = "UPDATE custlogin 
                     SET otp = '$otp', 
                         otp_expiry = '$expiry_time',
                         last_otp_sent = NOW() 
                     WHERE cust_id = '$cust_id'";
    
    if(mysqli_query($conn, $update_query)) {
        // Verify the OTP was saved correctly
        $verify_query = "SELECT otp FROM custlogin WHERE cust_id = '$cust_id'";
        $verify_result = mysqli_query($conn, $verify_query);
        $verify_row = mysqli_fetch_assoc($verify_result);
        
        if($verify_row['otp'] == $otp) {
            // Send OTP via email
            $email_result = sendOTPToCustomerEmail($cust_email, $cust_name, $otp);
            
            if($email_result['success']) {
                $_SESSION['otp_sent_time'] = time();
                $_SESSION['customer_otp_attempts'] = 0;
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
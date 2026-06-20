<?php
// ccheck_otp.php - Verify OTP for customers
session_start();
require('../sql.php');

date_default_timezone_set('Asia/Kolkata');

if(!isset($_SESSION['customer_login_user'])){
    echo "no";
    exit();
}

// Check if account is locked
if(isset($_SESSION['customer_otp_locked_until']) && $_SESSION['customer_otp_locked_until'] > time()) {
    echo "locked";
    exit();
}

$user = $_SESSION['customer_login_user'];
$entered_otp = mysqli_real_escape_string($conn, $_POST['otp']);

// Get OTP from database
$query = "SELECT otp, otp_expiry FROM custlogin 
          WHERE (email = '$user' OR phone_no = '$user')";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $db_otp = $row['otp'];
    $db_expiry = $row['otp_expiry'];
    
    // Check if OTP exists
    if($db_otp == NULL || $db_otp == '') {
        echo "expired";
        exit();
    }
    
    // Check if OTP matches
    if($db_otp == $entered_otp) {
        // Check if OTP is still valid
        $current_time = date('Y-m-d H:i:s');
        if($db_expiry > $current_time) {
            // OTP is valid - Clear it and grant access
            $clear = "UPDATE custlogin SET otp = NULL, otp_expiry = NULL WHERE (email = '$user' OR phone_no = '$user')";
            mysqli_query($conn, $clear);
            
            $_SESSION['customer_otp_attempts'] = 0;
            unset($_SESSION['customer_otp_locked_until']);
            
            echo "yes";
        } else {
            echo "expired";
        }
    } else {
        // Invalid OTP
        if(!isset($_SESSION['customer_otp_attempts'])) {
            $_SESSION['customer_otp_attempts'] = 1;
        } else {
            $_SESSION['customer_otp_attempts']++;
        }
        
        if($_SESSION['customer_otp_attempts'] >= 5) {
            $_SESSION['customer_otp_locked_until'] = time() + 900;
            echo "locked";
        } else {
            echo "no";
        }
    }
} else {
    echo "no";
}
?>
<?php
// fcheck_otp.php - Verify OTP
session_start();
require('../sql.php');

date_default_timezone_set('Asia/Kolkata');

if(!isset($_SESSION['farmer_login_user'])){
    echo "no";
    exit();
}

// Check if account is locked
if(isset($_SESSION['otp_locked_until']) && $_SESSION['otp_locked_until'] > time()) {
    echo "locked";
    exit();
}

$user = $_SESSION['farmer_login_user'];
$entered_otp = mysqli_real_escape_string($conn, $_POST['otp']);

// Get OTP from database
$query = "SELECT otp, otp_expiry FROM farmerlogin 
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
            $clear = "UPDATE farmerlogin SET otp = NULL, otp_expiry = NULL WHERE (email = '$user' OR phone_no = '$user')";
            mysqli_query($conn, $clear);
            
            $_SESSION['otp_attempts'] = 0;
            unset($_SESSION['otp_locked_until']);
            unset($_SESSION['current_otp']);
            
            echo "yes";
        } else {
            echo "expired";
        }
    } else {
        // Invalid OTP
        if(!isset($_SESSION['otp_attempts'])) {
            $_SESSION['otp_attempts'] = 1;
        } else {
            $_SESSION['otp_attempts']++;
        }
        
        if($_SESSION['otp_attempts'] >= 5) {
            $_SESSION['otp_locked_until'] = time() + 900;
            echo "locked";
        } else {
            echo "no";
        }
    }
} else {
    echo "no";
}
?>
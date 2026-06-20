<?php
// fget_attempts.php - Get remaining OTP attempts
session_start();

if(!isset($_SESSION['farmer_login_user'])){
    echo "0";
    exit();
}

if(isset($_SESSION['otp_attempts'])) {
    $remaining = 5 - $_SESSION['otp_attempts'];
    echo max(0, $remaining);
} else {
    echo "5";
}
?>
<?php
// cget_attempts.php - Get remaining OTP attempts for customers
session_start();

if(!isset($_SESSION['customer_login_user'])){
    echo "0";
    exit();
}

if(isset($_SESSION['customer_otp_attempts'])) {
    $remaining = 5 - $_SESSION['customer_otp_attempts'];
    echo max(0, $remaining);
} else {
    echo "5";
}
?>
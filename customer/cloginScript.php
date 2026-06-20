<?php
// cloginScript.php - Customer Login Script with password hashing support
session_start();
$error = '';

require('../sql.php');

if(isset($_POST['customerlogin'])) {
    $customer_email = mysqli_real_escape_string($conn, $_POST['customer_email']);
    $customer_password = $_POST['customer_password'];
    
    // Query to get user by email
    $customerquery = "SELECT * FROM `custlogin` WHERE email='$customer_email'";
    $result = mysqli_query($conn, $customerquery);
    
    if(mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verify password using password_verify() for hashed passwords
        if(password_verify($customer_password, $row['password'])) {
            // Password is correct
            $_SESSION['customer_login_user'] = $customer_email;
            $_SESSION['customer_id'] = $row['cust_id'];
            $_SESSION['customer_name'] = $row['cust_name'];
            
            // Clear any existing OTP data
            unset($_SESSION['customer_otp_attempts']);
            unset($_SESSION['customer_otp_locked_until']);
            
            header("location: ctwostep.php");
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>
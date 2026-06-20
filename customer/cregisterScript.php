<?php
// cregisterScript.php - Customer Registration Script with Password Hashing
session_start();
require('../sql.php');

$error = '';
$success = '';

if(isset($_POST['register'])) {
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $state_code = mysqli_real_escape_string($conn, $_POST['state']);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    
    // Validation
    if(empty($name) || empty($email) || empty($mobile) || empty($address) || empty($city) || empty($pincode) || empty($state_code) || empty($password)) {
        $error = "Please fill all required fields!";
    }
    // Validate email format
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    }
    // Validate mobile number (10 digits, starts with 6-9)
    elseif(!preg_match("/^[6-9][0-9]{9}$/", $mobile)) {
        $error = "Please enter a valid 10-digit mobile number!";
    }
    // Validate pincode (6 digits)
    elseif(!preg_match("/^[0-9]{6}$/", $pincode)) {
        $error = "Please enter a valid 6-digit pincode!";
    }
    // Validate password strength
    elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    }
    elseif($password !== $confirmpassword) {
        $error = "Passwords do not match!";
    }
    else {
        // Check if email already exists
        $check_email = mysqli_query($conn, "SELECT * FROM custlogin WHERE email = '$email'");
        if(mysqli_num_rows($check_email) > 0) {
            $error = "This email address already exists! Please use a different email or login.";
        }
        // Check if mobile already exists
        elseif(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM custlogin WHERE phone_no = '$mobile'")) > 0) {
            $error = "This mobile number already exists! Please use a different number or login.";
        }
        else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Get state name from code
            $state_query = mysqli_query($conn, "SELECT StateName FROM state WHERE StCode = '$state_code'");
            $state_row = mysqli_fetch_assoc($state_query);
            $state_name = $state_row['StateName'];
            
            // Insert into database
            $insert_query = "INSERT INTO custlogin (cust_name, email, phone_no, password, address, city, pincode, state, otp, otp_expiry, last_otp_sent, status) 
                             VALUES ('$name', '$email', '$mobile', '$hashed_password', '$address', '$city', '$pincode', '$state_name', NULL, NULL, NULL, 1)";
            
            if(mysqli_query($conn, $insert_query)) {
                // Registration successful - Set session and redirect to OTP page
                $_SESSION['customer_login_user'] = $email;
                $_SESSION['customer_id'] = mysqli_insert_id($conn);
                $_SESSION['customer_name'] = $name;
                
                // Clear any existing OTP data
                unset($_SESSION['customer_otp_attempts']);
                unset($_SESSION['customer_otp_locked_until']);
                
                // Redirect to OTP page
                header("location: ctwostep.php");
                exit();
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>
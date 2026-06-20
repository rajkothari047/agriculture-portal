<?php
// fregisterScript.php - Updated Registration Script
session_start(); // Starting Session
require('../sql.php'); // Includes Login Script
global $error, $success;

// Function for email validation
function is_valid_email($email)
{
	global $conn;
	global $error;
	
    $slquery = "SELECT farmer_id FROM farmerlogin WHERE email = '$email'";
    $selectresult = mysqli_query($conn, $slquery);
	$rowcount = mysqli_num_rows($selectresult);
	   
	if ($rowcount > 0) {
		$error = "This email already exists! Please use a different email.";
		return false;		
	}
    else {
        return true;
    }
}

// Function for mobile validation
function is_valid_mobile($mobile)
{
	global $conn;
	global $error;
	
	// Check mobile format (10 digits, starts with 6-9)
	if(!preg_match("/^[6-9][0-9]{9}$/", $mobile)) {
		$error = "Please enter a valid 10-digit mobile number!";
		return false;
	}
	
	// Check if mobile already exists
	$check_query = "SELECT farmer_id FROM farmerlogin WHERE phone_no = '$mobile'";
	$check_result = mysqli_query($conn, $check_query);
	if(mysqli_num_rows($check_result) > 0) {
		$error = "This mobile number already exists! Please use a different number.";
		return false;
	}
	
	return true;
}

// Function for password verification
function is_valid_passwords($password, $cpassword) 
{
	global $error;
	
	if ($password != $cpassword) {
		$error = "Your passwords do not match. Please type carefully!";
		return false;
	}
	
	// Password strength validation
	if(strlen($password) < 8) {
		$error = "Password must be at least 8 characters long!";
		return false;
	}
	
	if(!preg_match("/[A-Z]/", $password)) {
		$error = "Password must contain at least one uppercase letter!";
		return false;
	}
	
	if(!preg_match("/[0-9]/", $password)) {
		$error = "Password must contain at least one number!";
		return false;
	}
	
	if(!preg_match("/[!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?]/", $password)) {
		$error = "Password must contain at least one special character!";
		return false;
	}
	
	return true;
}

// Function for creating user with hashed password
function create_user($name, $password, $email, $mobile, $gender, $dob, $statename, $district, $city) 
{
	global $conn;
	
	// Hash the password for security
	$hashed_password = password_hash($password, PASSWORD_DEFAULT);
	
	$query = "INSERT INTO `farmerlogin` (farmer_name, password, email, phone_no, F_gender, F_birthday, F_State, F_District, F_Location, otp, otp_expiry, last_otp_sent) 
	  VALUES ('$name', '$hashed_password', '$email', '$mobile', '$gender', '$dob', '$statename', '$district', '$city', NULL, NULL, NULL)";
	  
	$result = mysqli_query($conn, $query);
	if($result){
		return true; // Success
	}else{
		return false; // Error somewhere
	}
}

// Code execution starts here after submit
if (isset($_POST['farmerregister'])){

    // Reading form values
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);	
	$mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
	$dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
	$district = mysqli_real_escape_string($conn, $_POST['district']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $password = $_POST['password'];
    $cpassword = $_POST['confirmpassword'];
	
	// Get state name
	$query5 = "SELECT StateName from state where StCode ='$state'";
	$ses_sq5 = mysqli_query($conn, $query5);
    $row5 = mysqli_fetch_assoc($ses_sq5);
    $statename = $row5['StateName'];

	// Validate all fields
	if (is_valid_email($email) == true && 
		is_valid_mobile($mobile) == true && 
		is_valid_passwords($password, $cpassword) == true)
    {	
        if (create_user($name, $password, $email, $mobile, $gender, $dob, $statename, $district, $city)) {
			// Registration successful - Set session and redirect to OTP page
			$_SESSION['farmer_login_user'] = $email; // Initializing Session    
			header("location: ftwostep.php");
			exit();
        } else {	
			$error = "Error While Registering User. Please try again!";
		}
    }
}
?>
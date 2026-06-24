<?php
include '../sql.php';

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check Login Session
if (!isset($_SESSION['farmer_login_user'])) {
    header("Location: login.php");
    exit();
}

$user_check = $_SESSION['farmer_login_user'];

// Fetch Farmer Details
$query = "SELECT farmer_name FROM farmerlogin WHERE email = '$user_check'";
$ses_sql = mysqli_query($conn, $query);

if ($ses_sql && mysqli_num_rows($ses_sql) > 0) {
    $row = mysqli_fetch_assoc($ses_sql);
    $login_session = $row['farmer_name'];
} else {
    $login_session = "";
}

$CustID = $user_check;
?>
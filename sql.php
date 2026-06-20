<?php
// connect to database
$servername="localhost";
$username="root";
$password="";
$dbname="agriculture_portal";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn){
    echo 'Connection error: ' . mysqli_connect_error();
}

// Set timezone to India (IST)
date_default_timezone_set('Asia/Kolkata');
mysqli_query($conn, "SET time_zone = '+05:30'");
mysqli_query($conn, "SET SESSION time_zone = '+05:30'");
?>
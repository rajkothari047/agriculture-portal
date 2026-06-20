<?php
// test_timezone.php - Check timezone settings
require('../sql.php');

echo "<h2>Timezone Debug Information</h2>";

echo "<h3>PHP Settings:</h3>";
echo "PHP Timezone: " . date_default_timezone_get() . "<br>";
echo "PHP Current Time: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Timestamp: " . time() . "<br>";

echo "<h3>MySQL Settings:</h3>";
$query = "SELECT NOW() as mysql_time, @@global.time_zone as global_tz, @@session.time_zone as session_tz";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
echo "MySQL Current Time: " . $row['mysql_time'] . "<br>";
echo "MySQL Global Timezone: " . $row['global_tz'] . "<br>";
echo "MySQL Session Timezone: " . $row['session_tz'] . "<br>";

echo "<h3>Comparison:</h3>";
$php_time = date('Y-m-d H:i:s');
$mysql_time = $row['mysql_time'];

echo "PHP Time: " . $php_time . "<br>";
echo "MySQL Time: " . $mysql_time . "<br>";

if($php_time == $mysql_time) {
    echo "✅ Times are synchronized!<br>";
} else {
    echo "❌ Times are NOT synchronized! Difference: " . (strtotime($php_time) - strtotime($mysql_time)) . " seconds<br>";
}

echo "<h3>Test OTP Insert:</h3>";

// Test inserting OTP
$test_otp = rand(10000, 99999);
$test_expiry = date('Y-m-d H:i:s', strtotime('+25 minutes'));

echo "Test OTP: $test_otp<br>";
echo "Test Expiry: $test_expiry<br>";

// Check if there's a logged in user
if(isset($_SESSION['farmer_login_user'])) {
    $user = $_SESSION['farmer_login_user'];
    $update = "UPDATE farmerlogin SET otp = '$test_otp', otp_expiry = '$test_expiry' WHERE email = '$user'";
    if(mysqli_query($conn, $update)) {
        echo "✅ Test OTP inserted successfully<br>";
        
        // Verify it
        $check = "SELECT otp, otp_expiry, NOW() as now FROM farmerlogin WHERE email = '$user'";
        $check_result = mysqli_query($conn, $check);
        $check_row = mysqli_fetch_assoc($check_result);
        
        echo "Inserted OTP: " . $check_row['otp'] . "<br>";
        echo "Inserted Expiry: " . $check_row['otp_expiry'] . "<br>";
        echo "Current DB Time: " . $check_row['now'] . "<br>";
        
        if(strtotime($check_row['otp_expiry']) > strtotime($check_row['now'])) {
            echo "✅ OTP is VALID<br>";
        } else {
            echo "❌ OTP is EXPIRED - This is the problem!<br>";
        }
    } else {
        echo "❌ Failed to insert test OTP: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "⚠️ Please login first to test OTP insertion<br>";
}
?>
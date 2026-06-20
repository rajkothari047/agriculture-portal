<?php
header('Content-Type: application/json');
include '../../sql.php';

$state = mysqli_real_escape_string($conn, $_GET['state']);
$query = "SELECT DISTINCT market_name FROM current_prices WHERE state='$state' ORDER BY market_name";
$result = mysqli_query($conn, $query);
$markets = [];

while ($row = mysqli_fetch_assoc($result)) {
    $markets[] = $row['market_name'];
}

echo json_encode($markets);
?>
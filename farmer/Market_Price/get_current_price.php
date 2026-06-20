<?php
header('Content-Type: application/json');
include '../../sql.php';

$state = mysqli_real_escape_string($conn, $_POST['state']);
$market = mysqli_real_escape_string($conn, $_POST['market']);
$commodity = mysqli_real_escape_string($conn, $_POST['commodity']);

$query = "SELECT * FROM current_prices WHERE state='$state' AND commodity='$commodity'";
if($market && $market != '') $query .= " AND market_name='$market'";
$query .= " ORDER BY updated_at DESC LIMIT 1";

$result = mysqli_query($conn, $query);
if(mysqli_num_rows($result) > 0) echo json_encode(['success' => true, 'price' => mysqli_fetch_assoc($result)]);
else echo json_encode(['success' => false]);
?>
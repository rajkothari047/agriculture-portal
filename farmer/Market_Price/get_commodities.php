<?php
header('Content-Type: application/json');
include '../../sql.php';

$state = mysqli_real_escape_string($conn, $_GET['state']);
$query = "SELECT DISTINCT commodity FROM current_prices WHERE state='$state' ORDER BY commodity";
$result = mysqli_query($conn, $query);
$commodities = [];

while ($row = mysqli_fetch_assoc($result)) {
    $commodities[] = $row['commodity'];
}

echo json_encode($commodities);
?>
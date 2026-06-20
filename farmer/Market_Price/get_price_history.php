<?php
header('Content-Type: application/json');
include '../../sql.php';

$commodity = mysqli_real_escape_string($conn, $_POST['commodity']);
$market = mysqli_real_escape_string($conn, $_POST['market']);

$query = "SELECT * FROM price_history WHERE commodity='$commodity' AND market_name='$market' AND price_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ORDER BY price_date DESC LIMIT 7";
$result = mysqli_query($conn, $query);
$history = [];
while($row = mysqli_fetch_assoc($result)) $history[] = $row;

$trend = "📈 Price Trend: ";
if(count($history) >= 2) {
    $oldest = $history[count($history)-1]['modal_price'];
    $newest = $history[0]['modal_price'];
    if($newest > $oldest) $trend .= "UP by " . ($newest - $oldest) . "₹ over last " . count($history) . " days";
    elseif($newest < $oldest) $trend .= "DOWN by " . ($oldest - $newest) . "₹ over last " . count($history) . " days";
    else $trend .= "STABLE over last " . count($history) . " days";
} else $trend = "Not enough data to show trend";

echo json_encode(['success' => true, 'history' => $history, 'trend' => $trend]);
?>
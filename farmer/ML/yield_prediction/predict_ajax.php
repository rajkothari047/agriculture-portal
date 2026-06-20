<?php
// predict_ajax.php - Handles AJAX prediction requests
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$state = $_POST['state'] ?? '';
$district = $_POST['district'] ?? '';
$season = $_POST['season'] ?? '';
$crop = $_POST['crop'] ?? '';
$area = $_POST['area'] ?? '';

if (empty($state) || empty($district) || empty($season) || empty($crop) || empty($area)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

// Run Python script
$Jstate = json_encode($state);
$Jdistrict = json_encode($district);
$Jseason = json_encode($season);
$Jcrop = json_encode($crop);
$Jarea = json_encode($area);

$start_time = microtime(true);
$command = escapeshellcmd("python yield_prediction.py $Jstate $Jdistrict $Jseason $Jcrop $Jarea 2>&1");
$predicted_yield_raw = shell_exec($command);
$end_time = microtime(true);
$duration = round($end_time - $start_time, 2);

$predicted_yield = floatval(trim($predicted_yield_raw));

if ($predicted_yield > 0) {
    echo json_encode([
        'status' => 'success',
        'prediction' => $predicted_yield,
        'time' => $duration,
        'state' => $state,
        'district' => $district,
        'season' => $season,
        'crop' => $crop,
        'area' => $area
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No valid prediction returned'
    ]);
}
?>
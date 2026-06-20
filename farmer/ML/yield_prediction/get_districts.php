<?php
header('Content-Type: application/json');

if (!isset($_POST['state']) || empty($_POST['state'])) {
    echo json_encode(['districts' => []]);
    exit;
}

$state = $_POST['state'];
$csv_file = 'crop_production.csv';
$districts = array();

// Read from CSV file
if (file_exists($csv_file)) {
    $handle = fopen($csv_file, 'r');
    if ($handle !== false) {
        $header = fgetcsv($handle);
        $state_index = array_search('State_Name', $header);
        $district_index = array_search('District_Name', $header);
        
        if ($state_index !== false && $district_index !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                if (isset($data[$state_index]) && trim($data[$state_index]) === $state) {
                    if (isset($data[$district_index]) && !empty(trim($data[$district_index]))) {
                        $district = trim($data[$district_index]);
                        $districts[$district] = $district;
                    }
                }
            }
        }
        fclose($handle);
    }
}

$districts = array_values($districts);
sort($districts);

echo json_encode(['districts' => $districts]);
?>
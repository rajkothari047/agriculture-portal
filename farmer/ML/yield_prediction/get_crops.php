<?php
header('Content-Type: application/json');

if (!isset($_POST['district']) || empty($_POST['district']) || !isset($_POST['season']) || empty($_POST['season'])) {
    echo json_encode(['crops' => []]);
    exit;
}

$district = $_POST['district'];
$season = $_POST['season'];
$csv_file = 'crop_production.csv';
$crops = array();

// Read from CSV file
if (file_exists($csv_file)) {
    $handle = fopen($csv_file, 'r');
    if ($handle !== false) {
        $header = fgetcsv($handle);
        $district_index = array_search('District_Name', $header);
        $season_index = array_search('Season', $header);
        $crop_index = array_search('Crop', $header);
        
        if ($district_index !== false && $season_index !== false && $crop_index !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                if (isset($data[$district_index]) && isset($data[$season_index]) && 
                    trim($data[$district_index]) === $district && 
                    trim($data[$season_index]) === $season) {
                    if (isset($data[$crop_index]) && !empty(trim($data[$crop_index]))) {
                        $crop = trim($data[$crop_index]);
                        $crops[$crop] = $crop;
                    }
                }
            }
        }
        fclose($handle);
    }
}

$crops = array_values($crops);
sort($crops);

echo json_encode(['crops' => $crops]);
?>
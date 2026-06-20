<?php
header('Content-Type: text/html; charset=utf-8');
echo "<h2>CSV File Debugger</h2>";

$csv_file2 = 'crop_production.csv';

if (file_exists($csv_file2)) {
    echo "<h3>First 10 rows of crop_production.csv:</h3>";
    echo "<table border='1' cellpadding='5'>";
    
    $handle = fopen($csv_file2, 'r');
    if ($handle !== false) {
        $row_count = 0;
        while (($data = fgetcsv($handle)) !== false && $row_count < 10) {
            echo "<tr>";
            foreach ($data as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
            $row_count++;
        }
        fclose($handle);
    }
    echo "</table>";
    
    // Show unique states
    echo "<h3>Unique States in dataset:</h3>";
    $handle = fopen($csv_file2, 'r');
    if ($handle !== false) {
        $header = fgetcsv($handle);
        $state_index = array_search('State_Name', $header);
        $states = [];
        
        while (($data = fgetcsv($handle)) !== false) {
            if (isset($data[$state_index]) && !empty($data[$state_index])) {
                $states[$data[$state_index]] = true;
            }
        }
        fclose($handle);
        
        echo "<ul>";
        foreach (array_keys($states) as $state) {
            echo "<li>" . htmlspecialchars($state) . "</li>";
        }
        echo "</ul>";
    }
    
    // Show districts for a sample state (Karnataka)
    echo "<h3>Districts in Karnataka (first 20):</h3>";
    $handle = fopen($csv_file2, 'r');
    if ($handle !== false) {
        $header = fgetcsv($handle);
        $state_index = array_search('State_Name', $header);
        $district_index = array_search('District_Name', $header);
        $districts = [];
        
        while (($data = fgetcsv($handle)) !== false) {
            if (isset($data[$state_index]) && $data[$state_index] == 'Karnataka') {
                if (isset($data[$district_index]) && !empty($data[$district_index])) {
                    $districts[$data[$district_index]] = true;
                }
            }
        }
        fclose($handle);
        
        echo "<ul>";
        $count = 0;
        foreach (array_keys($districts) as $district) {
            echo "<li>" . htmlspecialchars($district) . "</li>";
            $count++;
            if ($count >= 20) break;
        }
        echo "</ul>";
    }
    
} else {
    echo "<p style='color:red'>crop_production.csv not found!</p>";
}

// Also check Karnataka file
echo "<h3>Checking Karnataka CSV file:</h3>";
$csv_file1 = 'crop_production_karnataka.csv';
if (file_exists($csv_file1)) {
    echo "<p>File exists</p>";
    $handle = fopen($csv_file1, 'r');
    if ($handle !== false) {
        $header = fgetcsv($handle);
        echo "<p>Headers: " . implode(', ', $header) . "</p>";
        fclose($handle);
    }
} else {
    echo "<p style='color:red'>crop_production_karnataka.csv not found!</p>";
}
?>
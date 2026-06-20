<?php
// OPTIMIZED DUAL API FETCHER - Handles large data efficiently
include '../../sql.php';

// Increase memory and time limits for this script only
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);
ini_set('max_input_vars', 5000);

echo "<div style='font-family: monospace; padding: 10px; background: #1e1e1e; color: #d4d4d4; border-radius: 10px;'>";
echo "<h3 style='color: #4caf50;'>🔄 Fetching Data from BOTH Government APIs (Optimized)</h3>";

$apiKey = "579b464db66ec23bdd000001194c3a6defe34d6574ca5970f70e62e5";

// API 1: Current Daily Prices (Mandi)
$api1_url = "https://api.data.gov.in/resource/9ef84268-d588-465a-a308-a864a43d0070";
// API 2: Variety-wise Historical Data
$api2_url = "https://api.data.gov.in/resource/35985678-0d79-46b4-9ed6-6f13308a1d24";

$totalNew = 0;
$totalUpdated = 0;
$totalSkipped = 0;

// States that have data
$states = ['Maharashtra', 'Gujarat', 'Karnataka', 'Uttar Pradesh', 'Punjab', 'Haryana', 
           'Rajasthan', 'Madhya Pradesh', 'West Bengal', 'Tamil Nadu', 'Telangana', 
           'Andhra Pradesh', 'Kerala', 'Odisha', 'Assam'];

echo "<br>📡 <strong>API 1: Current Daily Prices (Mandi)</strong><br>";
echo "Fetching real-time market prices...<br><br>";

// Process API 1 (Current Prices)
foreach ($states as $state) {
    echo "📍 Processing: $state (Current Prices)... ";
    
    $url = $api1_url . "?api-key=" . $apiKey . "&format=json&filters[state.keyword]=" . urlencode($state) . "&limit=200";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data['records']) && count($data['records']) > 0) {
            $recordCount = 0;
            $batchNew = 0;
            $batchUpdate = 0;
            
            // Process in smaller batches to avoid memory issues
            $records = $data['records'];
            foreach ($records as $record) {
                $result = savePriceRecordOptimized($conn, $record, 'current_api');
                if ($result === 'new') {
                    $batchNew++;
                    $totalNew++;
                    $recordCount++;
                } elseif ($result === 'update') {
                    $batchUpdate++;
                    $totalUpdated++;
                    $recordCount++;
                } elseif ($result === 'skip') {
                    $totalSkipped++;
                }
            }
            echo "✅ Got $recordCount records (New: $batchNew, Updated: $batchUpdate)<br>";
        } else {
            echo "⚠️ No records<br>";
        }
    } else {
        echo "❌ API error (HTTP $http_code)<br>";
    }
    flush();
    sleep(0.3); // Small delay to prevent overwhelming
}

echo "<br>📡 <strong>API 2: Variety-wise Historical Data</strong><br>";
echo "Fetching additional market data...<br><br>";

// Process API 2 (Variety Data) - Only for states that had variety data
$varietyStates = ['West Bengal', 'Telangana'];
foreach ($varietyStates as $state) {
    echo "📍 Processing: $state (Variety Data)... ";
    
    $url = $api2_url . "?api-key=" . $apiKey . "&format=json&filters[State]=" . urlencode($state) . "&limit=200";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data['records']) && count($data['records']) > 0) {
            $recordCount = 0;
            foreach ($data['records'] as $record) {
                $result = savePriceRecordOptimized($conn, $record, 'variety_api');
                if ($result === 'new' || $result === 'update') {
                    $recordCount++;
                    if ($result === 'new') $totalNew++;
                    elseif ($result === 'update') $totalUpdated++;
                }
            }
            echo "✅ Got $recordCount records<br>";
        } else {
            echo "⚠️ No records<br>";
        }
    } else {
        echo "❌ API error (HTTP $http_code)<br>";
    }
    flush();
    sleep(0.3);
}

// Clean up old history (keep only 7 days)
$deleted = mysqli_query($conn, "DELETE FROM price_history WHERE price_date < DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$deletedCount = mysqli_affected_rows($conn);

// Get final stats
$totalInDb = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM current_prices"));
$uniqueStates = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT state FROM current_prices"));
$uniqueCommodities = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT commodity FROM current_prices"));
$uniqueMarkets = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT market_name FROM current_prices"));

echo "<br><hr>";
echo "<div style='background: #2e7d32; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
echo "🎉 <strong>DUAL API FETCH COMPLETED!</strong><br>";
echo "✅ New records added: " . number_format($totalNew) . "<br>";
echo "🔄 Records updated: " . number_format($totalUpdated) . "<br>";
echo "⏭️ Duplicates skipped: " . number_format($totalSkipped) . "<br>";
echo "🗑️ Old history cleaned up: " . number_format($deletedCount) . " records<br>";
echo "📋 Total records in database: " . number_format($totalInDb['total']) . "<br>";
echo "📍 Unique States: $uniqueStates | 🥬 Commodities: $uniqueCommodities | 🏪 Markets: $uniqueMarkets<br>";
echo "</div>";

// Show sample data
echo "<br><details>";
echo "<summary style='cursor:pointer; color:#64b5f6;'>📊 View Sample Data</summary>";
echo "<div style='margin-top:10px;'>";
$sample = mysqli_query($conn, "SELECT state, market_name, commodity, modal_price, updated_at FROM current_prices LIMIT 20");
echo "<table style='width:100%; border-collapse:collapse;'>";
echo "<tr style='background:#333;'><th>State</th><th>Market</th><th>Commodity</th><th>Price (₹)</th><th>Updated</th></tr>";
while ($row = mysqli_fetch_assoc($sample)) {
    echo "<tr>";
    echo "<td>" . $row['state'] . "</td>";
    echo "<td>" . substr($row['market_name'], 0, 20) . "</td>";
    echo "<td>" . $row['commodity'] . "</td>";
    echo "<td style='color:#4caf50; font-weight:bold;'>₹" . number_format($row['modal_price']) . "</td>";
    echo "<td>" . date('d M H:i', strtotime($row['updated_at'])) . "</td>";
    echo "</tr>";
}
echo "</table></div></details>";

echo "</div>";

// Optimized save function
function savePriceRecordOptimized($conn, $record, $source) {
    // Handle different API formats
    if ($source == 'current_api') {
        $state = mysqli_real_escape_string($conn, $record['state'] ?? $record['State'] ?? '');
        $district = mysqli_real_escape_string($conn, $record['district'] ?? $record['District'] ?? '');
        $market = mysqli_real_escape_string($conn, $record['market'] ?? $record['Market'] ?? '');
        $commodity = mysqli_real_escape_string($conn, $record['commodity'] ?? $record['Commodity'] ?? '');
        $min_price = mysqli_real_escape_string($conn, $record['min_price'] ?? $record['Min Price'] ?? 0);
        $max_price = mysqli_real_escape_string($conn, $record['max_price'] ?? $record['Max Price'] ?? 0);
        $modal_price = mysqli_real_escape_string($conn, $record['modal_price'] ?? $record['Modal Price'] ?? 0);
    } else {
        $state = mysqli_real_escape_string($conn, $record['State'] ?? '');
        $district = mysqli_real_escape_string($conn, $record['District'] ?? '');
        $market = mysqli_real_escape_string($conn, $record['Market'] ?? '');
        $commodity = mysqli_real_escape_string($conn, $record['Commodity'] ?? '');
        $min_price = mysqli_real_escape_string($conn, $record['Min_Price'] ?? 0);
        $max_price = mysqli_real_escape_string($conn, $record['Max_Price'] ?? 0);
        $modal_price = mysqli_real_escape_string($conn, $record['Modal_Price'] ?? 0);
    }
    
    // Skip invalid records
    if (empty($state) || empty($commodity) || $modal_price <= 0) {
        return 'skip';
    }
    
    // Convert price if needed (from paise to rupees)
    if ($modal_price > 10000) {
        $modal_price = $modal_price / 100;
        $min_price = $min_price / 100;
        $max_price = $max_price / 100;
    }
    
    // Round to nearest integer
    $modal_price = round($modal_price);
    $min_price = round($min_price);
    $max_price = round($max_price);
    
    $market_name = !empty($market) ? $market : ($district . ' Market');
    
    // Check if exists using simple query
    $check = mysqli_query($conn, "SELECT id, modal_price FROM current_prices 
                                 WHERE state='$state' AND market_name='$market_name' AND commodity='$commodity' LIMIT 1");
    
    if (mysqli_num_rows($check) > 0) {
        $existing = mysqli_fetch_assoc($check);
        if ($existing['modal_price'] != $modal_price) {
            // Save to history
            mysqli_query($conn, "INSERT INTO price_history (state, market_name, commodity, modal_price, price_date) 
                              VALUES ('$state', '$market_name', '$commodity', '{$existing['modal_price']}', CURDATE())");
            
            // Update current
            mysqli_query($conn, "UPDATE current_prices SET 
                              district='$district', min_price='$min_price', max_price='$max_price', 
                              modal_price='$modal_price', updated_at=NOW() 
                              WHERE state='$state' AND market_name='$market_name' AND commodity='$commodity'");
            return 'update';
        }
        return 'skip';
    } else {
        // Insert new
        $query = "INSERT INTO current_prices (state, district, market_name, commodity, min_price, max_price, modal_price, updated_at) 
                 VALUES ('$state', '$district', '$market_name', '$commodity', '$min_price', '$max_price', '$modal_price', NOW())";
        
        if (mysqli_query($conn, $query)) {
            return 'new';
        }
        return 'skip';
    }
}
?>
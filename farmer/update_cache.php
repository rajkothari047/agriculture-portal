<?php
$cacheFile = __DIR__ . '/cache_prices.json';
$apiKey = "579b464db66ec23bdd000001791a6d98070b4bea4ba2b7fa99ececcf";

$allRecords = [];
$limit = 10000;
$offset = 0;

do {
    $apiUrl = "https://api.data.gov.in/resource/9ef84268-d588-465a-a308-a864a43d0070?format=json&limit=$limit&offset=$offset&api-key=$apiKey";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if(!isset($data['records']) || empty($data['records'])) {
        break; // no more records
    }

    $allRecords = array_merge($allRecords, $data['records']);
    $offset += $limit;

    echo "Fetched " . count($data['records']) . " records. Total so far: " . count($allRecords) . "<br>";
    flush();

} while(count($data['records']) == $limit);

// Save to cache
file_put_contents($cacheFile, json_encode($allRecords, JSON_PRETTY_PRINT));
echo "<br>Cache updated successfully! Total records: " . count($allRecords);

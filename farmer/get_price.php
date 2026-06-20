<?php
header('Content-Type: application/json');

$apiKey = "579b464db66ec23bdd000001194c3a6defe34d6574ca5970f70e62e5";

$state = $_GET['state'] ?? '';
$district = $_GET['district'] ?? '';
$commodity = $_GET['commodity'] ?? '';
$variety = $_GET['variety'] ?? '';

if(!$state || !$district || !$commodity){
    echo json_encode(['records'=>[], 'message'=>"Please provide State, District, and Commodity"]);
    exit;
}

$limit = 1000;
$offset = 0;
$allRecords = [];

$filters = [
    'filters[state.keyword]' => $state,
    'filters[district]' => $district,
    'filters[commodity]' => $commodity
];
if($variety) $filters['filters[variety]'] = $variety;

function fetch_api($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// Pagination loop
while(true){
    $query = array_merge([
        'format'=>'json',
        'api-key'=>$GLOBALS['apiKey'],
        'limit'=>$limit,
        'offset'=>$offset
    ], $filters);

    $apiUrl = "https://api.data.gov.in/resource/9ef84268-d588-465a-a308-a864a43d0070?" . http_build_query($query);
    $data = fetch_api($apiUrl);

    if(empty($data['records'])) break;

    $allRecords = array_merge($allRecords, $data['records']);

    if(count($data['records']) < $limit) break;

    $offset += $limit;
}

if(empty($allRecords)){
    echo json_encode(['records'=>[], 'message'=>"No recent price data available for this crop in selected district."]);
    exit;
}

echo json_encode(['records'=>$allRecords]);

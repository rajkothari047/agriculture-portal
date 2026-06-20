<?php
header('Content-Type: application/json');

$apiKey = "579b464db66ec23bdd000001194c3a6defe34d6574ca5970f70e62e5";
$state = $_GET['state'] ?? '';
$districtFilter = $_GET['district'] ?? '';

if(!$state){
    echo json_encode(['districts'=>[], 'commodities'=>[], 'varieties'=>[]]);
    exit;
}

$limit = 1000;
$offset = 0;
$districts = [];
$commodities = [];
$varieties = [];

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
    $query = [
        'format'=>'json',
        'api-key'=>$GLOBALS['apiKey'],
        'limit'=>$limit,
        'offset'=>$offset,
        'filters[state.keyword]'=>$state
    ];

    $apiUrl = "https://api.data.gov.in/resource/9ef84268-d588-465a-a308-a864a43d0070?" . http_build_query($query);
    $data = fetch_api($apiUrl);

    if(empty($data['records'])) break;

    foreach($data['records'] as $r){
        if(!empty($r['district'])) $districts[] = $r['district'];
        if(!empty($districtFilter) && $r['district'] == $districtFilter){
            if(!empty($r['commodity'])) $commodities[] = $r['commodity'];
            if(!empty($r['variety'])) $varieties[] = $r['variety'];
        }
    }

    if(count($data['records']) < $limit) break;
    $offset += $limit;
}

// Remove duplicates
$districts = array_values(array_unique($districts));
sort($districts);

$commodities = array_values(array_unique($commodities));
sort($commodities);

$varieties = array_values(array_unique($varieties));
sort($varieties);

echo json_encode([
    'districts'=>$districts,
    'commodities'=>$commodities,
    'varieties'=>$varieties
]);

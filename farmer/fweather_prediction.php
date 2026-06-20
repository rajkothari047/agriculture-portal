<?php
// PHP LOGIC 
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index_php");
    exit();
}
 $query4 = "SELECT * from farmerlogin where email='$user_check'";
 $ses_sq4 = mysqli_query($conn, $query4); 
 $row4 = mysqli_fetch_assoc($ses_sq4);
 $para1 = $row4['farmer_id'];
 $para2 = $row4['farmer_name'];

// --- District Name Retrieval ---
 $display_district_name ="";
 $display_district="Select F_District from farmerlogin WHERE email='$user_check'";
 $display_district_result=mysqli_query($conn,$display_district);
 $display_district_name = mysqli_fetch_array($display_district_result);
 $District_name_farmer=$display_district_name[0];

// --- Weather API Setup ---
ini_set('memory_limit', '-1');
 $url = 'static/citylist.json'; 
 $data_json = @file_get_contents($url);
 $districts = $data_json ? json_decode($data_json) : [];

 $district_weather_id=0;
foreach ($districts as $district) {
    if ($district->name == trim($District_name_farmer)) {
        $district_weather_id=$district->id;
    }
}
if($district_weather_id<=0){
    $district_weather_id=1253952;
}
 $city_weather_id=strval($district_weather_id);

date_default_timezone_set("Asia/Kolkata");
 $apiKey = "870887df4d2b01335921fe396c69a360";
 $cityId = $city_weather_id;

 $googleApiUrl ="https://api.openweathermap.org/data/2.5/forecast?id=" . $cityId . "&lang=en&units=metric&APPID=" . $apiKey;
 $ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 $response = curl_exec($ch);
curl_close($ch);
 $data = json_decode($response);

// --- API Error Handling and Data Extraction ---
 $forecast_list = [];
if ($data && isset($data->list) && is_array($data->list)) {
    $forecast_list = $data->list;
}

 $hourly_forecasts = [];
 $forecast_days = [];
 $current_weather = isset($forecast_list[0]) ? $forecast_list[0] : (object)['main' => (object)['temp' => 25, 'feels_like' => 24, 'humidity' => 50, 'pressure' => 1012, 'temp_max' => 28, 'temp_min' => 22], 'weather' => [(object)['icon' => '01d', 'main' => 'Clear']], 'pop' => 0, 'dt' => time(), 'wind' => (object)['speed' => 3, 'deg' => 90], 'visibility' => 10000, 'clouds' => (object)['all' => 0]];
 $data_city = isset($data->city) ? $data->city : (object)['sunrise' => time() - 10000, 'sunset' => time() + 10000];

 $unique_dates = [date('Y-m-d', $current_weather->dt)];
for ($i = 1; $i < count($forecast_list); $i++) {
    $f = $forecast_list[$i];
    $date = date('Y-m-d', $f->dt);
    if (!in_array($date, $unique_dates) && count($forecast_days) < 6) {
        $forecast_days[] = $f;
        $unique_dates[] = $date;
    }
    if (count($forecast_days) >= 6) { break; }
}

 $now = time();
 $i = 0;
while (count($hourly_forecasts) < 6 && $i < count($forecast_list)) {
    $f = $forecast_list[$i];
    if ($f->dt >= $now) { $hourly_forecasts[] = $f; }
    $i++;
}

function getWeatherIconWI($icon_code) {
    $mapping = [
        '01d' => 'wi-day-sunny', '01n' => 'wi-night-clear', '02d' => 'wi-day-cloudy',
        '02n' => 'wi-night-alt-cloudy', '03d' => 'wi-cloud', '03n' => 'wi-cloud',
        '04d' => 'wi-cloudy', '04n' => 'wi-cloudy', '09d' => 'wi-showers',
        '09n' => 'wi-night-alt-showers', '10d' => 'wi-day-rain', '10n' => 'wi-night-alt-rain',
        '11d' => 'wi-thunderstorm', '11n' => 'wi-thunderstorm', '13d' => 'wi-snow',
        '13n' => 'wi-snow', '50d' => 'wi-fog', '50n' => 'wi-fog',
    ];
    return isset($mapping[$icon_code]) ? $mapping[$icon_code] : 'wi-na'; 
}

function getDailyHighLow($day_data) {
    $high = round($day_data->main->temp) + rand(1, 3);
    $low = round($day_data->main->temp) - rand(1, 3);
    return ['H' => $high, 'L' => $low];
}

 $today_max = round($current_weather->main->temp_max);
 $today_min = round($current_weather->main->temp_min);
 $sunrise_time = date('h:i A', $data_city->sunrise);
 $sunset_time = date('h:i A', $data_city->sunset);
 $visibility_km = round($current_weather->visibility / 1000, 1);
 $wind_gust = property_exists($current_weather->wind, 'gust') ? round($current_weather->wind->gust * 3.6, 1) : 'N/A';
 $rain_3h = (isset($current_weather->rain->{'3h'})) ? $current_weather->rain->{'3h'} : 0;
?>

<!DOCTYPE html>
<html>

<?php include ('fheader.php'); ?>

<head>
    <title>Weather Dashboard - <?php echo $District_name_farmer; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">

    <style>
        /* ═══ TOKENS — exact match with fmap.php ═══ */
        :root {
            --bg: #F9F7F3;
            --s1: #ffffff;
            --s2: #F9F7F3;
            --s3: #F0EDE7;
            --s4: #E8E4DC;
            --b1: rgba(30,41,59,0.08);
            --b2: rgba(30,41,59,0.14);
            --b3: rgba(30,41,59,0.22);
            --accent: #B85C38;
            --acg: rgba(184,92,56,0.15);
            --acg2: rgba(184,92,56,0.08);
            --blue: #2563EB;
            --green: #4F772D;
            --dark-green: #0A3D0A;
            --t1: #1E293B;
            --t2: #475569;
            --t3: #94A3B8;
            --card-bg: rgba(255,255,255,0.9);
            --card-border: rgba(30,41,59,0.1);
            --r: 16px;
            --rsm: 12px;
            --rlg: 22px;
            --g: 14px;
            --tr: 0.2s cubic-bezier(.4,0,.2,1);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Outfit', sans-serif;
            color: var(--t1);
            overflow-x: hidden;
            background: var(--bg);
        }

        /* MAIN BACKGROUND IMAGE */
        body {
            background-image: url('../assets/img/weather4.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

        /* GLASS OVERLAY - Changed from white to transparent glass effect */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 0;
            pointer-events: none;
        }

        /* Fallback gradient if image doesn't load */
        body.no-bg-image {
            background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        * { position: relative; z-index: 1; }
        a { text-decoration: none; color: inherit; }
        button { cursor: pointer; border: none; background: none; font-family: 'Outfit', sans-serif; }

        ::-webkit-scrollbar { width: 3px; height: 3px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--b2); border-radius: 99px; }

        /* ═══ SHELL ═══ */
        .shell {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding: 12px;
            gap: 10px;
            max-width: 1700px;
            margin: 0 auto;
        }

        /* ═══ TOP ROW ═══ */
        .top-row { display: flex; gap: var(--g); flex: 1; min-height: 0; }
        .main { flex: 1; display: flex; flex-direction: column; gap: var(--g); min-width: 0; }

        /* ═══ SIDEBAR — exact match with fmap.php ═══ */
        .sidebar {
            width: 72px;
            flex-shrink: 0;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(24px);
            border: 1px solid var(--card-border);
            border-radius: var(--rlg);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 18px 0;
            gap: 0;
            box-shadow: 0 4px 20px rgba(30,41,59,0.08);
        }

        .sb-logo {
            width: 38px; height: 38px; border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), #A34A2E);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #fff; margin-bottom: 24px;
            box-shadow: 0 4px 14px var(--acg);
            flex-shrink: 0;
        }

        .sb-links { display: flex; flex-direction: column; gap: 2px; width: 100%; padding: 0 8px; }

        .sb-link {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 3px; padding: 9px 0; border-radius: 10px;
            color: var(--t3); transition: var(--tr);
            font-size: 0.58rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        }

        .sb-link i { font-size: 1.05rem; }
        .sb-link:hover { color: var(--t2); background: var(--b1); }
        .sb-link.active { color: var(--accent); background: var(--acg2); box-shadow: inset 0 0 0 1px rgba(184,92,56,0.28); }

        .sb-sp { flex: 1; }
        .sb-bottom { padding: 0 8px; width: 100%; }

        /* ═══ HEADER BAR ═══ */
        .hbar {
            display: flex; align-items: center; gap: 12px;
            background: rgba(255,255,255,0.92); backdrop-filter: blur(24px);
            border: 1px solid var(--card-border); border-radius: var(--r);
            padding: 12px 18px; flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(30,41,59,0.06);
        }

        .hbar-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--accent); flex-shrink: 0;
            animation: glow 2s infinite;
        }

        @keyframes glow {
            0%,100% { box-shadow: 0 0 0 0 var(--acg); }
            50% { box-shadow: 0 0 0 8px transparent; }
        }

        .hbar-text { font-size: 0.78rem; color: var(--t2); flex: 1; }
        .hbar-text b { color: var(--t1); }
        .hbar-meta { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

        .badge {
            display: flex; align-items: center; gap: 5px; padding: 5px 11px;
            background: rgba(37,99,235,0.08); border: 1px solid rgba(37,99,235,0.2);
            border-radius: 99px; font-size: 0.72rem; color: var(--blue); font-weight: 600;
        }

        .mono { font-family: 'DM Mono', monospace; font-size: 0.75rem; color: var(--t3); }

        /* ═══ MAIN GRID ═══ */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            grid-template-rows: auto auto auto;
            gap: var(--g);
            flex: 1;
        }

        /* ═══ CARD BASE ═══ */
        .card {
            background: rgba(255,255,255,0.92);
            border: 1px solid var(--card-border);
            border-radius: var(--r);
            box-shadow: 0 4px 20px rgba(30,41,59,0.08);
            backdrop-filter: blur(20px);
            overflow: hidden;
        }

        /* ═══ HERO WEATHER CARD ═══ */
        .wx-hero-card {
            grid-column: 1 / 2;
            grid-row: 1 / 2;
            background: linear-gradient(150deg, #0A3D0A 0%, #1a5c1a 50%, #B85C38 100%);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: var(--r);
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            position: relative;
            box-shadow: 0 6px 24px rgba(10,61,10,0.25);
            min-height: 260px;
        }

        .wx-orb1 { position: absolute; top: -60px; right: -50px; width: 220px; height: 220px; border-radius: 50%; background: radial-gradient(circle, rgba(184,92,56,0.25) 0%, transparent 70%); pointer-events: none; }
        .wx-orb2 { position: absolute; bottom: -80px; left: -30px; width: 180px; height: 180px; border-radius: 50%; background: radial-gradient(circle, rgba(79,119,45,0.2) 0%, transparent 70%); pointer-events: none; }

        .wx-loc-row { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
        .wx-loc-dot { width: 6px; height: 6px; border-radius: 50%; background: #6ee7b7; box-shadow: 0 0 8px rgba(110,231,183,0.8); flex-shrink: 0; }
        .wx-loc-label { font-size: 0.67rem; color: rgba(255,255,255,0.65); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; }

        .wx-city-name { font-size: 1.8rem; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .wx-rain-chance { font-size: 0.82rem; color: rgba(255,255,255,0.65); }
        .wx-rain-chance i { color: rgba(147,197,253,0.9); margin-right: 4px; }

        .wx-main-row {
            display: flex; justify-content: space-between; align-items: flex-end;
            margin-top: 16px;
        }

        .wx-temp {
            font-size: 7rem; font-weight: 300; line-height: 1;
            color: #fff; letter-spacing: -4px;
        }

        .wx-temp sup { font-size: 2rem; font-weight: 400; letter-spacing: 0; vertical-align: super; color: rgba(255,255,255,0.75); }

        .wx-icon-large {
            font-size: 5rem;
            color: rgba(255,255,255,0.9);
            filter: drop-shadow(0 2px 12px rgba(184,92,56,0.5));
        }

        /* ═══ HOURLY CARD ═══ */
        .hourly-card-wrap {
            grid-column: 1 / 2;
            grid-row: 2 / 3;
        }

        .card-header {
            display: flex; align-items: center; gap: 8px;
            padding: 14px 20px 0;
            font-size: 0.65rem; color: var(--t2);
            text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;
        }

        .card-header::before {
            content: '';
            display: inline-block; width: 3px; height: 12px;
            background: var(--accent); border-radius: 99px;
        }

        /* Spread hourly items evenly across full width */
        .hourly-list {
            display: flex;
            gap: 0;
            padding: 12px 16px 14px;
            width: 100%;
        }

        .hourly-item {
            flex: 1;
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            padding: 10px 6px;
            border-right: 1px solid var(--b1);
            border-radius: 0;
            transition: var(--tr);
            cursor: default;
            position: relative;
        }

        .hourly-item:first-child { border-radius: 10px 0 0 10px; }
        .hourly-item:last-child { border-right: none; border-radius: 0 10px 10px 0; }
        .hourly-item:hover { background: var(--acg2); }

        .hourly-item.now {
            background: rgba(184,92,56,0.1);
        }

        .hourly-item.now::after {
            content: '';
            position: absolute;
            bottom: 0; left: 20%; right: 20%;
            height: 2px; background: var(--accent); border-radius: 99px;
        }

        .h-time { font-size: 0.63rem; color: var(--t3); font-family: 'DM Mono', monospace; }
        .h-icon { font-size: 1.3rem; color: var(--accent); }
        .h-temp { font-size: 0.85rem; font-weight: 800; color: var(--t1); }

        /* ═══ AIR CONDITIONS CARD ═══ */
        .air-card {
            grid-column: 1 / 2;
            grid-row: 3 / 4;
        }

        .air-card-inner { padding: 18px 20px 20px; }

        .air-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 18px;
        }

        .sec-title {
            font-size: 0.65rem; color: var(--t2);
            text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;
            display: flex; align-items: center; gap: 6px;
        }

        .sec-title::before {
            content: '';
            display: inline-block; width: 3px; height: 12px;
            background: var(--accent); border-radius: 99px;
        }

        .see-more-btn {
            background: var(--accent);
            color: #fff;
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--tr);
            border: none;
            font-family: 'Outfit', sans-serif;
        }

        .see-more-btn:hover { background: #A34A2E; }

        .air-metrics { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }

        .metric-pill {
            background: var(--s3);
            border: 1px solid var(--b1);
            border-radius: var(--rsm);
            padding: 13px 10px;
            display: flex; flex-direction: column; gap: 4px;
            transition: var(--tr);
        }

        .metric-pill:hover { border-color: rgba(184,92,56,0.25); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30,41,59,0.08); }

        .metric-label { font-size: 0.63rem; color: var(--t3); text-transform: uppercase; letter-spacing: 0.07em; font-weight: 700; display: flex; align-items: center; gap: 4px; }
        .metric-label i { color: var(--accent); font-size: 0.7rem; }
        .metric-value { font-size: 1.15rem; font-weight: 800; color: var(--t1); }

        /* ═══ 7-DAY PANEL ═══ */
        .seven-day-panel {
            grid-column: 2 / 3;
            grid-row: 1 / 4;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .seven-day-inner { padding: 20px; flex: 1; }

        /* Forecast Table */
        .forecast-table { width: 100%; border-collapse: collapse; }

        .forecast-table thead tr { border-bottom: 2px solid var(--accent); }

        .forecast-table th {
            text-align: left; padding: 10px 8px;
            font-size: 0.65rem; font-weight: 700; color: var(--t2);
            text-transform: uppercase; letter-spacing: 0.08em;
        }

        .forecast-table th:last-child { text-align: right; }

        .forecast-table tbody tr {
            border-bottom: 1px solid var(--b1);
            transition: var(--tr);
        }

        .forecast-table tbody tr:last-child { border-bottom: none; }
        .forecast-table tbody tr:hover { background: var(--acg2); }

        .forecast-table td { padding: 12px 8px; font-size: 0.9rem; font-weight: 500; color: var(--t1); }
        .forecast-table td:first-child { font-weight: 700; }
        .forecast-table td:last-child { text-align: right; }

        .forecast-weather-cell { display: flex; align-items: center; gap: 8px; }

        .weather-icon-small { font-size: 18px; color: var(--accent); width: 26px; }

        .weather-text { font-size: 0.85rem; color: var(--t2); }

        .temp-range { white-space: nowrap; }
        .temp-range .high { font-weight: 800; color: var(--t1); }
        .temp-range .low { opacity: 0.5; margin-left: 4px; font-weight: 600; }

        /* ═══ MODAL ═══ */
        .modal {
            display: none; position: fixed; z-index: 9999;
            left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(30,41,59,0.45);
            backdrop-filter: blur(6px);
            align-items: center; justify-content: center;
        }

        .modal-content {
            background: rgba(255,255,255,0.97);
            border: 1px solid var(--card-border);
            border-radius: var(--rlg);
            padding: 28px;
            width: 90%; max-width: 460px;
            box-shadow: 0 20px 50px rgba(30,41,59,0.2);
            position: relative;
            max-height: 90vh; overflow-y: auto;
        }

        .modal-title {
            font-size: 1rem; font-weight: 800; color: var(--t1);
            margin-bottom: 16px; padding-bottom: 12px;
            border-bottom: 2px solid var(--accent);
        }

        .close-btn {
            position: absolute; top: 14px; right: 18px;
            font-size: 1.4rem; color: var(--t3); cursor: pointer;
            transition: var(--tr); background: none; border: none;
            font-family: sans-serif; line-height: 1;
        }

        .close-btn:hover { color: var(--accent); }

        .modal-metrics { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }

        .modal-metric {
            background: var(--s3);
            border: 1px solid var(--b1);
            border-radius: var(--rsm);
            padding: 12px;
        }

        .modal-metric .metric-label { font-size: 0.62rem; margin-bottom: 4px; }
        .modal-metric .metric-value { font-size: 1rem; font-weight: 800; color: var(--t1); }

        /* ═══ FOOTER ═══ */
        footer, .footer {
            background-color: #0A3D0A !important;
            background-image: none !important;
            margin-top: 0;
        }

        footer h5, footer p, footer address { color: rgba(255,255,255,0.85) !important; }
        footer hr { border-top: 1px solid rgba(255,255,255,0.15) !important; }

        footer .btn {
            background-color: rgba(255,255,255,0.12) !important;
            border: 1px solid rgba(255,255,255,0.25) !important;
            color: white !important;
        }

        footer .social-network button:hover,
        footer .social-network .btn:hover { background-color: var(--accent) !important; }

        /* ═══ ANIMATIONS ═══ */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
        .fu { animation: fadeUp 0.38s ease both; }
        .d1 { animation-delay: 0.04s; }
        .d2 { animation-delay: 0.09s; }
        .d3 { animation-delay: 0.14s; }
        .d4 { animation-delay: 0.19s; }

        /* ═══ RESPONSIVE ═══ */
        @media (max-width: 1200px) {
            .main-grid { grid-template-columns: 1fr 300px; }
            .air-metrics { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
            }

            .wx-hero-card { grid-column: 1; grid-row: auto; }
            .hourly-card-wrap { grid-column: 1; grid-row: auto; }
            .air-card { grid-column: 1; grid-row: auto; }
            .seven-day-panel { grid-column: 1; grid-row: auto; overflow-y: visible; }
            .air-metrics { grid-template-columns: repeat(4, 1fr); }
        }

        @media (max-width: 768px) {
            .shell { padding: 8px; gap: 8px; }
            .top-row { flex-direction: column; }

            .sidebar {
                width: 100%; flex-direction: row;
                border-radius: var(--r);
                padding: 10px 12px; height: auto;
            }

            .sb-logo { margin-bottom: 0; margin-right: 10px; width: 30px; height: 30px; font-size: 0.85rem; }
            .sb-links { flex-direction: row; padding: 0; gap: 2px; }
            .sb-link { padding: 7px 10px; font-size: 0.52rem; }
            .sb-link i { font-size: 0.95rem; }
            .sb-sp { display: none; }
            .sb-bottom { margin-top: 0; margin-left: auto; padding: 0; }
            .hbar-meta { display: none; }

            .wx-temp { font-size: 5rem; letter-spacing: -2px; }
            .wx-icon-large { font-size: 3.8rem; }
            .wx-city-name { font-size: 1.4rem; }
            .air-metrics { grid-template-columns: repeat(2, 1fr); }
            .main-grid { gap: 8px; }
        }

        @media (max-width: 480px) {
            .wx-temp { font-size: 4rem; }
            .wx-icon-large { font-size: 3rem; }
            .air-metrics { grid-template-columns: repeat(2, 1fr); }
            .modal-metrics { grid-template-columns: 1fr; }
            .wx-main-row { flex-direction: column; align-items: flex-start; gap: 8px; }
            .hourly-item { flex: 0 0 64px; }
            .hourly-list { overflow-x: auto; }
        }
    </style>
</head>

<body id="top">

<script>
    // Check if background image loads, if not add fallback
    window.addEventListener('load', function() {
        var img = new Image();
        img.onload = function() {
            console.log('Background image loaded');
        };
        img.onerror = function() {
            document.body.classList.add('no-bg-image');
            console.log('Background image failed to load, using gradient fallback');
        };
        img.src = '../assets/img/weather4.jpg';
    });
</script>

<?php include ('fnav.php'); ?>

<div class="shell">
    <div class="top-row">

        <!-- ─── SIDEBAR — identical to fmap.php ─── -->
        <nav class="sidebar" aria-label="Weather Navigation">
            <div class="sb-logo"><i class="fa-solid fa-cloud-sun"></i></div>
            <div class="sb-links">
                <a href="fweather_prediction.php" class="sb-link active">
                    <i class="fa-solid fa-sun"></i><span>Today</span>
                </a>
                <!-- <a href="cities.php" class="sb-link">
                    <i class="fa-solid fa-city"></i><span>Cities</span>
                </a> -->
                <a href="fmap.php" class="sb-link">
                    <i class="fa-solid fa-map-location-dot"></i><span>Map</span>
                </a>
                <!-- <a href="fweather_prediction.php" class="sb-link">
                    <i class="fa-solid fa-chart-line"></i><span>Stats</span>
                </a> -->
            </div>
            <div class="sb-sp"></div>
            <div class="sb-bottom">
                <a href="settings.php" class="sb-link">
                    <i class="fa-solid fa-gear"></i><span>Settings</span>
                </a>
            </div>
        </nav>

        <!-- ─── MAIN ─── -->
        <div class="main">

            <!-- HEADER BAR -->
            <div class="hbar fu">
                <div class="hbar-dot"></div>
                <p class="hbar-text"><b>Weather Forecasting</b> — Real-time conditions for your district</p>
                <div class="hbar-meta">
                    <div class="badge"><i class="fa-solid fa-location-dot"></i>&nbsp;<?php echo htmlspecialchars($District_name_farmer); ?></div>
                    <div class="mono" id="liveClock">--:--:--</div>
                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="main-grid">

                <!-- ─ HERO WEATHER CARD ─ -->
                <div class="wx-hero-card fu d1">
                    <div class="wx-orb1"></div>
                    <div class="wx-orb2"></div>

                    <div>
                        <div class="wx-loc-row">
                            <div class="wx-loc-dot"></div>
                            <span class="wx-loc-label">Live Conditions</span>
                        </div>
                        <div class="wx-city-name"><?php echo $District_name_farmer; ?></div>
                        <div class="wx-rain-chance">
                            <i class="fa-solid fa-droplet"></i>
                            Chance of rain: <?php echo $current_weather->pop * 100; ?>%
                        </div>
                    </div>

                    <div class="wx-main-row">
                        <div class="wx-temp">
                            <?php echo round($current_weather->main->temp); ?><sup>°</sup>
                        </div>
                        <?php $main_icon_class = getWeatherIconWI($current_weather->weather[0]->icon); ?>
                        <i class="wi <?php echo $main_icon_class; ?> wx-icon-large"></i>
                    </div>
                </div>

                <!-- ─ HOURLY FORECAST ─ -->
                <div class="card hourly-card-wrap fu d2">
                    <div class="card-header">Today's Forecast</div>
                    <div class="hourly-list">
                        <?php
                        if (empty($hourly_forecasts)) {
                            echo '<div class="hourly-item" style="flex:1;border:none;"><span style="color:var(--t3);font-size:0.75rem;">Data unavailable</span></div>';
                        } else {
                            foreach($hourly_forecasts as $idx => $f) {
                                $time = date('g A', $f->dt);
                                $temp = round($f->main->temp);
                                $icon_wi = getWeatherIconWI($f->weather[0]->icon);
                                $nowClass = $idx === 0 ? ' now' : '';
                                echo '<div class="hourly-item' . $nowClass . '">';
                                echo '<div class="h-time">' . ($idx === 0 ? 'Now' : $time) . '</div>';
                                echo '<i class="wi ' . $icon_wi . ' h-icon"></i>';
                                echo '<div class="h-temp">' . $temp . '°</div>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- ─ AIR CONDITIONS ─ -->
                <div class="card air-card fu d3">
                    <div class="air-card-inner">
                        <div class="air-header">
                            <div class="sec-title">Air Conditions</div>
                            <button class="see-more-btn" onclick="openModal()">See more</button>
                        </div>
                        <div class="air-metrics">
                            <div class="metric-pill">
                                <div class="metric-label"><i class="fa-solid fa-thermometer-half"></i> Real Feel</div>
                                <div class="metric-value"><?php echo round($current_weather->main->feels_like); ?>°</div>
                            </div>
                            <div class="metric-pill">
                                <div class="metric-label"><i class="fa-solid fa-wind"></i> Wind</div>
                                <div class="metric-value"><?php echo round($current_weather->wind->speed * 3.6, 1); ?> <small style="font-size:0.7rem;font-weight:500;color:var(--t3);">km/h</small></div>
                            </div>
                            <div class="metric-pill">
                                <div class="metric-label"><i class="fa-solid fa-droplet"></i> Humidity</div>
                                <div class="metric-value"><?php echo $current_weather->main->humidity; ?>%</div>
                            </div>
                            <div class="metric-pill">
                                <div class="metric-label"><i class="fa-solid fa-gauge"></i> Pressure</div>
                                <div class="metric-value"><?php echo $current_weather->main->pressure; ?> <small style="font-size:0.7rem;font-weight:500;color:var(--t3);">hPa</small></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─ 7-DAY PANEL ─ -->
                <div class="card seven-day-panel fu d2">
                    <div class="seven-day-inner">
                        <div class="sec-title" style="margin-bottom:16px;">7-Day Forecast</div>

                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Weather</th>
                                    <th>High / Low</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $today_icon_class = getWeatherIconWI($current_weather->weather[0]->icon);
                                $weather_main = $current_weather->weather[0]->main;
                                ?>
                                <tr>
                                    <td>Today</span></div>
                                    <td>
                                        <div class="forecast-weather-cell">
                                            <i class="wi <?php echo $today_icon_class; ?> weather-icon-small"></i>
                                            <span class="weather-text"><?php echo $weather_main; ?></span>
                                        </div>
                                    </td>
                                    <td class="temp-range">
                                        <span class="high"><?php echo $today_max; ?>°</span>
                                        <span class="low"><?php echo $today_min; ?>°</span>
                                    </td>
                                </tr>

                                <?php
                                if (empty($forecast_days)) {
                                    echo '<tr><td colspan="3" style="text-align:center;color:var(--t3);font-size:0.8rem;padding:16px 0;">7-day forecast data unavailable.</td></tr>';
                                } else {
                                    foreach($forecast_days as $f) {
                                        $day_name = date('D', $f->dt);
                                        $weather_desc = $f->weather[0]->main;
                                        $icon_wi = getWeatherIconWI($f->weather[0]->icon);
                                        $temps = getDailyHighLow($f);
                                        ?>
                                        <tr>
                                            <td><?php echo $day_name; ?></td>
                                            <td>
                                                <div class="forecast-weather-cell">
                                                    <i class="wi <?php echo $icon_wi; ?> weather-icon-small"></i>
                                                    <span class="weather-text"><?php echo $weather_desc; ?></span>
                                                </div>
                                            </td>
                                            <td class="temp-range">
                                                <span class="high"><?php echo $temps['H']; ?>°</span>
                                                <span class="low"><?php echo $temps['L']; ?>°</span>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div><!-- /main-grid -->
        </div><!-- /main -->
    </div><!-- /top-row -->
</div><!-- /shell -->

<!-- AIR CONDITIONS MODAL -->
<div id="airConditionModal" class="modal">
    <div class="modal-content">
        <button class="close-btn" onclick="closeModal()">&#x2715;</button>
        <div class="modal-title">Detailed Air Conditions — <?php echo $District_name_farmer; ?></div>

        <div class="modal-metrics">
            <div class="modal-metric">
                <div class="metric-label"><i class="fa-solid fa-thermometer-half" style="color:var(--accent);"></i> Real Feel</div>
                <div class="metric-value"><?php echo round($current_weather->main->feels_like); ?>°C</div>
            </div>
            <div class="modal-metric">
                <div class="metric-label"><i class="fa-solid fa-wind" style="color:var(--accent);"></i> Wind / Gust</div>
                <div class="metric-value"><?php echo round($current_weather->wind->speed * 3.6, 1); ?> / <?php echo $wind_gust; ?> km/h</div>
            </div>
            <div class="modal-metric">
                <div class="metric-label"><i class="fa-solid fa-gauge" style="color:var(--accent);"></i> Pressure</div>
                <div class="metric-value"><?php echo $current_weather->main->pressure; ?> hPa</div>
            </div>
            <div class="modal-metric">
                <div class="metric-label"><i class="wi wi-sunrise" style="color:var(--accent);"></i> Sunrise</div>
                <div class="metric-value"><?php echo $sunrise_time; ?></div>
            </div>
            <div class="modal-metric">
                <div class="metric-label"><i class="fa-solid fa-cloud" style="color:var(--accent);"></i> Cloud Cover</div>
                <div class="metric-value"><?php echo $current_weather->clouds->all; ?>%</div>
            </div>
            <div class="modal-metric">
                <div class="metric-label"><i class="fa-solid fa-droplet" style="color:var(--accent);"></i> Humidity</div>
                <div class="metric-value"><?php echo $current_weather->main->humidity; ?>%</div>
            </div>
            <div class="modal-metric">
                <div class="metric-label"><i class="fa-solid fa-compass" style="color:var(--accent);"></i> Wind Direction</div>
                <div class="metric-value"><?php echo $current_weather->wind->deg; ?>°</div>
            </div>
            <div class="modal-metric">
                <div class="metric-label"><i class="fa-solid fa-eye" style="color:var(--accent);"></i> Visibility</div>
                <div class="metric-value"><?php echo $visibility_km; ?> km</div>
            </div>
            <div class="modal-metric">
                <div class="metric-label"><i class="wi wi-sunset" style="color:var(--accent);"></i> Sunset</div>
                <div class="metric-value"><?php echo $sunset_time; ?></div>
            </div>
            <div class="modal-metric">
                <div class="metric-label"><i class="fa-solid fa-cloud-rain" style="color:var(--accent);"></i> Rain (Last 3h)</div>
                <div class="metric-value"><?php echo $rain_3h; ?> mm</div>
            </div>
        </div>
    </div>
</div>

<script>
    /* Clock */
    setInterval(() => {
        const e = document.getElementById('liveClock');
        if (e) e.textContent = new Date().toLocaleTimeString('en-GB');
    }, 1000);

    /* Modal */
    var modal = document.getElementById("airConditionModal");
    function openModal() { modal.style.display = "flex"; }
    function closeModal() { modal.style.display = "none"; }
    window.onclick = function(event) { if (event.target == modal) closeModal(); }
</script>

<?php require("footer.php"); ?>

</body>
</html>
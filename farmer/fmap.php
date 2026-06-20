<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include ('fsession.php');
if (!isset($_SESSION['farmer_login_user'])) { header("location: ../index_php"); exit(); }

$query4 = "SELECT * from farmerlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$farmer_id = $row4['farmer_id'];
$farmer_name = $row4['farmer_name'];

$display_district = "SELECT F_District FROM farmerlogin WHERE email='$user_check'";
$display_district_result = mysqli_query($conn, $display_district);
$district_row = mysqli_fetch_array($display_district_result);
$default_district = $district_row[0];

$apiKey = "870887df4d2b01335921fe396c69a360";

function getWeatherByCoords($lat, $lon, $apiKey) {
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&units=metric&appid={$apiKey}";
    $ch = curl_init(); curl_setopt_array($ch,[CURLOPT_HEADER=>0,CURLOPT_RETURNTRANSFER=>1,CURLOPT_URL=>$url,CURLOPT_FOLLOWLOCATION=>1,CURLOPT_SSL_VERIFYPEER=>false]);
    $r=curl_exec($ch); curl_close($ch); return json_decode($r);
}
function getForecastByCoords($lat,$lon,$apiKey) {
    $url="https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&units=metric&cnt=40&appid={$apiKey}";
    $ch=curl_init(); curl_setopt_array($ch,[CURLOPT_HEADER=>0,CURLOPT_RETURNTRANSFER=>1,CURLOPT_URL=>$url,CURLOPT_FOLLOWLOCATION=>1,CURLOPT_SSL_VERIFYPEER=>false]);
    $r=curl_exec($ch); curl_close($ch); return json_decode($r);
}
function wiIcon($c){$m=['01d'=>'wi-day-sunny','01n'=>'wi-night-clear','02d'=>'wi-day-cloudy','02n'=>'wi-night-alt-cloudy','03d'=>'wi-cloud','03n'=>'wi-cloud','04d'=>'wi-cloudy','04n'=>'wi-cloudy','09d'=>'wi-showers','09n'=>'wi-night-alt-showers','10d'=>'wi-day-rain','10n'=>'wi-night-alt-rain','11d'=>'wi-thunderstorm','11n'=>'wi-thunderstorm','13d'=>'wi-snow','13n'=>'wi-snow','50d'=>'wi-fog','50n'=>'wi-fog'];return $m[$c]??'wi-day-sunny';}
function windDir($d){$a=['N','NE','E','SE','S','SW','W','NW'];return $a[round($d/45)%8];}
function dailyFx($fd,$max=7){$o=[];$ds=[];if($fd&&isset($fd->list))foreach($fd->list as $it){$d=date('Y-m-d',$it->dt);if(!in_array($d,$ds)&&count($o)<$max){$o[]=$it;$ds[]=$d;}}return $o;}

$dlat=20.5937; $dlon=78.9629;
$cw=getWeatherByCoords($dlat,$dlon,$apiKey);
$fd=getForecastByCoords($dlat,$dlon,$apiKey);
$daily=dailyFx($fd,7);

$c_labels=[]; $c_high=[]; $c_low=[]; $c_rain=[]; $c_hum=[];
if($cw&&isset($cw->cod)&&$cw->cod==200){
    $c_labels[]='Today';$c_high[]=round($cw->main->temp_max);$c_low[]=round($cw->main->temp_min);
    $c_rain[]=isset($cw->rain->{'1h'})?$cw->rain->{'1h'}:0;$c_hum[]=$cw->main->humidity;
}
foreach($daily as $d){if(count($c_labels)>=7)break;$c_labels[]=date('D',$d->dt);$c_high[]=round($d->main->temp_max);$c_low[]=round($d->main->temp_min);$c_rain[]=isset($d->rain->{'3h'})?round($d->rain->{'3h'},1):0;$c_hum[]=$d->main->humidity;}
$hh_labels=[]; $hh_temps=[]; $hh_icons=[];
if($fd&&isset($fd->list)){foreach(array_slice($fd->list,0,8)as $h){$hh_labels[]=date('H:i',$h->dt);$hh_temps[]=round($h->main->temp);$hh_icons[]=wiIcon($h->weather[0]->icon);}}
$dayIcons=[];
if($cw&&$cw->cod==200){$dayIcons[]=wiIcon($cw->weather[0]->icon);foreach($daily as $d){if(count($dayIcons)<7)$dayIcons[]=wiIcon($d->weather[0]->icon);}}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('fheader.php'); ?>
<head>
<title>WeatherMap — <?php echo htmlspecialchars($default_district); ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
/* ═══ TOKENS — theme from fweather_prediction.php ═══ */
:root{
  /* Core palette from fweather_prediction */
  --bg:#F9F7F3;
  --s1:#ffffff;
  --s2:#F9F7F3;
  --s3:#F0EDE7;
  --s4:#E8E4DC;
  --b1:rgba(30,41,59,0.08);
  --b2:rgba(30,41,59,0.14);
  --b3:rgba(30,41,59,0.22);
  --accent:#B85C38;         /* terracotta */
  --acg:rgba(184,92,56,0.15);
  --acg2:rgba(184,92,56,0.08);
  --blue:#2563EB;
  --sky:#0ea5e9;
  --green:#4F772D;           /* secondary-green from theme */
  --dark-green:#0A3D0A;      /* primary-dark from theme */
  --violet:#7C3AED;
  --pink:#ec4899;
  --t1:#1E293B;              /* color-text-dark */
  --t2:#475569;
  --t3:#94A3B8;
  --card-bg:rgba(255,255,255,0.9);
  --card-border:rgba(30,41,59,0.1);
  --r:16px;
  --rsm:12px;
  --rlg:22px;
  --g:14px;
  --tr:0.2s cubic-bezier(.4,0,.2,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'Outfit',sans-serif;color:var(--t1);overflow-x:hidden;background:var(--bg);}

/* MAIN BACKGROUND IMAGE - Added from first code */
body {
    background-image: url('../assets/img/weather4.jpg');
    background-size: cover;
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
}

/* GLASS OVERLAY - Changed to match first code (transparent glass effect) */
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

/* Fallback gradient if image doesn't load - Added from first code */
body.no-bg-image {
    background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

*{position:relative;z-index:1;}
a{text-decoration:none;color:inherit;}
button{cursor:pointer;border:none;background:none;font-family:'Outfit',sans-serif;}
::-webkit-scrollbar{width:3px;height:3px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:var(--b2);border-radius:99px;}

/* ═══ SHELL ═════════════════════════════════ */
.shell{
  display:flex;flex-direction:column;
  min-height:100vh;
  padding:12px;gap:10px;
  max-width:1700px;margin:0 auto;
}

/* ═══ SIDEBAR ════════════════════════════════ */
.sidebar{
  width:72px;flex-shrink:0;
  background:rgba(255,255,255,0.92);
  backdrop-filter:blur(24px);
  border:1px solid var(--card-border);
  border-radius:var(--rlg);
  display:flex;flex-direction:column;align-items:center;
  padding:18px 0;gap:0;
  box-shadow:0 4px 20px rgba(30,41,59,0.08);
}
.sb-logo{
  width:38px;height:38px;border-radius:12px;
  background:linear-gradient(135deg,var(--accent),#A34A2E);
  display:flex;align-items:center;justify-content:center;
  font-size:1rem;color:#fff;margin-bottom:24px;
  box-shadow:0 4px 14px var(--acg);
  flex-shrink:0;
}
.sb-links{display:flex;flex-direction:column;gap:2px;width:100%;padding:0 8px;}
.sb-link{
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:3px;padding:9px 0;border-radius:10px;
  color:var(--t3);transition:var(--tr);
  font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;
}
.sb-link i{font-size:1.05rem;}
.sb-link:hover{color:var(--t2);background:var(--b1);}
.sb-link.active{color:var(--accent);background:var(--acg2);box-shadow:inset 0 0 0 1px rgba(184,92,56,0.28);}
.sb-sp{flex:1;}
.sb-bottom{padding:0 8px;width:100%;}

/* ═══ TOP ROW ════════════════════════════════ */
.top-row{display:flex;gap:var(--g);flex:1;min-height:0;}
.main{flex:1;display:flex;flex-direction:column;gap:var(--g);min-width:0;}

/* ═══ HEADER ════════════════════════════════ */
.hbar{
  display:flex;align-items:center;gap:12px;
  background:rgba(255,255,255,0.92);backdrop-filter:blur(24px);
  border:1px solid var(--card-border);border-radius:var(--r);
  padding:12px 18px;flex-shrink:0;
  box-shadow:0 2px 10px rgba(30,41,59,0.06);
}
.hbar-dot{width:7px;height:7px;border-radius:50%;background:var(--accent);flex-shrink:0;animation:glow 2s infinite;}
@keyframes glow{0%,100%{box-shadow:0 0 0 0 var(--acg)}50%{box-shadow:0 0 0 8px transparent}}
.hbar-text{font-size:0.78rem;color:var(--t2);flex:1;}
.hbar-text b{color:var(--t1);}
.hbar-meta{display:flex;align-items:center;gap:10px;flex-shrink:0;}
.badge{
  display:flex;align-items:center;gap:5px;padding:5px 11px;
  background:rgba(37,99,235,0.08);border:1px solid rgba(37,99,235,0.2);
  border-radius:99px;font-size:0.72rem;color:var(--blue);font-weight:600;
}
.mono{font-family:'DM Mono',monospace;font-size:0.75rem;color:var(--t3);}

/* ═══ MID ROW ════════════════════════════════ */
.mid{display:grid;grid-template-columns:1fr 340px;gap:var(--g);align-items:stretch;}

/* ─ MAP ─ */
.map-card{
  background:rgba(255,255,255,0.92);border:1px solid var(--card-border);border-radius:var(--r);
  overflow:hidden;display:flex;flex-direction:column;
  box-shadow:0 4px 20px rgba(30,41,59,0.08);
}
.map-bar{
  display:flex;align-items:center;gap:10px;padding:10px 14px;flex-shrink:0;
  background:rgba(249,247,243,0.8);border-bottom:1px solid var(--card-border);
}
.mac-dots{display:flex;gap:5px;}
.mac-dot{width:9px;height:9px;border-radius:50%;}
.map-bar-title{font-size:0.72rem;color:var(--t2);font-weight:600;flex:1;margin-left:4px;}
.tile-btns{display:flex;gap:4px;}
.tile-btn{
  padding:4px 9px;border-radius:7px;font-size:0.68rem;font-weight:700;
  color:var(--t3);background:var(--b1);border:1px solid var(--b2);
  transition:var(--tr);cursor:pointer;
}
.tile-btn.on{color:var(--accent);background:var(--acg2);border-color:rgba(184,92,56,0.35);}
#map{flex:1;width:100%;min-height:460px;}

/* ─ WEATHER PANEL — full height to match map ─ */
.wx-panel{
  display:flex;flex-direction:column;gap:10px;
  /* Stretch to fill the grid row height, same as map card */
  align-self:stretch;
  overflow-y:auto;
}
.wx-panel::-webkit-scrollbar{width:3px;}
.wx-panel::-webkit-scrollbar-thumb{background:var(--b2);border-radius:99px;}

/* Hero */
.wx-hero{
  background:linear-gradient(150deg,#0A3D0A 0%,#1a5c1a 50%,#B85C38 100%);
  border:1px solid rgba(255,255,255,0.15);border-radius:var(--r);
  padding:22px;overflow:hidden;position:relative;flex-shrink:0;
  box-shadow:0 6px 24px rgba(10,61,10,0.25);
}
.wx-orb1{position:absolute;top:-60px;right:-50px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(184,92,56,0.25) 0%,transparent 70%);pointer-events:none;}
.wx-orb2{position:absolute;bottom:-80px;left:-30px;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(79,119,45,0.2) 0%,transparent 70%);pointer-events:none;}
.wx-loc-row{display:flex;align-items:center;gap:6px;margin-bottom:3px;}
.wx-loc-dot{width:6px;height:6px;border-radius:50%;background:#6ee7b7;box-shadow:0 0 8px rgba(110,231,183,0.8);flex-shrink:0;}
.wx-loc-label{font-size:0.67rem;color:rgba(255,255,255,0.65);text-transform:uppercase;letter-spacing:0.1em;}
.wx-city{font-size:1.25rem;font-weight:800;color:#fff;margin-bottom:14px;line-height:1.2;}
.wx-main-row{display:flex;align-items:flex-start;justify-content:space-between;}
.wx-temp-block{}
.wx-temp{font-size:4.2rem;font-weight:900;line-height:1;color:#fff;}
.wx-temp sup{font-size:1.4rem;font-weight:400;vertical-align:super;color:rgba(255,255,255,0.8);}
.wx-desc{font-size:0.83rem;color:rgba(255,255,255,0.8);text-transform:capitalize;margin-top:5px;}
.wx-feels{font-size:0.73rem;color:rgba(255,255,255,0.6);margin-top:2px;}
.wx-icon-side i{font-size:3.8rem;color:rgba(255,255,255,0.9);filter:drop-shadow(0 2px 8px rgba(184,92,56,0.4));}
.wx-meta-strip{
  display:flex;gap:0;margin-top:14px;padding-top:14px;
  border-top:1px solid rgba(255,255,255,0.15);
}
.wx-mm{display:flex;align-items:center;gap:5px;font-size:0.78rem;color:rgba(255,255,255,0.75);padding-right:14px;margin-right:14px;border-right:1px solid rgba(255,255,255,0.15);}
.wx-mm:last-child{border-right:none;margin-right:0;}
.wx-mm i{font-size:0.8rem;}
.wx-mm span{font-weight:700;color:#fff;}

/* Quick pills 4-col */
.wx-pills{display:grid;grid-template-columns:repeat(4,1fr);gap:7px;flex-shrink:0;}
.pill{
  background:rgba(255,255,255,0.88);border:1px solid var(--card-border);
  border-radius:var(--rsm);padding:11px 8px;text-align:center;
  transition:var(--tr);backdrop-filter:blur(10px);
  box-shadow:0 2px 8px rgba(30,41,59,0.05);
}
.pill:hover{border-color:rgba(184,92,56,0.3);transform:translateY(-1px);box-shadow:0 4px 12px rgba(30,41,59,0.1);}
.pill-em{font-size:1.05rem;margin-bottom:4px;}
.pill-v{font-size:0.95rem;font-weight:800;color:var(--t1);line-height:1.2;}
.pill-l{font-size:0.58rem;color:var(--t3);text-transform:uppercase;letter-spacing:0.07em;margin-top:2px;}

/* Detail grid 2x2 */
.wx-details{display:grid;grid-template-columns:1fr 1fr;gap:7px;flex-shrink:0;}
.det{
  background:rgba(255,255,255,0.88);border:1px solid var(--card-border);
  border-radius:var(--rsm);padding:13px;backdrop-filter:blur(10px);
  transition:var(--tr);box-shadow:0 2px 8px rgba(30,41,59,0.05);
}
.det:hover{border-color:rgba(184,92,56,0.25);box-shadow:0 4px 12px rgba(30,41,59,0.1);}
.det-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}
.det-label{font-size:0.65rem;color:var(--t3);text-transform:uppercase;letter-spacing:0.09em;font-weight:700;}
.det-icon{font-size:0.8rem;color:var(--accent);}
.det-val{font-size:1.3rem;font-weight:800;color:var(--t1);line-height:1;}
.det-sub{font-size:0.67rem;color:var(--t3);margin-top:4px;}
.gbar{height:4px;background:rgba(30,41,59,0.08);border-radius:99px;margin-top:9px;overflow:hidden;}
.gfill{height:100%;border-radius:99px;transition:width 0.9s cubic-bezier(.4,0,.2,1);}

/* Sun card */
.wx-sun{
  background:rgba(255,255,255,0.88);border:1px solid var(--card-border);
  border-radius:var(--rsm);padding:14px;flex-shrink:0;
  backdrop-filter:blur(10px);box-shadow:0 2px 8px rgba(30,41,59,0.05);
}
.wx-sun-hdr{font-size:0.65rem;color:var(--t2);text-transform:uppercase;letter-spacing:0.09em;font-weight:700;margin-bottom:10px;}
.arc-wrap{position:relative;height:52px;margin-bottom:8px;}
.arc-wrap svg{width:100%;height:100%;}
.sun-times{display:flex;justify-content:space-between;}
.stime{text-align:center;}
.stime span{display:block;font-size:0.62rem;color:var(--t3);margin-bottom:2px;}
.stime strong{font-size:0.82rem;font-weight:700;font-family:'DM Mono',monospace;color:var(--t1);}

/* Loading */
.wx-load{display:none;flex-direction:column;align-items:center;justify-content:center;padding:32px;gap:10px;}
.wx-load.show{display:flex;}
.ring{width:32px;height:32px;border-radius:50%;border:2px solid rgba(184,92,56,0.15);border-top-color:var(--accent);animation:spin .7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg)}}
.wx-load p{font-size:0.75rem;color:var(--t3);}

/* ═══ HOURLY STRIP — full width, evenly spread ═══ */
.hourly{
  background:rgba(255,255,255,0.92);border:1px solid var(--card-border);
  border-radius:var(--r);padding:16px 20px;flex-shrink:0;
  backdrop-filter:blur(20px);
  box-shadow:0 4px 20px rgba(30,41,59,0.08);
}
.sec-title{font-size:0.65rem;color:var(--t2);text-transform:uppercase;letter-spacing:0.1em;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:6px;}
.sec-title::before{content:'';display:inline-block;width:3px;height:12px;background:var(--accent);border-radius:99px;}

/* Spread items evenly across full width — no scrolling on desktop */
.h-list{
  display:flex;
  gap:0;
  width:100%;
}
.h-item{
  flex:1;
  display:flex;flex-direction:column;align-items:center;gap:5px;
  background:transparent;
  border-right:1px solid var(--b1);
  padding:10px 8px;
  transition:var(--tr);
  border-radius:0;
}
.h-item:last-child{border-right:none;}
.h-item:first-child{border-radius:10px 0 0 10px;}
.h-item:last-child{border-radius:0 10px 10px 0;}
.h-item:hover{background:var(--acg2);}
.h-item.now{background:rgba(184,92,56,0.1);position:relative;}
.h-item.now::after{content:'';position:absolute;bottom:0;left:20%;right:20%;height:2px;background:var(--accent);border-radius:99px;}
.h-time{font-size:0.63rem;color:var(--t3);font-family:'DM Mono',monospace;}
.h-icon{font-size:1.25rem;color:var(--accent);}
.h-temp{font-size:0.82rem;font-weight:800;color:var(--t1);}

/* ═══ FORECAST CARD ══════════════════════════ */
.fc-card{
  background:rgba(255,255,255,0.92);border:1px solid var(--card-border);
  border-radius:var(--r);padding:16px 20px;flex-shrink:0;
  backdrop-filter:blur(20px);
  box-shadow:0 4px 20px rgba(30,41,59,0.08);
}
.fc-top{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.fc-tabs{display:flex;gap:4px;margin-left:auto;}
.fc-tab{
  padding:5px 12px;border-radius:8px;font-size:0.68rem;font-weight:700;
  color:var(--t3);background:var(--b1);border:1px solid var(--b2);
  cursor:pointer;transition:var(--tr);
}
.fc-tab.on{color:var(--accent);background:var(--acg2);border-color:rgba(184,92,56,0.35);}

/* Day strip */
.day-strip{display:flex;gap:6px;margin-bottom:14px;overflow-x:auto;}
.day-strip::-webkit-scrollbar{display:none;}
.ditem{
  flex:1;min-width:0;display:flex;flex-direction:column;align-items:center;gap:3px;
  padding:9px 5px;border-radius:10px;border:1px solid var(--b1);
  background:rgba(249,247,243,0.8);cursor:pointer;transition:var(--tr);position:relative;overflow:hidden;
}
.ditem.today{border-color:rgba(184,92,56,0.45);background:var(--acg2);}
.ditem:hover:not(.today){border-color:rgba(184,92,56,0.2);background:rgba(184,92,56,0.04);}
.d-lbl{font-size:0.63rem;color:var(--t3);font-weight:700;text-transform:uppercase;}
.d-ico{font-size:1.15rem;color:var(--accent);}
.d-hi{font-size:0.85rem;font-weight:900;color:var(--t1);}
.d-lo{font-size:0.68rem;color:var(--t3);font-weight:600;}
.d-rain{position:absolute;bottom:0;left:0;right:0;height:2px;background:rgba(37,99,235,0.45);}

/* Chart wrap */
.chart-wrap{position:relative;width:100%;height:155px;}

/* ═══ LEAFLET OVERRIDES ══════════════════════ */
.leaflet-container{background:#F9F7F3 !important;font-family:'Outfit',sans-serif !important;}
.leaflet-popup-content-wrapper{background:rgba(255,255,255,0.97) !important;border:1px solid var(--card-border) !important;border-radius:14px !important;backdrop-filter:blur(20px);color:var(--t1) !important;box-shadow:0 12px 40px rgba(30,41,59,0.15) !important;}
.leaflet-popup-tip{background:rgba(255,255,255,0.97) !important;}
.leaflet-popup-close-button{color:var(--t3) !important;}
.leaflet-control-zoom a{background:rgba(255,255,255,0.95) !important;border-color:var(--b2) !important;color:var(--t1) !important;}
.leaflet-bar{border:none !important;border-radius:10px !important;overflow:hidden;box-shadow:0 2px 10px rgba(30,41,59,0.12) !important;}

/* ═══ FOOTER ══════════════════════════════════ */
footer,.footer{background-color:#0A3D0A !important;background-image:none !important;margin-top:0;}
footer h5,footer p,footer address{color:rgba(255,255,255,0.85) !important;}
footer hr{border-top:1px solid rgba(255,255,255,0.15) !important;}

/* ═══ ANIMATIONS ══════════════════════════════ */
@keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.fu{animation:fadeUp 0.38s ease both;}
.d1{animation-delay:0.04s;}.d2{animation-delay:0.08s;}.d3{animation-delay:0.13s;}.d4{animation-delay:0.18s;}

/* ═══ RESPONSIVE ══════════════════════════════ */
@media(max-width:1200px){
  .mid{grid-template-columns:1fr 300px;}
}
@media(max-width:1024px){
  .mid{grid-template-columns:1fr;}
  .wx-panel{max-height:none;flex-direction:row;flex-wrap:wrap;overflow:visible;}
  .wx-hero,.wx-pills,.wx-details,.wx-sun{flex:0 0 100%;}
  .map-card{min-height:380px;}
  #map{min-height:340px;}
  /* On tablet, allow hourly to scroll */
  .h-list{overflow-x:auto;}
  .h-item{flex:0 0 80px;}
}
@media(max-width:768px){
  .shell{padding:8px;gap:8px;}
  .top-row{flex-direction:column;}
  .sidebar{
    width:100%;flex-direction:row;border-radius:var(--r);
    padding:10px 12px;height:auto;
  }
  .sb-logo{margin-bottom:0;margin-right:10px;width:30px;height:30px;font-size:0.85rem;}
  .sb-links{flex-direction:row;padding:0;gap:2px;}
  .sb-link{padding:7px 10px;font-size:0.52rem;}
  .sb-link i{font-size:0.95rem;}
  .sb-sp{display:none;}
  .sb-bottom{margin-top:0;margin-left:auto;padding:0;}
  .hbar-meta{display:none;}
  .wx-pills{grid-template-columns:repeat(2,1fr);}
  #map{min-height:280px;}
  .fc-tabs{display:none;}
  .mid{gap:8px;}
  .h-item{flex:0 0 72px;}
}
@media(max-width:480px){
  .wx-pills{grid-template-columns:repeat(2,1fr);}
  .wx-details{grid-template-columns:1fr;}
  .wx-temp{font-size:3.5rem;}
  .wx-icon-side i{font-size:3rem;}
  .h-item{flex:0 0 64px;}
}
</style>
</head>
<body>

<script>
    // Check if background image loads, if not add fallback - Added from first code
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

<?php include('fnav.php'); ?>

<div class="shell">
  <div class="top-row">

    <!-- ─── SIDEBAR ─── -->
    <nav class="sidebar" aria-label="Weather Navigation">
      <div class="sb-logo"><i class="fa-solid fa-cloud-sun"></i></div>
      <div class="sb-links">
        <a href="fweather_prediction.php" class="sb-link"><i class="fa-solid fa-sun"></i><span>Today</span></a>
        <!-- <a href="cities.php" class="sb-link"><i class="fa-solid fa-city"></i><span>Cities</span></a> -->
        <a href="fmap.php" class="sb-link active"><i class="fa-solid fa-map-location-dot"></i><span>Map</span></a>
        <!-- <a href="fweather_prediction.php" class="sb-link"><i class="fa-solid fa-chart-line"></i><span>Stats</span></a> -->
      </div>
      <div class="sb-sp"></div>
      <div class="sb-bottom">
        <a href="settings.php" class="sb-link"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
      </div>
    </nav>

    <!-- ─── MAIN ─── -->
    <div class="main">

      <!-- HEADER -->
      <div class="hbar fu">
        <div class="hbar-dot"></div>
        <p class="hbar-text"><b>Live Map</b> — Click anywhere to load real-time weather conditions</p>
        <div class="hbar-meta">
          <div class="badge"><i class="fa-solid fa-location-dot"></i>&nbsp;<?php echo htmlspecialchars($default_district); ?></div>
          <div class="mono" id="liveClock">--:--:--</div>
        </div>
      </div>

      <!-- MID: map + wx panel -->
      <div class="mid">

        <!-- MAP -->
        <div class="map-card fu d1">
          <div class="map-bar">
            <div class="mac-dots">
              <div class="mac-dot" style="background:#ef4444;"></div>
              <div class="mac-dot" style="background:#f59e0b;"></div>
              <div class="mac-dot" style="background:#22c55e;"></div>
            </div>
            <span class="map-bar-title">Interactive Map — Click to explore weather</span>
            <div class="tile-btns">
              <button class="tile-btn on" onclick="setTile('osm',this)">Streets</button>
              <button class="tile-btn" onclick="setTile('sat',this)">Satellite</button>
              <button class="tile-btn" onclick="setTile('dark',this)">Dark</button>
            </div>
          </div>
          <div id="map"></div>
        </div>

        <!-- WEATHER PANEL — stretches to map height via align-self:stretch on .mid grid -->
        <div class="wx-panel" id="wxPanel">

          <!-- Hero -->
          <div class="wx-hero fu d2">
            <div class="wx-orb1"></div>
            <div class="wx-orb2"></div>
            <div class="wx-loc-row">
              <div class="wx-loc-dot"></div>
              <span class="wx-loc-label">Live Conditions</span>
            </div>
            <div class="wx-city" id="wxCity"><?php echo htmlspecialchars($default_district); ?></div>
            <div class="wx-main-row">
              <div class="wx-temp-block">
                <div class="wx-temp" id="wxTemp"><?php echo ($cw&&$cw->cod==200)?round($cw->main->temp):'--'; ?><sup>°C</sup></div>
                <div class="wx-desc" id="wxDesc"><?php echo ($cw&&$cw->cod==200)?ucfirst($cw->weather[0]->description):'Select a location'; ?></div>
                <div class="wx-feels" id="wxFeels"><?php echo ($cw&&$cw->cod==200)?'Feels like '.round($cw->main->feels_like).'°C':''; ?></div>
              </div>
              <div class="wx-icon-side">
                <i class="wi <?php echo ($cw&&$cw->cod==200)?wiIcon($cw->weather[0]->icon):'wi-day-sunny'; ?>" id="wxIcon"></i>
              </div>
            </div>
            <div class="wx-meta-strip">
              <div class="wx-mm"><i class="fa-solid fa-arrow-up" style="color:#fca5a5;"></i><span id="wxMax"><?php echo ($cw&&$cw->cod==200)?round($cw->main->temp_max).'°':'--'; ?></span></div>
              <div class="wx-mm"><i class="fa-solid fa-arrow-down" style="color:#93c5fd;"></i><span id="wxMin"><?php echo ($cw&&$cw->cod==200)?round($cw->main->temp_min).'°':'--'; ?></span></div>
              <div class="wx-mm"><i class="fa-solid fa-droplet" style="color:#93c5fd;"></i><span id="wxHumMini"><?php echo ($cw&&$cw->cod==200)?$cw->main->humidity.'%':'--'; ?></span></div>
              <div class="wx-mm" style="border-right:none;margin-right:0;"><i class="fa-solid fa-wind" style="color:#c4b5fd;"></i><span id="wxWindMini"><?php echo ($cw&&$cw->cod==200)?round($cw->wind->speed*3.6).' km/h':'--'; ?></span></div>
            </div>
          </div>

          <!-- Loading -->
          <div class="wx-load" id="wxLoad"><div class="ring"></div><p>Loading weather data…</p></div>

          <!-- Quick pills -->
          <div class="wx-pills fu d2" id="wxPills">
            <?php if($cw&&$cw->cod==200):
              $vis=isset($cw->visibility)?round($cw->visibility/1000,1):'--';
              $uv=round(max(0,12-abs($cw->main->humidity-50)/10));
              $uvColors=[0=>'#16a34a',1=>'#16a34a',2=>'#16a34a',3=>'#d97706',4=>'#d97706',5=>'#d97706',6=>'#ea580c',7=>'#ea580c',8=>'#dc2626',9=>'#dc2626',10=>'#dc2626'];
              $uvC=$uvColors[min(10,$uv)];
            ?>
            <div class="pill"><div class="pill-em">💨</div><div class="pill-v" id="pWind"><?php echo round($cw->wind->speed*3.6); ?></div><div class="pill-l">km/h · <?php echo windDir($cw->wind->deg??0); ?></div></div>
            <div class="pill"><div class="pill-em">💧</div><div class="pill-v" id="pHum"><?php echo $cw->main->humidity; ?>%</div><div class="pill-l">Humidity</div></div>
            <div class="pill"><div class="pill-em">☀️</div><div class="pill-v" id="pUV" style="color:<?php echo $uvC; ?>"><?php echo $uv; ?></div><div class="pill-l" id="pUVl"><?php echo $uv<=2?'Low UV':($uv<=5?'Moderate':($uv<=7?'High':'Very High')); ?></div></div>
            <div class="pill"><div class="pill-em">👁️</div><div class="pill-v" id="pVis"><?php echo $vis; ?></div><div class="pill-l">km Vis.</div></div>
            <?php else: ?>
            <div class="pill"><div class="pill-em">💨</div><div class="pill-v" id="pWind">--</div><div class="pill-l">Wind</div></div>
            <div class="pill"><div class="pill-em">💧</div><div class="pill-v" id="pHum">--</div><div class="pill-l">Humidity</div></div>
            <div class="pill"><div class="pill-em">☀️</div><div class="pill-v" id="pUV">--</div><div class="pill-l" id="pUVl">UV Index</div></div>
            <div class="pill"><div class="pill-em">👁️</div><div class="pill-v" id="pVis">--</div><div class="pill-l">Visibility</div></div>
            <?php endif; ?>
          </div>

          <!-- Detail cards 2x2 -->
          <div class="wx-details fu d3" id="wxDetails">
            <?php if($cw&&$cw->cod==200):
              $pres=$cw->main->pressure;
              $cc=$cw->clouds->all;
              $dp=round($cw->main->temp-((100-$cw->main->humidity)/5));
              $ws=round($cw->wind->speed*3.6,1);
              $gust=isset($cw->wind->gust)?round($cw->wind->gust*3.6).'km/h':'N/A';
            ?>
            <div class="det">
              <div class="det-top"><span class="det-label">Pressure</span><i class="fa-solid fa-gauge det-icon"></i></div>
              <div class="det-val" id="dPres"><?php echo $pres; ?></div>
              <div class="det-sub"><?php echo $pres>1013?'High pressure':'Low pressure'; ?> · hPa</div>
              <div class="gbar"><div class="gfill" id="gPres" style="width:<?php echo min(100,($pres-960)/80*100); ?>%;background:linear-gradient(90deg,#818cf8,#a78bfa);"></div></div>
            </div>
            <div class="det">
              <div class="det-top"><span class="det-label">Cloud Cover</span><i class="fa-solid fa-cloud det-icon"></i></div>
              <div class="det-val" id="dCloud"><?php echo $cc; ?>%</div>
              <div class="det-sub"><?php echo $cc<25?'Clear sky':($cc<70?'Partly cloudy':'Overcast'); ?></div>
              <div class="gbar"><div class="gfill" id="gCloud" style="width:<?php echo $cc; ?>%;background:linear-gradient(90deg,#94a3b8,#64748b);"></div></div>
            </div>
            <div class="det">
              <div class="det-top"><span class="det-label">Dew Point</span><i class="fa-solid fa-droplet det-icon"></i></div>
              <div class="det-val" id="dDew"><?php echo $dp; ?>°C</div>
              <div class="det-sub"><?php echo $dp<10?'Dry':($dp<16?'Comfortable':($dp<21?'Sticky':'Oppressive')); ?></div>
              <div class="gbar"><div class="gfill" id="gDew" style="width:<?php echo min(100,max(0,($dp+10)/50*100)); ?>%;background:linear-gradient(90deg,#38bdf8,#0ea5e9);"></div></div>
            </div>
            <div class="det">
              <div class="det-top"><span class="det-label">Wind</span><i class="fa-solid fa-wind det-icon"></i></div>
              <div class="det-val" id="dWind"><?php echo $ws; ?><small style="font-size:0.6rem;font-weight:400;color:var(--t3);"> km/h</small></div>
              <div class="det-sub" id="dWindSub"><?php echo windDir($cw->wind->deg??0); ?> · Gust <?php echo $gust; ?></div>
              <div class="gbar"><div class="gfill" id="gWind" style="width:<?php echo min(100,$ws/1.5); ?>%;background:linear-gradient(90deg,#4F772D,#86b53a);"></div></div>
            </div>
            <?php else: ?>
            <div class="det"><div class="det-top"><span class="det-label">Pressure</span><i class="fa-solid fa-gauge det-icon"></i></div><div class="det-val" id="dPres">--</div><div class="det-sub">hPa</div><div class="gbar"><div class="gfill" id="gPres" style="width:0%;background:linear-gradient(90deg,#818cf8,#a78bfa);"></div></div></div>
            <div class="det"><div class="det-top"><span class="det-label">Cloud Cover</span><i class="fa-solid fa-cloud det-icon"></i></div><div class="det-val" id="dCloud">--</div><div class="det-sub">--</div><div class="gbar"><div class="gfill" id="gCloud" style="width:0%;background:linear-gradient(90deg,#94a3b8,#64748b);"></div></div></div>
            <div class="det"><div class="det-top"><span class="det-label">Dew Point</span><i class="fa-solid fa-droplet det-icon"></i></div><div class="det-val" id="dDew">--</div><div class="det-sub">--</div><div class="gbar"><div class="gfill" id="gDew" style="width:0%;background:linear-gradient(90deg,#38bdf8,#0ea5e9);"></div></div></div>
            <div class="det"><div class="det-top"><span class="det-label">Wind</span><i class="fa-solid fa-wind det-icon"></i></div><div class="det-val" id="dWind">--</div><div class="det-sub" id="dWindSub">--</div><div class="gbar"><div class="gfill" id="gWind" style="width:0%;background:linear-gradient(90deg,#4F772D,#86b53a);"></div></div></div>
            <?php endif; ?>
          </div>

          <!-- Sun timeline -->
          <div class="wx-sun fu d3">
            <div class="wx-sun-hdr">☀ Sun Timeline</div>
            <?php if($cw&&$cw->cod==200):
              $sr=date('H:i',$cw->sys->sunrise);$ss=date('H:i',$cw->sys->sunset);
              $dlen=$cw->sys->sunset-$cw->sys->sunrise;
              $el=max(0,min($dlen,time()-$cw->sys->sunrise));
              $sp=$dlen>0?$el/$dlen:0.5;
              $t=$sp;
              $bx=(1-$t)*(1-$t)*10+2*(1-$t)*$t*150+$t*$t*290;
              $by=(1-$t)*(1-$t)*52+2*(1-$t)*$t*4+$t*$t*52;
            ?>
            <div class="arc-wrap">
              <svg viewBox="0 0 300 60" preserveAspectRatio="none">
                <defs><linearGradient id="sg" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#0A3D0A"/><stop offset="100%" stop-color="#B85C38"/></linearGradient></defs>
                <path d="M 10 52 Q 150 4 290 52" fill="none" stroke="rgba(30,41,59,0.1)" stroke-width="2" stroke-linecap="round"/>
                <path d="M 10 52 Q 150 4 290 52" fill="none" stroke="url(#sg)" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="<?php echo round($sp*380); ?> 380"/>
                <circle cx="<?php echo round($bx,1); ?>" cy="<?php echo round($by,1); ?>" r="5" fill="#B85C38"/>
                <circle cx="<?php echo round($bx,1); ?>" cy="<?php echo round($by,1); ?>" r="9" fill="rgba(184,92,56,0.2)"/>
              </svg>
            </div>
            <div class="sun-times">
              <div class="stime"><span>Sunrise</span><strong><?php echo $sr; ?></strong></div>
              <div class="stime"><span>Day Length</span><strong><?php echo floor($dlen/3600).'h '.round(($dlen%3600)/60).'m'; ?></strong></div>
              <div class="stime"><span>Sunset</span><strong><?php echo $ss; ?></strong></div>
            </div>
            <?php else: ?>
            <p style="text-align:center;color:var(--t3);font-size:0.75rem;padding:10px 0;">Click map for sun data</p>
            <?php endif; ?>
          </div>

        </div><!-- /wx-panel -->
      </div><!-- /mid -->

      <!-- HOURLY — full width, items spread evenly -->
      <div class="hourly fu d3">
        <div class="sec-title">Hourly Forecast · Next 24 Hours</div>
        <div class="h-list" id="hList">
          <?php if(!empty($hh_labels)):foreach($hh_labels as $i=>$ht): ?>
          <div class="h-item <?php echo $i===0?'now':''; ?>">
            <div class="h-time"><?php echo $i===0?'Now':$ht; ?></div>
            <i class="wi <?php echo $hh_icons[$i]; ?> h-icon"></i>
            <div class="h-temp"><?php echo $hh_temps[$i]; ?>°</div>
          </div>
          <?php endforeach;else: ?>
          <div class="h-item" style="flex:1;border:none;"><span style="color:var(--t3);font-size:0.75rem;">Click map to load hourly data</span></div>
          <?php endif; ?>
        </div>
      </div>

      <!-- 7-DAY FORECAST -->
      <div class="fc-card fu d4">
        <div class="fc-top">
          <div class="sec-title" style="margin-bottom:0;">7-Day Forecast</div>
          <div class="fc-tabs">
            <button class="fc-tab on" onclick="sw('temp',this)">Temperature</button>
            <button class="fc-tab" onclick="sw('hum',this)">Humidity</button>
            <button class="fc-tab" onclick="sw('rain',this)">Rain</button>
          </div>
        </div>
        <!-- Day strip -->
        <div class="day-strip" id="dayStrip">
          <?php foreach($c_labels as $i=>$lbl): ?>
          <div class="ditem <?php echo $i===0?'today':''; ?>">
            <div class="d-lbl"><?php echo $lbl; ?></div>
            <i class="wi <?php echo $dayIcons[$i]??'wi-day-sunny'; ?> d-ico"></i>
            <div class="d-hi"><?php echo ($c_high[$i]??'--'); ?>°</div>
            <div class="d-lo"><?php echo ($c_low[$i]??'--'); ?>°</div>
            <?php if(($c_rain[$i]??0)>0): ?><div class="d-rain"></div><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <!-- Chart -->
        <div class="chart-wrap">
          <canvas id="fxChart" role="img" aria-label="7-day forecast chart"></canvas>
        </div>
      </div>

    </div><!-- /main -->
  </div><!-- /top-row -->
</div><!-- /shell -->

<script>
/* ─── CONSTANTS ───────────────────────────── */
const KEY="<?php echo $apiKey; ?>";
const INIT={
  labels:<?php echo json_encode($c_labels); ?>,
  highs:<?php echo json_encode($c_high); ?>,
  lows:<?php echo json_encode($c_low); ?>,
  rains:<?php echo json_encode($c_rain); ?>,
  hums:<?php echo json_encode($c_hum); ?>,
};
const WI={
  '01d':'wi-day-sunny','01n':'wi-night-clear','02d':'wi-day-cloudy','02n':'wi-night-alt-cloudy',
  '03d':'wi-cloud','03n':'wi-cloud','04d':'wi-cloudy','04n':'wi-cloudy',
  '09d':'wi-showers','09n':'wi-night-alt-showers','10d':'wi-day-rain','10n':'wi-night-alt-rain',
  '11d':'wi-thunderstorm','11n':'wi-thunderstorm','13d':'wi-snow','13n':'wi-snow','50d':'wi-fog','50n':'wi-fog'
};
function wi(c){return WI[c]||'wi-day-sunny';}
function cap(s){return s?s.charAt(0).toUpperCase()+s.slice(1):'';}
function dir(d){return['N','NE','E','SE','S','SW','W','NW'][Math.round((d||0)/45)%8];}
function uvCol(u){if(u<=2)return'#16a34a';if(u<=5)return'#d97706';if(u<=7)return'#ea580c';if(u<=10)return'#dc2626';return'#7c3aed';}
function uvLbl(u){if(u<=2)return'Low UV';if(u<=5)return'Moderate';if(u<=7)return'High';if(u<=10)return'Very High';return'Extreme';}
function set(id,html){const e=document.getElementById(id);if(e)e.innerHTML=html;}

/* ─── CLOCK ───────────────────────────────── */
setInterval(()=>{const e=document.getElementById('liveClock');if(e)e.textContent=new Date().toLocaleTimeString('en-GB');},1000);

/* ─── MAP ─────────────────────────────────── */
const TILES={
  osm:{url:'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',attr:'© OSM'},
  sat:{url:'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',attr:'Esri'},
  dark:{url:'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',attr:'© CartoDB'},
};
let map,tl,marker;
function initMap(){
  map=L.map('map').setView([20.5937,78.9629],5);
  tl=L.tileLayer(TILES.osm.url,{attribution:TILES.osm.attr,maxZoom:19}).addTo(map);
  map.on('click',e=>load(e.latlng.lat,e.latlng.lng));
}
function setTile(k,btn){
  document.querySelectorAll('.tile-btn').forEach(b=>b.classList.remove('on'));btn.classList.add('on');
  if(tl)map.removeLayer(tl);const t=TILES[k];
  tl=L.tileLayer(t.url,{attribution:t.attr,maxZoom:19}).addTo(map);
}

/* ─── FETCH ───────────────────────────────── */
async function load(lat,lng){
  if(marker)map.removeLayer(marker);
  marker=L.marker([lat,lng]).addTo(map);
  document.getElementById('wxLoad').classList.add('show');
  try{
    const[wr,fr]=await Promise.all([
      fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lng}&appid=${KEY}&units=metric`),
      fetch(`https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lng}&appid=${KEY}&units=metric&cnt=40`)
    ]);
    const w=await wr.json(),f=await fr.json();
    if(w.cod!==200)throw new Error(w.message);
    update(w,f);
  }catch(e){toast('⚠ Could not fetch data for this location.');}
  finally{document.getElementById('wxLoad').classList.remove('show');}
}

function toast(msg){
  const d=document.createElement('div');
  d.style.cssText='position:fixed;bottom:18px;right:18px;background:rgba(255,255,255,0.97);border:1px solid rgba(184,92,56,0.35);color:#1E293B;padding:10px 18px;border-radius:10px;font-size:0.78rem;z-index:9999;backdrop-filter:blur(12px);font-family:Outfit,sans-serif;box-shadow:0 8px 24px rgba(30,41,59,0.15);';
  d.textContent=msg;document.body.appendChild(d);setTimeout(()=>d.remove(),3200);
}

/* ─── UPDATE UI ───────────────────────────── */
function update(w,f){
  /* Hero */
  set('wxCity',w.name||'Unknown');
  document.getElementById('wxTemp').innerHTML=Math.round(w.main.temp)+'<sup>°C</sup>';
  set('wxDesc',cap(w.weather[0].description));
  set('wxFeels','Feels like '+Math.round(w.main.feels_like)+'°C');
  document.getElementById('wxIcon').className='wi '+wi(w.weather[0].icon);
  set('wxMax',Math.round(w.main.temp_max)+'°');
  set('wxMin',Math.round(w.main.temp_min)+'°');
  set('wxHumMini',w.main.humidity+'%');
  set('wxWindMini',Math.round(w.wind.speed*3.6)+' km/h');

  /* Pills */
  const ws=Math.round(w.wind.speed*3.6);
  const uv=Math.round(Math.max(0,12-Math.abs(w.main.humidity-50)/10));
  const vis=w.visibility?(w.visibility/1000).toFixed(1):'--';
  const pp=document.getElementById('wxPills');
  pp.innerHTML=`
    <div class="pill"><div class="pill-em">💨</div><div class="pill-v">${ws}</div><div class="pill-l">km/h · ${dir(w.wind.deg)}</div></div>
    <div class="pill"><div class="pill-em">💧</div><div class="pill-v">${w.main.humidity}%</div><div class="pill-l">Humidity</div></div>
    <div class="pill"><div class="pill-em">☀️</div><div class="pill-v" style="color:${uvCol(uv)}">${uv}</div><div class="pill-l">${uvLbl(uv)}</div></div>
    <div class="pill"><div class="pill-em">👁️</div><div class="pill-v">${vis}</div><div class="pill-l">km Vis.</div></div>
  `;

  /* Detail cards */
  const pres=w.main.pressure,cc=w.clouds.all;
  const dp=Math.round(w.main.temp-((100-w.main.humidity)/5));
  const wsf=(w.wind.speed*3.6).toFixed(1);
  const gust=w.wind.gust?Math.round(w.wind.gust*3.6)+'km/h':'N/A';
  const dpLabel=dp<10?'Dry':dp<16?'Comfortable':dp<21?'Sticky':'Oppressive';
  document.getElementById('wxDetails').innerHTML=`
    <div class="det"><div class="det-top"><span class="det-label">Pressure</span><i class="fa-solid fa-gauge det-icon"></i></div><div class="det-val">${pres}</div><div class="det-sub">${pres>1013?'High':'Low'} pressure · hPa</div><div class="gbar"><div class="gfill" style="width:${Math.min(100,(pres-960)/80*100).toFixed(1)}%;background:linear-gradient(90deg,#818cf8,#a78bfa);"></div></div></div>
    <div class="det"><div class="det-top"><span class="det-label">Cloud Cover</span><i class="fa-solid fa-cloud det-icon"></i></div><div class="det-val">${cc}%</div><div class="det-sub">${cc<25?'Clear sky':cc<70?'Partly cloudy':'Overcast'}</div><div class="gbar"><div class="gfill" style="width:${cc}%;background:linear-gradient(90deg,#94a3b8,#64748b);"></div></div></div>
    <div class="det"><div class="det-top"><span class="det-label">Dew Point</span><i class="fa-solid fa-droplet det-icon"></i></div><div class="det-val">${dp}°C</div><div class="det-sub">${dpLabel}</div><div class="gbar"><div class="gfill" style="width:${Math.min(100,Math.max(0,(dp+10)/50*100)).toFixed(1)}%;background:linear-gradient(90deg,#38bdf8,#0ea5e9);"></div></div></div>
    <div class="det"><div class="det-top"><span class="det-label">Wind</span><i class="fa-solid fa-wind det-icon"></i></div><div class="det-val">${wsf}<small style="font-size:0.6rem;font-weight:400;color:var(--t3);"> km/h</small></div><div class="det-sub">${dir(w.wind.deg)} · Gust ${gust}</div><div class="gbar"><div class="gfill" style="width:${Math.min(100,parseFloat(wsf)/1.5).toFixed(1)}%;background:linear-gradient(90deg,#4F772D,#86b53a);"></div></div></div>
  `;

  /* Hourly — evenly spread */
  const hl=document.getElementById('hList');
  hl.innerHTML=(f.list||[]).slice(0,8).map((h,i)=>{
    const t=new Date(h.dt*1000).toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'});
    return`<div class="h-item ${i===0?'now':''}"><div class="h-time">${i===0?'Now':t}</div><i class="wi ${wi(h.weather[0].icon)} h-icon"></i><div class="h-temp">${Math.round(h.main.temp)}°</div></div>`;
  }).join('');

  /* 7-day rebuild */
  const lbs=['Today'],hi=[Math.round(w.main.temp_max)],lo=[Math.round(w.main.temp_min)],rn=[w.rain?.['1h']||0],hu=[w.main.humidity];
  const icons=[wi(w.weather[0].icon)];
  const seen=new Set();
  for(const it of(f.list||[])){
    const d=new Date(it.dt*1000).toDateString();
    if(!seen.has(d)&&lbs.length<7){seen.add(d);lbs.push(new Date(it.dt*1000).toLocaleDateString('en-US',{weekday:'short'}));hi.push(Math.round(it.main.temp_max));lo.push(Math.round(it.main.temp_min));rn.push((it.rain?.['3h']||0)*1);hu.push(it.main.humidity);icons.push(wi(it.weather[0].icon));}
  }
  CD={labels:lbs,highs:hi,lows:lo,rains:rn,hums:hu};
  document.getElementById('dayStrip').innerHTML=lbs.map((l,i)=>`<div class="ditem ${i===0?'today':''}"><div class="d-lbl">${l}</div><i class="wi ${icons[i]||'wi-day-sunny'} d-ico"></i><div class="d-hi">${hi[i]}°</div><div class="d-lo">${lo[i]}°</div>${(rn[i]||0)>0?'<div class="d-rain"></div>':''}</div>`).join('');
  buildChart();
}

/* ─── CHART ───────────────────────────────── */
let chart;
let CM='temp';
let CD=INIT;

function sw(mode,btn){
  CM=mode;
  document.querySelectorAll('.fc-tab').forEach(b=>b.classList.remove('on'));btn.classList.add('on');
  buildChart();
}

function buildChart(){
  const ctx=document.getElementById('fxChart').getContext('2d');
  if(chart)chart.destroy();

  const labels=CD.labels;
  let datasets,yLbl,yUnit;

  if(CM==='temp'){
    yLbl='Temperature';yUnit='°C';
    datasets=[
      {
        label:'High °C',data:CD.highs,type:'bar',
        backgroundColor:function(ctx){
          const g=ctx.chart.ctx.createLinearGradient(0,0,0,150);
          g.addColorStop(0,'rgba(184,92,56,0.85)');g.addColorStop(1,'rgba(184,92,56,0.2)');return g;
        },
        borderRadius:{topLeft:8,topRight:8},borderSkipped:false,
        barPercentage:0.45,categoryPercentage:0.65,order:2,
      },
      {
        label:'Low °C',data:CD.lows,type:'bar',
        backgroundColor:function(ctx){
          const g=ctx.chart.ctx.createLinearGradient(0,0,0,150);
          g.addColorStop(0,'rgba(79,119,45,0.75)');g.addColorStop(1,'rgba(79,119,45,0.15)');return g;
        },
        borderRadius:{topLeft:8,topRight:8},borderSkipped:false,
        barPercentage:0.45,categoryPercentage:0.65,order:2,
      },
      {
        label:'High Trend',data:CD.highs,type:'line',
        borderColor:'rgba(184,92,56,0.6)',backgroundColor:'transparent',
        pointBackgroundColor:'#B85C38',pointBorderColor:'#fff',pointBorderWidth:2,
        pointRadius:5,pointHoverRadius:7,borderWidth:1.5,tension:0.4,order:1,
      }
    ];
  }else if(CM==='hum'){
    yLbl='Humidity';yUnit='%';
    datasets=[{
      label:'Humidity %',data:CD.hums,type:'line',fill:true,
      borderColor:'#0ea5e9',
      backgroundColor:function(ctx){
        const g=ctx.chart.ctx.createLinearGradient(0,0,0,150);
        g.addColorStop(0,'rgba(14,165,233,0.22)');g.addColorStop(1,'rgba(14,165,233,0.02)');return g;
      },
      pointBackgroundColor:'#0ea5e9',pointBorderColor:'#fff',pointBorderWidth:2,
      pointRadius:5,tension:0.4,borderWidth:2,
    }];
  }else{
    yLbl='Precipitation';yUnit='mm';
    datasets=[{
      label:'Rain mm',data:CD.rains,type:'bar',
      backgroundColor:function(ctx){
        const g=ctx.chart.ctx.createLinearGradient(0,0,0,150);
        g.addColorStop(0,'rgba(79,119,45,0.82)');g.addColorStop(1,'rgba(79,119,45,0.12)');return g;
      },
      borderRadius:{topLeft:8,topRight:8},borderSkipped:false,
      barPercentage:0.5,categoryPercentage:0.7,
    }];
  }

  chart=new Chart(ctx,{
    data:{labels,datasets},
    options:{
      responsive:true,maintainAspectRatio:false,
      interaction:{mode:'index',intersect:false},
      plugins:{
        legend:{display:false},
        tooltip:{
          backgroundColor:'rgba(255,255,255,0.97)',
          titleColor:'#1E293B',bodyColor:'#64748B',
          borderColor:'rgba(30,41,59,0.12)',borderWidth:1,
          padding:12,cornerRadius:12,usePointStyle:true,
          titleFont:{family:'Outfit,sans-serif',size:12,weight:'700'},
          bodyFont:{family:'Outfit,sans-serif',size:11},
          callbacks:{label:c=>` ${c.dataset.label}: ${c.parsed.y}${yUnit}`}
        }
      },
      scales:{
        x:{
          grid:{display:false},border:{display:false},
          ticks:{color:'#94A3B8',font:{size:11,family:'Outfit,sans-serif',weight:'700'},maxRotation:0}
        },
        y:{
          grid:{color:'rgba(30,41,59,0.06)',drawTicks:false},
          border:{display:false},
          ticks:{
            color:'#94A3B8',
            font:{size:10,family:'DM Mono,monospace'},
            padding:10,
            callback:v=>v+yUnit
          }
        }
      }
    }
  });
}

/* ─── BOOT ────────────────────────────────── */
document.addEventListener('DOMContentLoaded',()=>{initMap();buildChart();});
</script>
<?php require("footer.php"); ?>
</body>
</html>
<?php
// yield_prediction.php (Single file, self-processing)
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit; // Stop execution after redirect
}

$query4 = "SELECT * from farmerlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['farmer_id'];
$para2 = $row4['farmer_name'];

// --- PHP Prediction Logic ---
$prediction_result_html = '';

if(isset($_POST['Yield_Predict'])){
    
    $state=trim($_POST['state']);
    $district=trim($_POST['district']);
    $season=trim($_POST['Season']);
    $crops=trim($_POST['crops']);
    $area=trim($_POST['area']);

    // Run Python Script (Preserving your original code)
    $Jstate=json_encode($state);
    $Jdistrict=json_encode($district);
    $Jseason=json_encode($season);
    $Jcrops=json_encode($crops);
    $Jarea=json_encode($area);

    $command = escapeshellcmd("python ML/yield_prediction/yield_prediction.py $Jstate $Jdistrict $Jseason $Jcrops $Jarea ");
    $predicted_yield_raw = shell_exec($command);
    
    // Clean and validate the output.
    $predicted_yield = floatval(trim($predicted_yield_raw));
    
    // --- Generate HTML for the Result Section ---
    $prediction_result_html = '
    <h3 class="mb-4 text-center" style="color: var(--color-secondary-green);">Predicted Yield for ' . htmlspecialchars($crops) . ':</h3>
    <div class="alert py-4 alert-custom" role="alert">
        <h4 class="alert-heading font-weight-bold" style="color: var(--color-text-dark);">Parameters Submitted:</h4>
        <p class="mb-0" style="color: var(--color-text-dark);">
            <strong>State:</strong> ' . htmlspecialchars($state) . ' | <strong>District:</strong> ' . htmlspecialchars($district) . ' | <strong>Season:</strong> ' . htmlspecialchars($season) . '<br>
            <strong>Crop:</strong> ' . htmlspecialchars($crops) . ' | <strong>Area:</strong> ' . htmlspecialchars($area) . ' Hectares
        </p>
    </div>
    <hr class="my-4">';

    if ($predicted_yield > 0) {
        $prediction_result_html .= '
        <div class="text-center py-5">
            <p class="yield-value">' . number_format($predicted_yield, 2) . '</p>
            <p class="yield-unit text-uppercase">Quintal (Expected Output)</p>
        </div>
        <div class="alert alert-success text-center mt-4">
            <p class="mb-0 font-weight-bold">The forecast suggests a total yield of ' . number_format($predicted_yield, 2) . ' Quintal for this cultivation.</p>
        </div>';
    } else {
        $prediction_result_html .= '
        <div class="alert alert-warning text-center" role="alert">
            <p class="lead mb-0" style="color: var(--color-text-dark);"><i class="ni ni-notification-70 mr-2"></i> No valid yield was predicted. Please ensure your inputs are correct and the model path is valid.</p>
        </div>';
    }
} else {
    // Default message when form is first loaded
    $prediction_result_html = '
    <div class="alert text-center" role="alert" style="background-color: #f0f0f0;">
        <p class="lead mb-0" style="color: var(--color-text-dark);"><i class="ni ni-notification-70 mr-2"></i> Enter the farming details above and click "Predict Crop Yield" to view the forecast.</p>
    </div>';
}

?>

<!DOCTYPE html>
<html>
<?php include ('fheader.php'); ?>

<style>
/* ----------------------------------------------------------- */
/* 1. CUSTOM COLOR THEME DEFINITION */
/* ----------------------------------------------------------- */
:root {
    --color-primary-dark: #0A3D0A; /* Deep Neem Green */
    --color-accent-terracotta: #B85C38; /* Rustic Terracotta */
    --color-secondary-green: #4F772D; /* Lush Field Green */
    --color-bg-light: #F9F7F3; /* Warm Cream Background */
    --color-text-dark: #1E293B; /* Slate Dark */
}

/* ----------------------------------------------------------- */
/* 2. AI LENS LOADING SCREEN (NEW ADDITION) */
/* ----------------------------------------------------------- */
#loader-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: radial-gradient(circle, #0D4721 0%, #051A0B 100%);
    display: none; 
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    color: white;
}

.scanner-lens {
    position: relative;
    width: 150px; height: 150px;
    display: flex; justify-content: center; align-items: center;
    margin-bottom: 30px;
}

.lens-ring {
    position: absolute;
    border-radius: 50%;
    border: 2px solid transparent;
    border-top-color: var(--color-secondary-green);
    animation: rotate-lens 2s linear infinite;
}

.ring-1 { width: 100%; height: 100%; opacity: 0.8; border-top-color: #fff; }
.ring-2 { width: 80%; height: 80%; opacity: 0.6; animation-duration: 3s; border-top-color: var(--color-secondary-green); }
.ring-3 { width: 60%; height: 60%; opacity: 0.4; animation-direction: reverse; border-top-color: #fff; }

.lens-center {
    position: absolute;
    width: 15px; height: 15px;
    background: white; border-radius: 50%;
    box-shadow: 0 0 20px #fff, 0 0 40px var(--color-secondary-green);
    animation: pulse-center 1s ease-in-out infinite alternate;
}

@keyframes rotate-lens { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@keyframes pulse-center { from { transform: scale(0.8); opacity: 0.5; } to { transform: scale(1.2); opacity: 1; } }

.loading-text-container { text-align: center; }
.loading-main-text { font-size: 1.8rem; font-weight: 800; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 5px; color: #ffffff; }
.loading-sub-text { font-family: 'Courier New', Courier, monospace; font-size: 1rem; color: #8db38b; letter-spacing: 1px; }

.dots::after { content: ''; animation: dots-anim 1.5s infinite; }
@keyframes dots-anim { 0% { content: ''; } 33% { content: '.'; } 66% { content: '..'; } 100% { content: '...'; } }

/* ----------------------------------------------------------- */
/* 3. GENERAL STYLING & OVERRIDES (Original styles preserved) */
/* ----------------------------------------------------------- */
body {
    background-color: var(--color-bg-light) !important;
    color: var(--color-text-dark);
}

.text-dark {
    color: var(--color-text-dark) !important;
}

.btn-primary-custom {
    background-color: var(--color-primary-dark);
    border-color: var(--color-primary-dark);
    color: white;
}
.btn-primary-custom:hover {
    background-color: var(--color-secondary-green);
    border-color: var(--color-secondary-green);
}

.btn-accent-custom {
    background-color: var(--color-accent-terracotta);
    border-color: var(--color-accent-terracotta);
    color: white;
}
.btn-accent-custom:hover {
    background-color: #9E4D31;
    border-color: #9E4D31;
}

.badge-success-custom {
    background-color: var(--color-secondary-green) !important;
    color: white !important;
}

.form-control:focus {
    border-color: var(--color-accent-terracotta);
    box-shadow: 0 0 0 0.2rem rgba(184, 92, 56, 0.25);
}

#hero-section {
    position: relative;
    height: 100vh; 
    width: 100%;
    background-image: url('../assets/img/rain5.jpg'); 
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    z-index: 1;
}

#hero-section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(rgba(10, 61, 10, 0.4), rgba(10, 61, 10, 0.6)); 
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    color: white !important;
}

.hero-content h1, .hero-content p, .hero-content a {
    color: white !important;
}

#results-container .yield-value {
    font-size: 3rem;
    font-weight: 700;
    color: var(--color-primary-dark);
    margin: 0;
}
#results-container .yield-unit {
    font-size: 1.5rem;
    color: var(--color-accent-terracotta);
}

.alert-custom {
    background-color: var(--color-bg-light); 
    border-left: 5px solid var(--color-accent-terracotta);
}

.alert.alert-success {
    background-color: #19692c !important; 
    color: white !important;
    border-color: #19692c !important;
}

.alert.alert-success p {
    color: white !important;
}

html {
    scroll-behavior: smooth;
}

/* ========== RESPONSIVE MEDIA QUERIES (ADDED ONLY) ========== */

/* Large Tablets and Small Desktops */
@media (max-width: 992px) {
    #hero-section {
        height: 80vh;
        background-attachment: scroll;
    }
    
    .hero-content h1.display-1 {
        font-size: 3rem;
    }
    
    .hero-content p.lead {
        font-size: 1.1rem;
    }
    
    .btn-accent-custom.btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .py-7 {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }
    
    .display-4 {
        font-size: 2rem !important;
    }
    
    .card-body.p-lg-5 {
        padding: 2rem !important;
    }
}

/* Tablets and Mobile Devices */
@media (max-width: 768px) {
    #hero-section {
        height: 70vh;
    }
    
    .hero-content h1.display-1 {
        font-size: 2rem;
    }
    
    .hero-content p.lead {
        font-size: 0.9rem;
        margin-bottom: 1.5rem !important;
    }
    
    .btn-accent-custom.btn-lg {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
    
    .py-7 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }
    
    .display-4 {
        font-size: 1.6rem !important;
    }
    
    .badge-pill {
        font-size: 0.7rem;
        padding: 0.4rem 1rem !important;
    }
    
    .card-body.p-lg-5 {
        padding: 1.5rem !important;
    }
    
    /* Stack form fields */
    .col-md-4.mb-4,
    .col-md-6.mb-4 {
        margin-bottom: 0.8rem !important;
    }
    
    .form-control-label {
        font-size: 0.75rem !important;
    }
    
    .form-control-lg {
        font-size: 0.9rem;
        padding: 0.6rem 0.8rem;
    }
    
    .btn-primary-custom.btn-lg {
        font-size: 0.9rem;
        padding: 0.8rem 1rem;
    }
    
    /* Step 1 and Step 2 Badge Alignment Fix */
    .text-center.mb-5 {
        margin-bottom: 1.5rem !important;
        text-align: center;
        width: 100%;
    }
    
    .badge-pill {
        display: inline-block;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }
    
    /* Alert box fixes - FIXED ALIGNMENT */
    .alert-custom,
    .alert.alert-success,
    .alert.alert-warning,
    .alert.text-center {
        padding: 1rem !important;
        margin-bottom: 1rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
        border-radius: 0.75rem;
        width: 100%;
        box-sizing: border-box;
    }
    
    .alert-custom h4 {
        font-size: 1rem;
        word-break: keep-all;
    }
    
    .alert-custom p {
        font-size: 0.85rem;
        word-break: break-word;
        line-height: 1.4;
    }
    
    /* Parameters text formatting */
    .alert-custom p.mb-0 {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem 0.5rem;
    }
    
    .alert-custom p.mb-0 br {
        display: none;
    }
    
    /* Result card fixes */
    .card.shadow-lg.border-0 {
        margin-bottom: 1rem;
        overflow: hidden;
        border-radius: 1rem !important;
        width: 100%;
    }
    
    .card.shadow-lg.border-0 .card-body {
        padding: 1.5rem !important;
    }
    
    /* Results container fixes */
    #results-container {
        width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
    }
    
    #results-container .text-center.py-5 {
        padding: 1.5rem 0 !important;
    }
    
    #results-container .yield-value {
        font-size: 2rem;
    }
    
    #results-container .yield-unit {
        font-size: 1rem;
    }
    
    /* Alert success styling */
    .alert.alert-success {
        padding: 0.8rem !important;
        width: 100%;
        box-sizing: border-box;
    }
    
    .alert.alert-success p {
        font-size: 0.85rem;
        word-break: break-word;
    }
    
    hr.my-4 {
        margin: 1rem 0 !important;
    }
    
    h3.mb-4 {
        font-size: 1.2rem;
        margin-bottom: 1rem !important;
        word-break: break-word;
        text-align: center;
    }
}

/* Small Mobile Devices */
@media (max-width: 480px) {
    #hero-section {
        height: 60vh;
    }
    
    .hero-content h1.display-1 {
        font-size: 1.4rem;
    }
    
    .hero-content p.lead {
        font-size: 0.75rem;
        padding: 0 10px;
    }
    
    .btn-accent-custom.btn-lg {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }
    
    .display-4 {
        font-size: 1.3rem !important;
    }
    
    .badge-pill {
        font-size: 0.6rem;
        padding: 0.3rem 0.8rem !important;
    }
    
    .card-body.p-lg-5 {
        padding: 1rem !important;
    }
    
    .form-control-lg {
        font-size: 0.85rem;
        padding: 0.5rem 0.7rem;
    }
    
    .form-control-label {
        font-size: 0.7rem !important;
        margin-bottom: 0.3rem !important;
    }
    
    .btn-primary-custom.btn-lg {
        font-size: 0.85rem;
        padding: 0.7rem 0.8rem;
    }
    
    /* Alert fixes for small mobile */
    .alert-custom,
    .alert.alert-success,
    .alert.alert-warning {
        padding: 0.8rem !important;
    }
    
    .alert-custom h4 {
        font-size: 0.9rem;
    }
    
    .alert-custom p {
        font-size: 0.75rem;
    }
    
    /* Card body fix */
    .card.shadow-lg.border-0 .card-body {
        padding: 1rem !important;
    }
    
    /* Results container */
    #results-container .yield-value {
        font-size: 1.6rem;
    }
    
    #results-container .yield-unit {
        font-size: 0.85rem;
    }
    
    #results-container .text-center.py-5 {
        padding: 1rem 0 !important;
    }
    
    h3.mb-4 {
        font-size: 1rem;
    }
    
    .alert.alert-success p {
        font-size: 0.75rem;
    }
    
    /* Parameters text wrapping fix */
    .alert-custom p.mb-0 {
        flex-direction: column;
        gap: 0.2rem;
    }
    
    /* Loader scaling */
    .scanner-lens {
        width: 100px;
        height: 100px;
    }
    
    .loading-main-text {
        font-size: 1.2rem;
        letter-spacing: 3px;
    }
    
    .loading-sub-text {
        font-size: 0.8rem;
    }
    
    /* Step 1 and Step 2 container alignment */
    .text-center.mb-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

/* Landscape Mode for Mobile */
@media (max-width: 768px) and (orientation: landscape) {
    #hero-section {
        height: 100vh;
        min-height: 500px;
    }
    
    .hero-content h1.display-1 {
        font-size: 1.6rem;
    }
    
    .py-7 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    
    .col-md-4.mb-4,
    .col-md-6.mb-4 {
        margin-bottom: 0.5rem !important;
    }
    
    .card-body.p-lg-5 {
        padding: 1rem !important;
    }
    
    /* Results container fix for landscape */
    .card.shadow-lg.border-0 .card-body {
        padding: 1rem !important;
    }
}

/* Extra Large Desktop Screens */
@media (min-width: 1400px) {
    .container {
        max-width: 1320px;
    }
    
    .hero-content h1.display-1 {
        font-size: 4.5rem;
    }
}

/* Fix for select dropdowns on mobile */
@media (max-width: 768px) {
    select.form-control {
        font-size: 0.9rem;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    
    /* Improve touch targets */
    .btn-primary-custom,
    .btn-accent-custom {
        min-height: 44px;
    }
    
    /* Ensure alerts don't overflow */
    .alert {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    /* Fix for results container background */
    #results-container {
        background: transparent;
    }
    
    #results-container .alert {
        width: 100%;
        box-sizing: border-box;
    }
}

/* Small fix for hero content on very short screens */
@media (max-height: 600px) {
    #hero-section {
        min-height: 400px;
    }
    
    .hero-content h1.display-1 {
        font-size: 1.6rem;
        margin-bottom: 0.5rem !important;
    }
    
    .hero-content p.lead {
        margin-bottom: 0.8rem !important;
    }
}
</style>

<body id="top"> 

    <div id="loader-overlay">
        <div class="scanner-lens">
            <div class="lens-ring ring-1"></div>
            <div class="lens-ring ring-2"></div>
            <div class="lens-ring ring-3"></div>
            <div class="lens-center"></div>
        </div>
        <div class="loading-text-container">
            <div class="loading-main-text">YIELD ANALYSIS<span class="dots"></span></div>
            <div id="dynamic-subtext" class="loading-sub-text">Connecting to harvest database...</div>
        </div>
    </div>
    
<?php include ('fnav.php'); ?>

    <section id="hero-section">
        <div class="container hero-content">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1 class="display-1 font-weight-bolder mb-4">
                        Predict Future Harvest Yields
                    </h1>
                    <p class="lead opacity-8 mb-5">
                        Input your farming parameters to accurately forecast the expected yield of your selected crop for better planning and resource management.
                    </p>
                    
                    <a href="#prediction-tool" class="btn btn-accent-custom btn-lg btn-icon shadow-lg lift">
                        <span class="btn-inner--text">Start Yield Forecast</span>
                        <span class="btn-inner--icon"><i class="ni ni-bold-down"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="prediction-tool" class="py-7">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    
                    <div class="text-center mb-5">
                        <span class="badge badge-pill mb-3 text-uppercase badge-success-custom">Step 1: Input</span>
                        <h2 class="display-4 font-weight-bold" style="color: var(--color-primary-dark);">Enter Yield Parameters</h2>
                    </div>

                    <div class="card shadow-lg border-light rounded-lg mb-5">
                        <div class="card-body p-lg-5">
                            <form id="yield-form" role="form" action="#prediction-results-area" method="post">
                                
                                <div class="row">
                                    
                                    <div class="col-md-4 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="state-select" style="color: var(--color-text-dark);">State</label>
                                            <select id="state-select" name ="state" class="form-control form-control-lg" required>
                                                <option value="Karnataka">Karnataka</option> 
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="district-select" style="color: var(--color-text-dark);">District</label>
                                            <select id ="district-select" name="district" class="form-control form-control-lg" required>
                                                <option value="">Select a district</option>
                                                <option value="BAGALKOT">Bagalkot</option>
                                                <option value="BANGALORE_RURAL">Bangalore Rural</option>
                                                <option value="BELGAUM">Belgaum</option>
                                                <option value="BELLARY">Bellary</option>
                                                <option value="BENGALURU_URBAN">Bengaluru Urban</option>
                                                <option value="BIDAR">Bidar</option>
                                                <option value="BIJAPUR">Bijapur</option>
                                                <option value="CHAMARAJANAGAR">Chamarajanagar</option>
                                                <option value="CHIKBALLAPUR">Chikballapur</option>
                                                <option value="CHIKMAGALUR">Chikmagalur</option>
                                                <option value="CHITRADURGA">Chitradurga</option>
                                                <option value="DAKSHIN_KANNAD">Dakshin Kannad</option>
                                                <option value="DAVANGERE">Davangere</option>
                                                <option value="DHARWAD">Dharwad</option>
                                                <option value="GADAG">Gadag</option>
                                                <option value="GULBARGA">Gulbarga</option>
                                                <option value="HAVERI">Haveri</option>
                                                <option value="HASSAN">Hassan</option>
                                                <option value="KODAGU">Kodagu</option>
                                                <option value="KOLAR">Kolar</option>
                                                <option value="KOPPAL">Koppal</option>
                                                <option value="MANDYA">Mandya</option>
                                                <option value="MYSORE">Mysore</option>
                                                <option value="RAMANAGARA">Ramanagara</option>
                                                <option value="RAICHUR">Raichur</option>
                                                <option value="SHIMOGA">Shimoga</option>
                                                <option value="TUMKUR">Tumkur</option>
                                                <option value="UDUPI">Udupi</option>
                                                <option value="UTTAR_KANNAD">Uttar Kannad</option>
                                                <option value="YADGIR">Yadgir</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="season-select" style="color: var(--color-text-dark);">Season</label>
                                            <select id="season-select" name="Season" class="form-control form-control-lg" required> 
                                                <option value="">Select Season ...</option>
                                                <option value="Kharif">Kharif</option>
                                                <option value="Rabi">Rabi</option>
                                                <option value="Summer">Summer</option>
                                                <option value="WholeYear">Whole Year</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div class="row">

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="crop-select" style="color: var(--color-text-dark);">Crop Name</label>
                                            <select id="crop-select" class="form-control form-control-lg" name="crops" required>
                                                <option value="">Select crop</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="area-input" style="color: var(--color-text-dark);">Area (in Hectares)</label>
                                            <input type="number" step="0.01" id="area-input" name="area" class="form-control form-control-lg" placeholder="e.g., 10.5" required>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <script> 
                                    document.getElementById("season-select").addEventListener("change", function() { 
                                        const districtDropdown = document.getElementById('district-select');
                                        const seasonDropdown = document.getElementById('season-select');
                                        const cropDropdown = document.getElementById('crop-select');

                                        if (typeof cropOptions === 'undefined') {
                                            console.error("Error: cropOptions object is missing. Dynamic crop dropdown will not work.");
                                            cropDropdown.innerHTML = '<option value="">Error: Crop data missing</option>';
                                            return;
                                        }

                                        const selectedDistrict = districtDropdown.value;
                                        const selectedSeason = seasonDropdown.value;

                                        cropDropdown.innerHTML = '<option value="">Select crop</option>';
                                        
                                        if (selectedDistrict && selectedSeason && cropOptions[selectedDistrict] && cropOptions[selectedDistrict][selectedSeason]) {
                                            const options = cropOptions[selectedDistrict][selectedSeason];
                                            for (const option of options) {
                                                const optionElement = document.createElement('option');
                                                optionElement.value = option; 
                                                optionElement.text = option;
                                                cropDropdown.appendChild(optionElement);
                                            }
                                        }
                                    }); 
                                </script> 

                                <div class="text-center mt-5">
                                    <button type="submit" value="Yield" name="Yield_Predict" class="btn btn-primary-custom btn-lg btn-block btn-icon shadow-lg lift">
                                        <span class="btn-inner--icon"><i class="ni ni-chart-bar-32"></i></span>
                                        <span class="btn-inner--text">Predict Crop Yield</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="text-center mb-5" id="prediction-results-area">
                        <span class="badge badge-pill mb-3 text-uppercase btn-accent-custom">Step 2: Results</span>
                        <h2 class="display-4 font-weight-bold" style="color: var(--color-primary-dark);">Forecasted Harvest Yield</h2>
                    </div>

                    <div class="card shadow-lg border-0">
                        <div class="card-body p-lg-5">
                            
                            <div id="results-container">
                                <?php echo $prediction_result_html; ?>
                            </div>
                            
                        </div>
                    </div>
                    
                    <?php if (isset($_POST['Yield_Predict'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const resultElement = document.getElementById('prediction-results-area');
                            if (resultElement) {
                                resultElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        });
                    </script>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>

    <?php require("footer.php");?>

    <script>
    const yieldForm = document.getElementById('yield-form');
    const loader = document.getElementById('loader-overlay');
    const subtext = document.getElementById('dynamic-subtext');

    const yieldMessages = [
        "Fetching regional soil data...",
        "Analyzing historical crop yields...",
        "Processing climate variations...",
        "Applying ML Prediction models...",
        "Finalizing harvest forecast..."
    ];

    yieldForm.addEventListener('submit', function() {
        loader.style.display = 'flex';
        let step = 0;
        const msgInterval = setInterval(() => {
            if (step < yieldMessages.length) {
                subtext.innerText = yieldMessages[step];
                step++;
            } else {
                clearInterval(msgInterval);
            }
        }, 1500);
    });
    </script>

</body>
</html>
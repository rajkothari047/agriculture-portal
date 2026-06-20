<?php
// frainfall_prediction.php (Single file, self-processing)
include ('fsession.php');
ini_set('memory_limit', '-1');

// Essential session check
if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit; 
}

$query4 = "SELECT * from farmerlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['farmer_id'];
$para2 = $row4['farmer_name'];

// --- PHP Prediction Logic ---
$prediction_result_html = '';

if(isset($_POST['Rainfall_Predict'])){
    
    // --- Input reading secured inside the submission block ---
    $region=trim($_POST['region']);
    $month=trim($_POST['month']);

    // Display message for debugging/confirmation
    $predicted_rainfall_display = '';
    
    try {
        // Prepare JSON encoded parameters
        $Jregion=json_encode($region);
        $Jmonth=json_encode($month);

        // Execute Python Script (Using passthru as in your original code)
        $command = escapeshellcmd("python ML/rainfall_prediction/rainfall_prediction.py $Jregion $Jmonth");
        // Capture the output directly
        ob_start();
        passthru($command);
        $predicted_rainfall_raw = ob_get_clean();
        
        // Clean and validate the output. Expecting a single float value (rainfall in mm)
        $predicted_rainfall = floatval(trim($predicted_rainfall_raw));
        $predicted_rainfall_display = number_format($predicted_rainfall, 2);
        
    } catch (Exception $e) {
        // Handle execution errors if passthru fails unexpectedly
        $predicted_rainfall_display = "Error running model.";
    }
    // --- Generate HTML for the Result Section ---
    $prediction_result_html = '
    <h3 class="mb-4 text-center" style="color: var(--color-secondary-green);">Predicted Rainfall for ' . htmlspecialchars($month) . ':</h3>
    <div class="alert py-4 alert-custom" role="alert">
        <h4 class="alert-heading font-weight-bold" style="color: var(--color-text-dark);">Parameters Submitted:</h4>
        <p class="mb-0" style="color: var(--color-text-dark);">
            <strong>Region:</strong> ' . htmlspecialchars($region) . ' | <strong>Month:</strong> ' . htmlspecialchars($month) . '
        </p>
    </div>
    <hr class="my-4">';

    if (is_numeric($predicted_rainfall) && $predicted_rainfall >= 0) {
        // Use the dark green box styling for the result
        $prediction_result_html .= '
        <div class="text-center py-5">
            <p class="rainfall-value">' . $predicted_rainfall_display . '</p>
            <p class="rainfall-unit text-uppercase">MM (Expected Monthly Rainfall)</p>
        </div>
        <div class="alert alert-success text-center mt-4">
            <p class="mb-0 font-weight-bold">The forecast suggests an average rainfall of ' . $predicted_rainfall_display . ' mm for the month of ' . htmlspecialchars($month) . '.</p>
        </div>';
    } else {
        $prediction_result_html .= '
        <div class="alert alert-warning text-center" role="alert">
            <p class="lead mb-0" style="color: var(--color-text-dark);"><i class="ni ni-notification-70 mr-2"></i> No valid rainfall was predicted. Check inputs or model script. Raw output: ' . htmlspecialchars($predicted_rainfall_raw) . '</p>
        </div>';
    }

} else {
    // Default message when form is first loaded
    $prediction_result_html = '
    <div class="alert text-center" role="alert" style="background-color: #f0f0f0;">
        <p class="lead mb-0" style="color: var(--color-text-dark);"><i class="ni ni-notification-70 mr-2"></i> Enter the region and month above and click "Predict Rainfall" to view the forecast.</p>
    </div>';
}
?>

<!DOCTYPE html>
<html>
<?php include ('fheader.php'); ?>

<style>
/* ----------------------------------------------------------- */
/* 1. CUSTOM COLOR THEME DEFINITION - CONSISTENT STYLING */
/* ----------------------------------------------------------- */
:root {
    --color-primary-dark: #0A3D0A; /* Deep Neem Green */
    --color-accent-terracotta: #B85C38; /* Rustic Terracotta */
    --color-secondary-green: #4F772D; /* Lush Field Green */
    --color-bg-light: #F9F7F3; /* Warm Cream Background */
    --color-text-dark: #1E293B; /* Slate Dark */
}

/* ----------------------------------------------------------- */
/* 2. NEW PROFESSIONAL AI LENS LOADING SCREEN */
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
/* 3. GENERAL STYLING & OVERRIDES */
/* ----------------------------------------------------------- */
body {
    background-color: var(--color-bg-light) !important;
    color: var(--color-text-dark);
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

.btn-accent-custom .btn-inner--text {
    color: white !important;
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
    background-image: url('../assets/img/rain2.jpg'); 
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
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(rgba(18, 52, 86, 0.5), rgba(18, 52, 86, 0.5)); 
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    color: white !important;
}

.hero-content h1 {
    color: white !important;
}

.lift { transition: all .15s ease-in-out; }
.lift:hover { transform: translateY(-3px); box-shadow: 0 1rem 2.5rem rgba(0,0,0,.1) !important; }

#results-container .rainfall-value {
    font-size: 3rem;
    font-weight: 700;
    color: var(--color-primary-dark);
    margin: 0;
}
#results-container .rainfall-unit {
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

html { scroll-behavior: smooth; }

/* ========== RESPONSIVE MEDIA QUERIES (ADDED ONLY) ========== */

/* Large Tablets and Small Desktops */
@media (max-width: 992px) {
    #hero-section {
        height: 80vh;
        background-attachment: scroll;
    }
    
    .hero-content h1.display-1 {
        font-size: 2.5rem !important;
    }
    
    .hero-content p.lead {
        font-size: 1rem;
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
    
    .col-lg-8 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

/* Tablets and Mobile Devices */
@media (max-width: 768px) {
    #hero-section {
        height: 70vh;
    }
    
    .hero-content h1.display-1 {
        font-size: 1.8rem !important;
    }
    
    .hero-content p.lead {
        font-size: 0.85rem;
        margin-bottom: 1.5rem !important;
    }
    
    .btn-accent-custom.btn-lg {
        padding: 0.6rem 1.2rem;
        font-size: 0.85rem;
    }
    
    .py-7 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }
    
    .display-4 {
        font-size: 1.5rem !important;
    }
    
    .badge-pill {
        font-size: 0.7rem;
        padding: 0.4rem 1rem !important;
    }
    
    .card-body.p-lg-5 {
        padding: 1.5rem !important;
    }
    
    /* Stack form fields */
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
    
    #results-container .rainfall-value {
        font-size: 2rem;
    }
    
    #results-container .rainfall-unit {
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
        font-size: 1.3rem !important;
    }
    
    .hero-content p.lead {
        font-size: 0.7rem;
        padding: 0 10px;
    }
    
    .btn-accent-custom.btn-lg {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .display-4 {
        font-size: 1.2rem !important;
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
    #results-container .rainfall-value {
        font-size: 1.5rem;
    }
    
    #results-container .rainfall-unit {
        font-size: 0.8rem;
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
        font-size: 1.5rem !important;
    }
    
    .py-7 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    
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
        font-size: 4rem;
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
        font-size: 1.5rem !important;
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
            <div class="loading-main-text">METEO ANALYSIS<span class="dots"></span></div>
            <div id="dynamic-subtext" class="loading-sub-text">Connecting to weather stations...</div>
        </div>
    </div>
    
<?php include ('fnav.php'); ?>

    <section id="hero-section">
        <div class="container hero-content">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1 class="display-1 font-weight-bolder mb-4">
                        Predict Monthly Rainfall Levels
                    </h1>
                    <p class="lead opacity-8 mb-5">
                        Get accurate forecasts of expected rainfall based on location and time, crucial for planning irrigation and crop sowing.
                    </p>
                    
                    <a href="#prediction-tool" class="btn btn-accent-custom btn-lg btn-icon shadow-lg lift">
                        <span class="btn-inner--text">Start Rainfall Forecast</span>
                        <span class="btn-inner--icon"><i class="ni ni-bold-down"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="prediction-tool" class="py-7">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <div class="text-center mb-5">
                        <span class="badge badge-pill mb-3 text-uppercase badge-success-custom">Step 1: Input</span>
                        <h2 class="display-4 font-weight-bold" style="color: var(--color-primary-dark);">Enter Region and Month</h2>
                    </div>

                    <div class="card shadow-lg border-light rounded-lg mb-5">
                        <div class="card-body p-lg-5">
                            <form id="rainfall-form" role="form" action="#prediction-results-area" method="post">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="region-select" style="color: var(--color-text-dark);">Region</label>
                                            <select id="region-select" name="region" class="form-control form-control-lg" required>
                                                <option value="">Select Region</option>
                                            </select>
                                            <script language="javascript"> print_region("region-select"); </script>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="month-select" style="color: var(--color-text-dark);">Month</label>
                                            <select id="month-select" name="month" class="form-control form-control-lg" required>
                                                <option value="">Select Month</option>
                                            </select>
                                            <script language="javascript"> print_months("month-select"); </script>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-5">
                                    <button type="submit" value="Rainfall" name="Rainfall_Predict" class="btn btn-primary-custom btn-lg btn-block btn-icon shadow-lg lift">
                                        <span class="btn-inner--icon"><i class="ni ni-cloud-download-95"></i></span>
                                        <span class="btn-inner--text">Predict Rainfall</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="text-center mb-5" id="prediction-results-area">
                        <span class="badge badge-pill mb-3 text-uppercase btn-accent-custom">Step 2: Results</span>
                        <h2 class="display-4 font-weight-bold" style="color: var(--color-primary-dark);">Forecasted Monthly Rainfall</h2>
                    </div>

                    <div class="card shadow-lg border-0">
                        <div class="card-body p-lg-5">
                            <div id="results-container">
                                <?php echo $prediction_result_html; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isset($_POST['Rainfall_Predict'])): ?>
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
    /* LOADER LOGIC */
    const rainForm = document.getElementById('rainfall-form');
    const loader = document.getElementById('loader-overlay');
    const subtext = document.getElementById('dynamic-subtext');

    const rainMessages = [
        "Fetching regional climate data...",
        "Analyzing historical precipitation...",
        "Running ML Regression models...",
        "Calculating monthly deviations...",
        "Finalizing rainfall forecast..."
    ];

    rainForm.addEventListener('submit', function() {
        loader.style.display = 'flex';
        let step = 0;
        const msgInterval = setInterval(() => {
            if (step < rainMessages.length) {
                subtext.innerText = rainMessages[step];
                step++;
            } else {
                clearInterval(msgInterval);
            }
        }, 1500);
    });
    </script>

</body>
</html>
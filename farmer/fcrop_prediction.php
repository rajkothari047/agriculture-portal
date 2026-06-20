<?php
// Core logic remains the same (Session and database fetching)
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit; 
}

$query4 = "SELECT * from farmerlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['farmer_id'];
$para2 = $row4['farmer_name'];

// Check if form was just submitted to keep loader active during PHP processing
$is_loading = isset($_POST['Crop_Predict']) ? 'flex' : 'none';
?>

<!DOCTYPE html>
<html>
<?php include ('fheader.php'); // Include header (meta tags, CSS links) ?>

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

/* --- LOADING SCREEN CSS --- */
#loader-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: radial-gradient(circle, #0D4721 0%, #051A0B 100%);
    /* Updated display to use PHP state */
    display: <?php echo $is_loading; ?>; 
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 999999; /* Increased priority */
}
.scanner-lens {
    position: relative;
    width: 150px; height: 150px;
    display: flex; justify-content: center; align-items: center;
}
.lens-ring {
    position: absolute;
    border-radius: 50%;
    border: 2px solid transparent;
    animation: rotate-lens 2s linear infinite;
}
.ring-1 { width: 100%; height: 100%; border-top-color: #fff; }
.ring-2 { width: 80%; height: 80%; border-top-color: var(--color-secondary-green); animation-duration: 3s; }
.lens-center {
    width: 15px; height: 15px;
    background: white; border-radius: 50%;
    box-shadow: 0 0 20px #fff, 0 0 40px var(--color-secondary-green);
    animation: pulse-center 1s ease-in-out infinite alternate;
}
@keyframes rotate-lens { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@keyframes pulse-center { from { transform: scale(0.8); } to { transform: scale(1.2); } }
.loading-text { font-size: 1.2rem; color: white; margin-top: 20px; letter-spacing: 3px; font-weight: bold; }

/* ----------------------------------------------------------- */
/* 2. GENERAL STYLING & OVERRIDES */
/* ----------------------------------------------------------- */
body {
    background-color: var(--color-bg-light) !important;
    color: var(--color-text-dark);
}

.text-dark {
    color: var(--color-text-dark) !important;
}

/* Custom Buttons & Badges */
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

/* Form Inputs & Focus */
.form-control:focus {
    border-color: var(--color-accent-terracotta);
    box-shadow: 0 0 0 0.2rem rgba(184, 92, 56, 0.25);
}

/* ----------------------------------------------------------- */
/* 3. HERO SECTION STYLING (FIXED) */
/* ----------------------------------------------------------- */
#hero-section {
    position: relative;
    height: 100vh; 
    width: 100%;
    background-image: url('../assets/img/main12.jpg'); 
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

/* Table Specific Styling for bottom border only */
#prediction-results .table td, #prediction-results .table th {
    border-top: none;
}
#prediction-results .table tbody tr {
    border-bottom: 1px solid #eee;
}
#prediction-results .table tbody tr:last-child {
    border-bottom: none;
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
    .col-md-4.mb-4 {
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
    
    /* Alert box - FIXED */
    .alert.py-4 {
        padding: 1rem !important;
        margin-bottom: 1rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .alert h4 {
        font-size: 1rem;
        word-break: keep-all;
    }
    
    .alert p {
        font-size: 0.85rem;
        word-break: break-word;
        line-height: 1.4;
    }
    
    /* Result card fixes */
    .card.shadow-lg.border-0 {
        margin-bottom: 1rem;
        overflow: hidden;
    }
    
    .card.shadow-lg.border-0 .card-body {
        padding: 1.5rem !important;
    }
    
    /* Table fixes - FIXED ALIGNMENT */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -0.5rem;
        padding: 0 0.5rem;
    }
    
    .table {
        min-width: 300px;
        margin-bottom: 0;
    }
    
    .table tbody th,
    .table tbody td {
        padding: 0.8rem 0.5rem !important;
        vertical-align: middle;
    }
    
    .table .h5 {
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    
    .badge-success-custom {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
        white-space: nowrap;
    }
    
    .ni-ui-04 {
        font-size: 1rem !important;
    }
    
    /* Fix for the results container background */
    .card {
        background: white;
        border-radius: 0.75rem;
    }
    
    /* Ensure the alert doesn't overflow */
    .alert {
        border-radius: 0.75rem;
    }
    
    /* Parameters text fix */
    .alert p.mb-0 {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem 0.5rem;
    }
    
    .alert p.mb-0 strong {
        display: inline-block;
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
    .alert.py-4 {
        padding: 0.8rem !important;
    }
    
    .alert h4 {
        font-size: 0.9rem;
    }
    
    .alert p {
        font-size: 0.75rem;
    }
    
    /* Card body fix */
    .card.shadow-lg.border-0 .card-body {
        padding: 1rem !important;
    }
    
    /* Table fixes */
    .table tbody th,
    .table tbody td {
        padding: 0.6rem 0.3rem !important;
    }
    
    .table .h5 {
        font-size: 0.8rem;
    }
    
    .badge-success-custom {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
    }
    
    hr.my-4 {
        margin: 1rem 0 !important;
    }
    
    h3.mb-4 {
        font-size: 1.1rem;
        margin-bottom: 1rem !important;
    }
    
    /* Loader scaling */
    .scanner-lens {
        width: 100px;
        height: 100px;
    }
    
    .loading-text {
        font-size: 0.9rem;
        letter-spacing: 2px;
    }
    
    /* Parameters text wrapping fix */
    .alert p.mb-0 {
        flex-direction: column;
        gap: 0.2rem;
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
    
    .col-md-4.mb-4 {
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
    
    /* Better table responsiveness */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Ensure alerts don't overflow */
    .alert {
        word-wrap: break-word;
        overflow-wrap: break-word;
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
            <div class="lens-center"></div>
        </div>
        <div class="loading-text">AI SCANNING...</div>
    </div>
    
<?php include ('fnav.php'); // Include navigation bar ?>

    <section id="hero-section">
        <div class="container hero-content">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1 class="display-1 font-weight-bolder mb-4">
                        Data-Driven Farming
                    </h1>
                    <p class="lead opacity-8 mb-5">
                        Get instant, accurate crop recommendations based on historical data and your specific farm location and season. Start making smarter decisions today.
                    </p>
                    
                    <a href="#prediction-tool" class="btn btn-accent-custom btn-lg btn-icon shadow-lg lift">
                        <span class="btn-inner--text">Start Prediction Now</span>
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
                        <h2 class="display-4 font-weight-bold" style="color: var(--color-primary-dark);">Select Your Farming Context</h2>
                    </div>

                    <div class="card shadow-lg border-light rounded-lg mb-5">
                        <div class="card-body p-lg-5">
                            <form id="prediction-form" role="form" action="#prediction-results" method="post">
                                
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="sts" style="color: var(--color-text-dark);">State</label>
                                            <select onchange="print_city('state', this.selectedIndex);" id="sts" name ="stt" class="form-control form-control-lg" required>
                                                <option value="">Select State</option>
                                            </select>
                                            <script language="javascript">print_state("sts");</script>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="state" style="color: var(--color-text-dark);">District</label>
                                            <select id ="state" name="district" class="form-control form-control-lg" required>
                                                <option value="">Select District</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label h6 text-uppercase" for="Season" style="color: var(--color-text-dark);">Season</label>
                                            <select id="Season" name="Season" class="form-control form-control-lg" required>
                                                <option value="">Select Season ...</option>
                                                <option value="Kharif">Kharif</option>
                                                <option value="Whole Year">Whole Year</option>
                                                <option value="Autumn">Autumn</option>
                                                <option value="Rabi">Rabi</option>
                                                <option value="Summer">Summer</option>
                                                <option value="Winter">Winter</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-5">
                                    <button type="submit" name="Crop_Predict" class="btn btn-primary-custom btn-lg btn-block btn-icon shadow-lg lift">
                                        <span class="btn-inner--icon"><i class="ni ni-check-bold"></i></span>
                                        <span class="btn-inner--text">Run Crop Prediction</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="text-center mb-5" id="prediction-results">
                        <span class="badge badge-pill mb-3 text-uppercase btn-accent-custom">Step 2: Results</span>
                        <h2 class="display-4 font-weight-bold" style="color: var(--color-primary-dark);">Predicted Recommendations</h2>
                    </div>

                    <div class="card shadow-lg border-0">
                        <div class="card-body p-lg-5">
                            
                            <?php 
                            if(isset($_POST['Crop_Predict'])){
                                $state=trim($_POST['stt']);
                                $district=trim($_POST['district']);
                                $season=trim($_POST['Season']);

                                $JsonState=json_encode($state);
                                $JsonDistrict=json_encode($district);
                                $JsonSeason=json_encode($season);
                                
                                $command = escapeshellcmd("python ML/crop_prediction/ZDecision_Tree_Model_Call.py $JsonState $JsonDistrict $JsonSeason");
                                $raw_output = shell_exec($command);
                                
                                $raw_output = trim(str_replace(['"', '[', ']'], '', $raw_output));
                                $crop_array = array_filter(array_map('trim', explode(',', $raw_output)));

                                echo '<div class="alert py-4" role="alert" style="background-color: var(--color-bg-light); border-left: 5px solid var(--color-accent-terracotta);"> ';
                                echo '<h4 class="alert-heading font-weight-bold" style="color: var(--color-text-dark);">Parameters Submitted:</h4>';
                                echo '<p class="mb-0" style="color: var(--color-text-dark);"><strong>State:</strong> '.$state.' | <strong>District:</strong> '.$district.' | <strong>Season:</strong> '.$season.'</p>';
                                echo '</div>';
                                
                                echo '<hr class="my-4">';
                                echo '<h3 class="mb-4 text-center" style="color: var(--color-secondary-green);">Crops Recommended for Maximum Yield:</h3>';

                                if (!empty($crop_array)) {
                                    echo '<div class="table-responsive">';
                                    echo '<table class="table align-items-center">';
                                    echo '<tbody>';
                                    
                                    foreach ($crop_array as $crop) {
                                        if (empty($crop)) continue;
                                        echo '<tr>';
                                        echo '<th scope="row" class="px-2 py-3" style="width: 50px;">';
                                        echo '<i class="ni ni-ui-04" style="font-size: 1.4rem; color: var(--color-secondary-green) !important;"></i>';
                                        echo '</th>';
                                        echo '<td class="py-3">';
                                        echo '<span class="h5 font-weight-bold" style="color: var(--color-text-dark);">' . htmlspecialchars($crop) . '</span>';
                                        echo '</td>';
                                        echo '<td class="text-right py-3">';
                                        echo '<span class="badge badge-pill badge-success-custom">Recommended</span>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                    echo '</tbody>';
                                    echo '</table>';
                                    echo '</div>';
                                } else {
                                    echo '<div class="alert text-center" role="alert" style="background-color: #f0f0f0;">';
                                    echo '<p class="lead mb-0" style="color: var(--color-text-dark);"><i class="ni ni-notification-70 mr-2"></i> No crops were predicted for the selected criteria. Please try different parameters.</p>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="alert text-center" role="alert" style="background-color: #f0f0f0;">';
                                echo '<p class="lead mb-0" style="color: var(--color-text-dark);"><i class="ni ni-notification-70 mr-2"></i> Select your farm details above and click "Run Crop Prediction" to see the results here.</p>';
                                echo '</div>';
                            }
                            ?>

                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>

    <?php require("footer.php"); // Include footer (scripts) ?>

    <script>
        // Trigger loader on submit
        document.getElementById('prediction-form').addEventListener('submit', function() {
            document.getElementById('loader-overlay').style.display = 'flex';
            window.scrollTo(0, 0); // Jump to top to see loader clearly
        });

        // Hide loader ONLY after the server response is completely loaded and window is ready
        window.addEventListener('load', function() {
            // Adding a slight delay (200ms) to ensure CSS is painted before hiding
            setTimeout(function() {
                document.getElementById('loader-overlay').style.display = 'none';
            }, 200);
        });
    </script>

</body>
</html>
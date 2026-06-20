<?php
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit();
} 

$query4 = "SELECT * from farmerlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['farmer_id'];
$para2 = $row4['farmer_name'];

// Function to get crop recommendation directly from Python model
function getCropRecommendation($n, $p, $k, $t, $h, $ph, $r) {
    // Correct path to Python script
    $script_path = __DIR__ . '/ML/crop_recommendation/frecommend.py';
    
    // Check if file exists
    if (!file_exists($script_path)) {
        error_log("Python script not found: " . $script_path);
        return "script_not_found";
    }
    
    // Build command
    $command = sprintf(
        'python "%s" %s %s %s %s %s %s %s 2>&1',
        $script_path,
        escapeshellarg($n),
        escapeshellarg($p),
        escapeshellarg($k),
        escapeshellarg($t),
        escapeshellarg($h),
        escapeshellarg($ph),
        escapeshellarg($r)
    );
    
    // Execute Python script
    $output = shell_exec($command);
    $output = trim($output);
    
    // Check if output is valid
    if ($output && !preg_match('/error|traceback|exception/i', $output)) {
        return $output;
    }
    
    // If error, log and return
    error_log("Crop recommendation failed: " . $output);
    return "prediction_error";
}

// Handle form submission
$recommended_crop = '';
if(isset($_POST['Crop_Recommend'])){
    $recommended_crop = getCropRecommendation(
        trim($_POST['n']),
        trim($_POST['p']),
        trim($_POST['k']),
        trim($_POST['t']),
        trim($_POST['h']),
        trim($_POST['ph']),
        trim($_POST['r'])
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include ('fheader.php'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --color-primary-dark: #0A3D0A;
            --color-accent-terracotta: #B85C38;
            --color-accent-terracotta-light: #d67d5a;
            --color-secondary-green: #4F772D;
            --color-bg-light: #F9F7F3;
            --color-text-dark: #1E293B;
        }

        body { 
            background-color: var(--color-bg-light); 
            color: var(--color-text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .hero-banner {
            background: url('../assets/img/weather4.jpg');
            background-size: cover;
            background-position: center;
            padding: 80px 0;
            color: white;
            border-bottom: 5px solid var(--color-accent-terracotta);
        }

        .hero-banner h1 {
            color: #FFFFFF !important;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
        }
        
        .hero-banner p {
            color: #FFFFFF !important;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.7);
        }

        .glass-card {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05); 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 
                        0 4px 6px -2px rgba(0, 0, 0, 0.05),
                        0 20px 25px -5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
        }

        .card-accent-top {
            border-top: 5px solid var(--color-secondary-green);
        }

        .form-label {
            font-weight: 700;
            color: var(--color-primary-dark);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            background-color: #ffffff;
        }

        .form-control:focus {
            border-color: var(--color-secondary-green);
            box-shadow: 0 0 0 3px rgba(79, 119, 45, 0.1);
            outline: none;
        }

        .btn-predict {
            background-color: var(--color-accent-terracotta);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(184, 92, 56, 0.3);
            transition: all 0.3s ease;
        }

        .btn-predict:hover {
            background-color: var(--color-accent-terracotta-light);
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 20px rgba(184, 92, 56, 0.5);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: var(--color-bg-light);
            color: var(--color-secondary-green);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        /* WIDER RESULT CONTAINER */
        .result-container {
            background: var(--color-primary-dark);
            color: #fff;
            border-radius: 20px;
            padding: 50px;
            margin-top: 40px;
            position: relative;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .leaf-decoration {
            position: absolute;
            bottom: -10px;
            right: 20px;
            font-size: 120px;
            color: rgba(255,255,255,0.1);
            transform: rotate(-15deg);
        }

        /* ADDED: Style for crop name - White color, symbol, and glow */
        .crop-name {
            color: #FFFFFF !important;
            text-shadow: 0 0 10px rgba(255,255,255,0.5), 0 0 20px rgba(255,255,255,0.3);
            display: inline-block;
        }
        
        .crop-symbol {
            margin-right: 15px;
            font-size: 1.2em;
            display: inline-block;
        }

        /* ADDED: Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* ========== RESPONSIVE MEDIA QUERIES ========== */
        
        /* Tablet Devices */
        @media (max-width: 992px) {
            .hero-banner {
                padding: 60px 0;
            }
            
            .hero-banner h1 {
                font-size: 2.2rem;
            }
            
            .hero-banner p {
                font-size: 1rem;
            }
            
            .container.mt-n5 {
                margin-top: -2rem !important;
            }
            
            .result-container {
                padding: 30px;
                margin-top: 30px;
            }
            
            .result-container .display-3 {
                font-size: 2.5rem;
            }
            
            .result-container .d-flex {
                gap: 20px;
            }
            
            .result-container img {
                width: 70px;
            }
            
            .leaf-decoration {
                font-size: 80px;
            }
        }
        
        /* Mobile Devices */
        @media (max-width: 768px) {
            .hero-banner {
                padding: 40px 0;
            }
            
            .hero-banner h1 {
                font-size: 1.6rem;
            }
            
            .hero-banner p {
                font-size: 0.85rem;
            }
            
            .container.mt-n5 {
                margin-top: -1rem !important;
            }
            
            .glass-card.p-4.p-md-5 {
                padding: 1.5rem !important;
            }
            
            .glass-card h3 {
                font-size: 1.3rem;
                text-align: center;
            }
            
            .form-label {
                font-size: 0.75rem;
            }
            
            .form-control {
                padding: 10px;
                font-size: 0.9rem;
            }
            
            .btn-predict {
                padding: 12px 30px;
                font-size: 0.95rem;
                width: 100%;
            }
            
            .btn-predict i {
                margin-left: 8px;
            }
            
            .row .col-md-4,
            .row .col-md-6 {
                margin-bottom: 0.8rem !important;
            }
            
            .result-container {
                padding: 20px;
            }
            
            .result-container h2 {
                font-size: 1.3rem;
                text-align: center;
            }
            
            .result-container .d-flex {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .result-container .mr-5 {
                margin-right: 0 !important;
            }
            
            .result-container .display-3 {
                font-size: 1.8rem;
            }
            
            .result-container p {
                font-size: 0.75rem;
            }
            
            .result-container img {
                width: 60px;
            }
            
            .leaf-decoration {
                font-size: 50px;
                bottom: 5px;
                right: 10px;
            }
            
            .container.py-5.mt-4 {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }
            
            .container.py-5.mt-4 h4 {
                font-size: 1.2rem;
                margin-bottom: 1.5rem !important;
            }
            
            .col-md-3 {
                margin-bottom: 1.5rem;
            }
            
            .col-md-3 i {
                font-size: 1.5rem !important;
            }
            
            .col-md-3 p {
                font-size: 0.8rem;
            }
            
            .col-md-3 strong {
                font-size: 0.9rem;
            }
        }
        
        /* Small Mobile Devices */
        @media (max-width: 480px) {
            .hero-banner {
                padding: 30px 0;
            }
            
            .hero-banner h1 {
                font-size: 1.3rem;
            }
            
            .hero-banner p {
                font-size: 0.75rem;
            }
            
            .glass-card.p-4.p-md-5 {
                padding: 1rem !important;
            }
            
            .glass-card h3 {
                font-size: 1.1rem;
            }
            
            .form-label {
                font-size: 0.7rem;
                margin-bottom: 5px;
            }
            
            .form-control {
                padding: 8px;
                font-size: 0.85rem;
            }
            
            .btn-predict {
                padding: 10px 20px;
                font-size: 0.85rem;
            }
            
            .result-container {
                padding: 15px;
            }
            
            .result-container h2 {
                font-size: 1.1rem;
            }
            
            .result-container .display-3 {
                font-size: 1.4rem;
            }
            
            .result-container img {
                width: 45px;
            }
            
            .leaf-decoration {
                font-size: 35px;
            }
            
            .container.py-5.mt-4 h4 {
                font-size: 1rem;
            }
            
            .col-md-3 i {
                font-size: 1.2rem !important;
            }
            
            .col-md-3 p {
                font-size: 0.7rem;
            }
            
            .col-md-3 strong {
                font-size: 0.8rem;
            }
            
            .input-group-text {
                padding: 0 8px;
            }
            
            .input-group-text i {
                font-size: 0.8rem;
            }
        }
        
        /* Landscape Mode for Mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .hero-banner {
                padding: 30px 0;
            }
            
            .result-container {
                padding: 20px;
            }
            
            .result-container .d-flex {
                flex-direction: row;
                text-align: left;
            }
            
            .col-md-4, .col-md-6 {
                margin-bottom: 0.5rem !important;
            }
        }
        
        /* Extra Large Desktop Screens */
        @media (min-width: 1400px) {
            .container {
                max-width: 1320px;
            }
            
            .result-container {
                max-width: 1200px;
            }
        }
        
        /* Fix for input group responsiveness */
        @media (max-width: 768px) {
            .input-group {
                flex-wrap: nowrap;
            }
            
            .input-group-prepend {
                margin-right: -1px;
            }
            
            .input-group-text {
                font-size: 0.8rem;
                padding: 0 10px;
            }
        }
    </style>
</head>

<body>
    <?php include ('fnav.php'); ?>

    <header class="hero-banner text-center">
        <div class="container">
            <h1 class="display-4 font-weight-bold">Precision Agriculture</h1>
            <p class="lead">Harnessing AI to suggest the perfect crop for your unique soil conditions.</p>
        </div>
    </header>

    <div class="container mt-n5">
        <div class="row">
            <div class="col-lg-4 d-none d-lg-block">
                <div class="glass-card p-4 mb-4">
                    <div class="feature-icon"><i class="fas fa-microscope fa-lg"></i></div>
                    <h5>Why Soil Data?</h5>
                    <p class="small text-muted">Nitrogen (N), Phosphorus (P), and Potassium (K) are vital macronutrients that determine crop health and yield potential.</p>
                </div>
                <div class="glass-card p-4 mb-4">
                    <div class="feature-icon"><i class="fas fa-cloud-sun-rain fa-lg"></i></div>
                    <h5>Climate Impact</h5>
                    <p class="small text-muted">Temperature and Rainfall patterns ensure the crop reaches its full growth cycle without stress.</p>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-card card-accent-top p-4 p-md-5">
                    <form role="form" action="#" method="post" id="predictionForm">
                        <h3 class="mb-4" style="color: var(--color-primary-dark)">Soil Parameter Input</h3>
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Nitrogen (N)</label>
                                <input type="number" name="n" placeholder="Ex: 90" required class="form-control">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Phosphorus (P)</label>
                                <input type="number" name="p" placeholder="Ex: 42" required class="form-control">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Potassium (K)</label>
                                <input type="number" name="k" placeholder="Ex: 43" required class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Temperature (°C)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-thermometer-half"></i></span></div>
                                    <input type="number" step="0.01" name="t" placeholder="Ex: 21" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Humidity (%)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-tint"></i></span></div>
                                    <input type="number" step="0.01" name="h" placeholder="Ex: 82" required class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Soil pH Level</label>
                                <input type="number" step="0.01" name="ph" placeholder="Ex: 6.5" required class="form-control">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Average Rainfall (mm)</label>
                                <input type="number" step="0.01" name="r" placeholder="Ex: 203" required class="form-control">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" value="Recommend" name="Crop_Recommend" class="btn btn-predict">
                                Get Best Crop Recommendation <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if(isset($_POST['Crop_Recommend'])): ?>
        <div class="row" id="resultsSection">
            <div class="col-12">
                <div class="result-container animate__animated animate__fadeInUp">
                    <i class="fas fa-leaf leaf-decoration"></i>
                    <h2 class="mb-3" style="color: var(--color-accent-terracotta)">AI Prediction Result</h2>
                    <hr style="border-top: 1px solid rgba(255,255,255,0.2)">
                    <div class="d-flex align-items-center mt-4">
                        <div class="mr-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/2328/2328437.png" width="100" alt="Crop Icon">
                        </div>
                        <div>
                            <p class="mb-1" style="opacity: 0.8; letter-spacing: 2px; font-weight: 600;">OPTIMAL CROP TO PLANT:</p>
                            <h1 class="display-3 font-weight-bold mb-0 crop-name"> 
                                <?php echo ucwords($recommended_crop); ?>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="container py-5 mt-4">
        <h4 class="text-center mb-5" style="color: var(--color-primary-dark)">Understanding the Parameters</h4>
        <div class="row text-center mt-4">
            <div class="col-md-3">
                <i class="fas fa-flask fa-2x mb-2" style="color: var(--color-accent-terracotta)"></i>
                <p><strong>N-P-K</strong><br><small>Essential nutrients for plant metabolism.</small></p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-vial fa-2x mb-2" style="color: var(--color-accent-terracotta)"></i>
                <p><strong>pH Level</strong><br><small>Acidity or alkalinity of your soil.</small></p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-sun fa-2x mb-2" style="color: var(--color-accent-terracotta)"></i>
                <p><strong>Environment</strong><br><small>Weather factors affecting growth.</small></p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-chart-line fa-2x mb-2" style="color: var(--color-accent-terracotta)"></i>
                <p><strong>Yield</strong><br><small>Expected productivity based on data.</small></p>
            </div>
        </div>
    </div>

    <?php require("footer.php");?>

    <!-- ADDED: Auto-scroll script -->
    <?php if(isset($_POST['Crop_Recommend'])): ?>
    <script>
        // Smooth scroll to results section when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const resultsSection = document.getElementById('resultsSection');
            if (resultsSection) {
                // Small delay to ensure page is fully loaded
                setTimeout(function() {
                    resultsSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start',
                        inline: 'nearest'
                    });
                }, 100);
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
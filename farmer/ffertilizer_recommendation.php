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

// Function to get fertilizer recommendation directly from Python model
function getFertilizerRecommendation($n, $p, $k, $t, $h, $sm, $soil, $crop) {
    // Path to Python script
    $script_path = __DIR__ . '/ML/fertilizer_recommendation/frecommend_fertilizer.py';
    
    // Check if file exists
    if (!file_exists($script_path)) {
        error_log("Python script not found: " . $script_path);
        return "script_not_found";
    }
    
    // Build command - FIXED: Remove extra quotes around soil and crop
    $command = sprintf(
        'python "%s" %s %s %s %s %s %s %s %s 2>&1',
        $script_path,
        escapeshellarg($n),
        escapeshellarg($p),
        escapeshellarg($k),
        escapeshellarg($t),
        escapeshellarg($h),
        escapeshellarg($sm),
        escapeshellarg($soil),
        escapeshellarg($crop)
    );
    
    // Execute Python script
    $output = shell_exec($command);
    $output = trim($output);
    
    // Check if output is valid
    if ($output && !preg_match('/error|traceback|exception|usage/i', $output)) {
        return $output;
    }
    
    // If error, log and return
    error_log("Fertilizer recommendation failed: " . $output);
    return "prediction_error";
}

// Handle form submission
$recommended_fertilizer = '';
if(isset($_POST['Fert_Recommend'])){
    $recommended_fertilizer = getFertilizerRecommendation(
        trim($_POST['n']),
        trim($_POST['p']),
        trim($_POST['k']),
        trim($_POST['t']),
        trim($_POST['h']),
        trim($_POST['soilMoisture']),
        trim($_POST['soil']),
        trim($_POST['crop'])
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
            background: url('../assets/img/rain5.jpg');
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

        /* ADDED: Style for fertilizer name - White color, symbol, and glow */
        .fertilizer-name {
            color: #FFFFFF !important;
            text-shadow: 0 0 10px rgba(255,255,255,0.5), 0 0 20px rgba(255,255,255,0.3);
            display: inline-block;
        }
        
        .fertilizer-symbol {
            margin-right: 15px;
            font-size: 1.2em;
            display: inline-block;
        }

        /* ADDED: Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* ========== RESPONSIVE MEDIA QUERIES ========== */
        
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
            .leaf-decoration {
                font-size: 80px;
            }
        }
        
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
            select.form-control {
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
            .col-md-4 {
                margin-bottom: 1.5rem;
            }
            .col-md-4 i {
                font-size: 1.5rem !important;
            }
            .col-md-4 p {
                font-size: 0.8rem;
            }
            .col-md-4 strong {
                font-size: 0.9rem;
            }
        }
        
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
            select.form-control {
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
            .leaf-decoration {
                font-size: 35px;
            }
            .container.py-5.mt-4 h4 {
                font-size: 1rem;
            }
            .col-md-4 i {
                font-size: 1.2rem !important;
            }
            .col-md-4 p {
                font-size: 0.7rem;
            }
            .col-md-4 strong {
                font-size: 0.8rem;
            }
        }
        
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
        
        @media (min-width: 1400px) {
            .container {
                max-width: 1320px;
            }
            .result-container {
                max-width: 1200px;
            }
        }
        
        @media (max-width: 768px) {
            option {
                font-size: 0.85rem;
            }
            select.form-control {
                min-height: 44px;
            }
            .btn-predict {
                min-height: 48px;
            }
        }
        
        @media (max-width: 576px) {
            .result-container {
                margin-left: 10px;
                margin-right: 10px;
                width: calc(100% - 20px);
            }
        }
    </style>
</head>

<body>
    <?php include ('fnav.php'); ?>

    <header class="hero-banner text-center">
        <div class="container">
            <h1 class="display-4 font-weight-bold">Fertilizer Optimization</h1>
            <p class="lead">Advanced nutrient balancing to boost your soil productivity and crop yield.</p>
        </div>
    </header>

    <div class="container mt-n5">
        <div class="row">
            <div class="col-lg-4 d-none d-lg-block">
                <div class="glass-card p-4 mb-4">
                    <div class="feature-icon"><i class="fas fa-flask fa-lg"></i></div>
                    <h5>Nutrient Balance</h5>
                    <p class="small text-muted">Maintaining the right N-P-K ratio prevents soil degradation and ensures your plants get exactly what they need.</p>
                </div>
                <div class="glass-card p-4 mb-4">
                    <div class="feature-icon"><i class="fas fa-seedling fa-lg"></i></div>
                    <h5>Specific Recommendations</h5>
                    <p class="small text-muted">Different crops like Maize or Wheat require specific fertilizer types based on current soil moisture and climate.</p>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-card card-accent-top p-4 p-md-5">
                    <form role="form" action="#" method="post" id="fertilizerForm">
                        <h3 class="mb-4" style="color: var(--color-primary-dark)">Soil & Crop Input</h3>
                        
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Nitrogen (N)</label>
                                <input type="number" name="n" placeholder="Ex: 37" required class="form-control">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Phosphorus (P)</label>
                                <input type="number" name="p" placeholder="Ex: 0" required class="form-control">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Potassium (K)</label>
                                <input type="number" name="k" placeholder="Ex: 0" required class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Temperature (°C)</label>
                                <input type="number" name="t" placeholder="Ex: 26" required class="form-control">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Humidity (%)</label>
                                <input type="number" name="h" placeholder="Ex: 52" required class="form-control">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Soil Moisture</label>
                                <input type="number" name="soilMoisture" placeholder="Ex: 38" required class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Soil Type</label>
                                <select name="soil" class="form-control" required>
                                    <option value="">Select Soil Type</option>
                                    <option value="Sandy">Sandy</option>
                                    <option value="Loamy">Loamy</option>
                                    <option value="Black">Black</option>
                                    <option value="Red">Red</option>
                                    <option value="Clayey">Clayey</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Crop Name</label>
                                <select name="crop" class="form-control" required>
                                    <option value="">Select Crop</option>
                                    <option value="Maize">Maize</option>
                                    <option value="Sugarcane">Sugarcane</option>
                                    <option value="Cotton">Cotton</option>
                                    <option value="Tobacco">Tobacco</option>
                                    <option value="Paddy">Paddy</option>
                                    <option value="Barley">Barley</option>
                                    <option value="Wheat">Wheat</option>
                                    <option value="Millets">Millets</option>
                                    <option value="Oil seeds">Oil seeds</option>
                                    <option value="Pulses">Pulses</option>
                                    <option value="Ground Nuts">Ground Nuts</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" value="Recommend" name="Fert_Recommend" class="btn btn-predict">
                                Get Fertilizer Recommendation <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if(isset($_POST['Fert_Recommend'])): ?>
        <div class="row" id="resultsSection">
            <div class="col-12">
                <div class="result-container animate__animated animate__fadeInUp">
                    <i class="fas fa-vial leaf-decoration"></i>
                    <h2 class="mb-3" style="color: var(--color-accent-terracotta)">AI Recommendation Result</h2>
                    <hr style="border-top: 1px solid rgba(255,255,255,0.2)">
                    <div class="d-flex align-items-center mt-4">
                        <div class="mr-5 d-none d-md-block">
                            <img src="https://cdn-icons-png.flaticon.com/512/4207/4207253.png" width="100" alt="Fertilizer Icon">
                        </div>
                        <div>
                            <p class="mb-1" style="opacity: 0.8; letter-spacing: 2px; font-weight: 600;">RECOMMENDED FERTILIZER:</p>
                            <h1 class="display-3 font-weight-bold mb-0 fertilizer-name"> 
                                <?php 
                                    if($recommended_fertilizer && $recommended_fertilizer != 'prediction_error') {
                                        echo ucwords($recommended_fertilizer);
                                    } else {
                                        echo "Unable to predict";
                                    }
                                ?>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="container py-5 mt-4">
        <h4 class="text-center mb-5" style="color: var(--color-primary-dark)">Soil Health Indicators</h4>
        <div class="row text-center mt-4">
            <div class="col-md-4">
                <i class="fas fa-tint fa-2x mb-2" style="color: var(--color-accent-terracotta)"></i>
                <p><strong>Moisture</strong><br><small>Crucial for fertilizer absorption.</small></p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-mountain fa-2x mb-2" style="color: var(--color-accent-terracotta)"></i>
                <p><strong>Soil Type</strong><br><small>Affects the drainage and nutrient retention.</small></p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-check-circle fa-2x mb-2" style="color: var(--color-accent-terracotta)"></i>
                <p><strong>Precision</strong><br><small>Reduces wastage and saves cost.</small></p>
            </div>
        </div>
    </div>

    <?php require("footer.php");?>

    <?php if(isset($_POST['Fert_Recommend'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resultsSection = document.getElementById('resultsSection');
            if (resultsSection) {
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
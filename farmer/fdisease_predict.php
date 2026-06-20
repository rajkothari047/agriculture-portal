<?php
include('fsession.php');
if (!isset($_SESSION['farmer_login_user'])) header("location: ../index.php");

$prediction = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['leaf_image']) && $_FILES['leaf_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $tmp_name = $_FILES['leaf_image']['tmp_name'];
        $filename = time() . '_' . basename($_FILES['leaf_image']['name']);
        $filepath = $upload_dir . $filename;
        move_uploaded_file($tmp_name, $filepath);

        $crop_name = $_POST['crop_name'] ?? '';
        $planting_date = $_POST['planting_date'] ?? '';
        $disease_date = $_POST['disease_date'] ?? '';

        $days_diff = '';
        if ($planting_date && $disease_date) {
            $diff = strtotime($disease_date) - strtotime($planting_date);
            $days_diff = ceil($diff / (60 * 60 * 24));
        }

        $predict_script = __DIR__ . '/ML/DiseaseDetection/predict.py';
        $cmd = escapeshellcmd("python \"$predict_script\" \"$filepath\" \"$crop_name\"");
        $output = shell_exec($cmd);

        $parts = explode(",", $output);
        $predicted_disease = isset($parts[0]) ? trim($parts[0]) : "Prediction failed";
        $accuracy = isset($parts[1]) ? trim($parts[1]) : 0;
        $ai_explanation = isset($parts[2]) ? trim($parts[2]) : "No output from AI model.";

        $json_file = __DIR__ . '/ML/DiseaseDetection/disease_data.json';
        if (file_exists($json_file)) {
            $json_data = json_decode(file_get_contents($json_file), true);
            $disease_info = $json_data[$predicted_disease] ?? [];
        } else {
            $disease_info = [];
        }

        $severity_label = $disease_info['severity'] ?? 'N/A';
        
        $status_color = "#4F772D"; 
        if($severity_label == 'High') $status_color = "#B85C38";
        if($severity_label == 'Critical') $status_color = "#dc3545";

        $prediction = [
            'image' => $filepath,
            'crop' => $crop_name,
            'disease' => str_replace('_', ' ', $predicted_disease),
            'accuracy' => $accuracy,
            'ai_explanation' => $ai_explanation,
            'definition' => $disease_info['definition'] ?? 'N/A',
            'symptoms' => $disease_info['symptoms'] ?? 'N/A',
            'causes' => $disease_info['causes'] ?? 'N/A',
            'treatment_steps' => $disease_info['cure_steps'] ?? 'N/A',
            'pesticide' => $disease_info['pesticide_schedule'] ?? 'N/A',
            'fertilizer' => $disease_info['fertilizer_schedule'] ?? 'N/A',
            'water_routine' => $disease_info['water_schedule'] ?? 'N/A',
            'safety' => $disease_info['preventive_measures'] ?? 'N/A',
            'recovery_rate' => $disease_info['expected_recovery'] ?? 'N/A',
            'severity' => $severity_label,
            'status_color' => $status_color,
            'estimated_cost' => $disease_info['treatment_cost'] ?? 'N/A',
            'planting_date' => $planting_date,
            'disease_date' => $disease_date,
            'days_diff' => $days_diff
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include ('fheader.php'); ?>
<style>
:root {
    --color-primary-dark: #0A3D0A;
    --color-accent-terracotta: #B85C38;
    --color-secondary-green: #4F772D;
    --color-bg-light: #F9F7F3;
    --color-text-dark: #1E293B;
}

body {
    background-color: var(--color-bg-light) !important;
    font-family: 'Open Sans', sans-serif;
    color: var(--color-text-dark);
}

.status-badge { padding: 5px 15px; border-radius: 50px; color: white !important; font-weight: bold; font-size: 0.8rem; text-transform: uppercase; }

.section-title { 
    border-left: 5px solid var(--color-accent-terracotta); 
    padding-left: 15px; 
    color: var(--color-primary-dark); 
    font-weight: 700; 
    margin-bottom: 20px; 
    text-transform: uppercase; 
}

.info-box { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 20px; transition: 0.3s; height: 100%; }
.info-box:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

.result-header { 
    background-color: #6c757d !important; 
    padding: 40px 0; 
}

.header-white-text {
    color: #ffffff !important;
}

.clinical-header {
    color: var(--color-secondary-green) !important;
    font-weight: bold;
}

.leaf-img-frame {
    padding: 15px;
    border-radius: 25px;
    background: white;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    overflow: hidden;
    width: 100%;
}

.leaf-img-frame img {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 20px;
}

/* Button Hover Effects */
.btn-terracotta {
    background-color: var(--color-accent-terracotta) !important;
    color: white !important;
    border: none;
    transition: all 0.3s ease;
}

.btn-terracotta:hover {
    background-color: #9c4a2a !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(184, 92, 56, 0.4) !important;
}

.btn-neem {
    background-color: var(--color-primary-dark) !important;
    color: white !important;
    border: none;
    transition: all 0.3s ease;
}

.btn-neem:hover {
    background-color: var(--color-secondary-green) !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(10, 61, 10, 0.4) !important;
}

.text-accent-icon { color: var(--color-accent-terracotta) !important; }

.bg-white-transparent {
    background: rgba(255,255,255,0.1);
}

/* ========== RESPONSIVE MEDIA QUERIES (ADDED ONLY) ========== */

/* Large Tablets and Small Desktops */
@media (max-width: 992px) {
    .result-header {
        padding: 30px 0;
    }
    
    .result-header .display-4 {
        font-size: 2rem !important;
    }
    
    .result-header .h4 {
        font-size: 1.1rem !important;
    }
    
    .leaf-img-frame {
        padding: 10px;
        margin-bottom: 20px;
    }
    
    .col-md-4.px-lg-5,
    .col-md-8.pr-lg-5 {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .py-5 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }
}

/* Tablets and Mobile Devices */
@media (max-width: 768px) {
    .result-header {
        padding: 25px 0;
    }
    
    .result-header .display-4 {
        font-size: 1.5rem !important;
    }
    
    .result-header .h4 {
        font-size: 0.9rem !important;
    }
    
    .d-flex.align-items-center.mb-2 {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .status-badge {
        font-size: 0.7rem;
        padding: 3px 10px;
    }
    
    .row.no-gutters.bg-white-transparent {
        margin-top: 15px;
    }
    
    .row.no-gutters .col-4 {
        padding: 0 5px !important;
    }
    
    .row.no-gutters .col-4 small {
        font-size: 0.6rem;
    }
    
    .row.no-gutters .col-4 strong {
        font-size: 0.85rem;
    }
    
    /* Section titles */
    .section-title {
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
    
    .info-box {
        padding: 15px;
    }
    
    .info-box .lead {
        font-size: 0.95rem;
    }
    
    .clinical-header {
        font-size: 0.9rem;
    }
    
    .info-box p.small {
        font-size: 0.8rem;
    }
    
    /* AI Explanation */
    .mb-5 .p-3 {
        padding: 12px !important;
        font-size: 0.85rem;
    }
    
    /* Treatment Plan Card */
    .card-body.p-4 {
        padding: 1rem !important;
    }
    
    .card-body .mb-4 h6 {
        font-size: 0.7rem;
    }
    
    .card-body .mb-4 span {
        font-size: 0.85rem;
    }
    
    .card-body .p-3 {
        padding: 0.8rem !important;
    }
    
    .card-body .p-3 h6 {
        font-size: 0.8rem;
    }
    
    .card-body .p-3 p {
        font-size: 0.75rem;
    }
    
    /* Alert box */
    .alert.shadow-sm {
        flex-direction: column;
        text-align: center;
        padding: 1rem !important;
    }
    
    .alert.shadow-sm .icon-shape {
        margin-right: 0 !important;
        margin-bottom: 10px;
    }
    
    .alert.shadow-sm h5 {
        font-size: 0.9rem;
    }
    
    .alert.shadow-sm p {
        font-size: 0.75rem;
    }
    
    /* Buttons */
    .text-center.mt-5 {
        margin-top: 1.5rem !important;
    }
    
    .btn-terracotta.px-5,
    .btn-neem.px-5 {
        padding: 0.6rem 1rem !important;
        font-size: 0.8rem;
        margin: 5px !important;
        width: 100%;
        max-width: 200px;
    }
    
    hr.my-5 {
        margin: 1.5rem 0 !important;
    }
    
    .container.py-7 {
        padding: 3rem 1rem !important;
    }
}

/* Small Mobile Devices */
@media (max-width: 480px) {
    .result-header {
        padding: 20px 0;
    }
    
    .result-header .display-4 {
        font-size: 1.2rem !important;
    }
    
    .result-header .h4 {
        font-size: 0.75rem !important;
    }
    
    .status-badge {
        font-size: 0.6rem;
        padding: 2px 8px;
    }
    
    .row.no-gutters .col-4 small {
        font-size: 0.5rem;
    }
    
    .row.no-gutters .col-4 strong {
        font-size: 0.7rem;
    }
    
    .section-title {
        font-size: 1rem;
        padding-left: 10px;
    }
    
    .info-box {
        padding: 12px;
    }
    
    .info-box .lead {
        font-size: 0.85rem;
    }
    
    .clinical-header {
        font-size: 0.8rem;
    }
    
    .info-box p.small {
        font-size: 0.7rem;
    }
    
    .mb-5 .p-3 {
        font-size: 0.75rem;
    }
    
    .card-body .mb-4 span {
        font-size: 0.75rem;
    }
    
    .btn-terracotta.px-5,
    .btn-neem.px-5 {
        padding: 0.5rem 0.8rem !important;
        font-size: 0.7rem;
        max-width: 170px;
    }
    
    .leaf-img-frame {
        padding: 8px;
    }
}

/* Landscape Mode for Mobile */
@media (max-width: 768px) and (orientation: landscape) {
    .result-header {
        padding: 15px 0;
    }
    
    .leaf-img-frame {
        max-width: 200px;
        margin: 0 auto;
    }
    
    .result-header .display-4 {
        font-size: 1.3rem !important;
    }
    
    .row.no-gutters.bg-white-transparent {
        margin-top: 10px;
    }
    
    .py-5 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    .mb-5 {
        margin-bottom: 1rem !important;
    }
}

/* Extra Large Desktop Screens */
@media (min-width: 1400px) {
    .container {
        max-width: 1320px;
    }
    
    .result-header .display-4 {
        font-size: 3rem;
    }
}

/* Fix for no diagnosis section */
@media (max-width: 768px) {
    .container.py-7.text-center .col-lg-6 {
        padding: 0 1rem;
    }
    
    .container.py-7.text-center h2 {
        font-size: 1.3rem;
    }
    
    .container.py-7.text-center p.lead {
        font-size: 0.9rem;
    }
    
    .container.py-7.text-center .btn-neem {
        font-size: 0.85rem;
        padding: 0.6rem 1.2rem;
    }
}

/* Fix for icon shapes */
@media (max-width: 768px) {
    .icon-shape {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-shape i {
        font-size: 1.2rem;
    }
}

/* Ensure proper spacing for treatment plan */
@media (max-width: 768px) {
    .col-lg-7,
    .col-lg-5 {
        margin-bottom: 1.5rem;
    }
    
    .col-lg-7 .mb-5:last-child {
        margin-bottom: 0 !important;
    }
}
</style>

<body>
<?php include ('fnav.php'); ?>

<?php if($prediction): ?>
<header class="result-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-4 px-lg-5">
                <div class="leaf-img-frame">
                    <img src="<?= $prediction['image'] ?>" alt="Leaf Image">
                </div>
            </div>

            <div class="col-md-8 pr-lg-5">
                <div class="d-flex align-items-center mb-2">
                    <span class="status-badge mr-3" style="background: <?= $prediction['status_color'] ?>;">
                        <?= $prediction['severity'] ?> Severity
                    </span>
                    <h6 class="mb-0 text-uppercase header-white-text" style="opacity: 0.9;">Diagnostic ID: #<?= rand(1000, 9999) ?></h6>
                </div>
                <h1 class="display-4 font-weight-bold mb-1 header-white-text"><?= $prediction['disease'] ?></h1>
                <p class="h4 mb-4 header-white-text" style="opacity: 0.9;">Target Crop: <?= $prediction['crop'] ?> • Analysis Confidence: <?= $prediction['accuracy'] ?>%</p>

                <div class="row no-gutters bg-white-transparent p-3 rounded">
                    <div class="col-4 border-right border-white-50 px-3 text-center header-white-text">
                        <small class="d-block text-uppercase">Growth Day</small>
                        <strong>Day <?= $prediction['days_diff'] ?></strong>
                    </div>
                    <div class="col-4 border-right border-white-50 px-3 text-center header-white-text">
                        <small class="d-block text-uppercase">Recovery Chance</small>
                        <strong><?= $prediction['recovery_rate'] ?></strong>
                    </div>
                    <div class="col-4 px-3 text-center header-white-text">
                        <small class="d-block text-uppercase">Est. Cost</small>
                        <strong>$<?= $prediction['estimated_cost'] ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="mb-5">
                    <h4 class="section-title">Clinical Overview</h4>
                    <div class="info-box shadow-sm">
                        <p class="lead font-weight-bold"><?= $prediction['definition'] ?></p>
                        <hr>
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <h6 class="clinical-header"><i class="ni ni-zoom-split-in mr-2"></i>Primary Symptoms</h6>
                                <p class="small"><?= $prediction['symptoms'] ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="clinical-header"><i class="ni ni-bulb-61 mr-2"></i>Root Causes</h6>
                                <p class="small"><?= $prediction['causes'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <h4 class="section-title">AI Logic Explanation</h4>
                    <div class="p-3 rounded" style="font-style: italic; background-color: #343a40; color: white; border-left: 5px solid var(--color-accent-terracotta);">
                        "<?= $prediction['ai_explanation'] ?>"
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <h4 class="section-title">Treatment Plan</h4>
                <div class="card border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-uppercase text-muted small">Pesticide / Bactericide</h6>
                            <div class="d-flex align-items-center">
                                <i class="ni ni-flask-04 text-accent-icon mr-3 h4 mb-0"></i>
                                <span class="text-dark font-weight-bold"><?= $prediction['pesticide'] ?></span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-uppercase text-muted small">Fertilizer Adjustment</h6>
                            <div class="d-flex align-items-center">
                                <i class="ni ni-delivery-fast text-accent-icon mr-3 h4 mb-0"></i>
                                <span class="text-dark font-weight-bold"><?= $prediction['fertilizer'] ?></span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-uppercase text-muted small">Watering Protocol</h6>
                            <div class="d-flex align-items-center">
                                <i class="ni ni-droplet text-accent-icon mr-3 h4 mb-0"></i>
                                <span class="text-dark font-weight-bold"><?= $prediction['water_routine'] ?></span>
                            </div>
                        </div>
                        <div class="p-3 bg-success-light rounded" style="background: #e8f5e9;">
                            <h6 class="text-success font-weight-bold mb-1"><i class="ni ni-check-bold mr-2"></i>Next Steps:</h6>
                            <p class="small mb-0 text-dark"><?= $prediction['treatment_steps'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-5">

        <div class="alert shadow-sm border-0 d-flex align-items-center p-4" style="background: white; border-radius: 15px;">
            <div class="icon icon-shape text-white rounded-circle shadow mr-4" style="background-color: var(--color-accent-terracotta) !important;">
                <i class="ni ni-shield-check"></i>
            </div>
            <div>
                <h5 class="mb-0 font-weight-bold" style="color: var(--color-primary-dark);">Preventive Measures for Next Season</h5>
                <p class="mb-0 text-muted"><?= $prediction['safety'] ?></p>
            </div>
        </div>

        <div class="text-center mt-5">
            <button onclick="window.print()" class="btn btn-terracotta px-5 btn-lg shadow">
                <i class="ni ni-cloud-download-95 mr-2"></i> Download Full PDF Report
            </button>
            <a href="disease_detection.php" class="btn btn-neem px-5 btn-lg ml-md-3 shadow">
                New Scan
            </a>
        </div>
    </div>
</section>

<?php else: ?>
<div class="container py-7 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <i class="ni ni-active-40 display-1 text-muted mb-4"></i>
            <h2 class="font-weight-bold">No Diagnosis Found</h2>
            <p class="lead">We couldn't process the image. Please ensure you are uploading a clear photo of the leaf.</p>
            <a href="fdisease.php" class="btn btn-neem btn-lg mt-4">Return to Scanner</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include('footer.php'); ?>
</body>
</html>
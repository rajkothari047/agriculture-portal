<?php
include('fsession.php');
if (!isset($_SESSION['farmer_login_user'])) header("location: ../index.php");
?>

<!DOCTYPE html>
<html lang="en">
<?php include ('fheader.php'); ?>

<style>
/* ----------------------------------------------------------- */
/* 1. BRANDING & COLORS                                        */
/* ----------------------------------------------------------- */
:root {
    --color-primary-dark: #0A3D0A; /* Deep Neem Green */
    --color-accent-terracotta: #B85C38; /* Rustic Terracotta */
    --color-secondary-green: #4F772D; /* Lush Field Green */
    --color-bg-light: #F9F7F3; /* Warm Cream Background */
    --color-text-dark: #1E293B; /* Slate Dark */
}

body {
    background-color: var(--color-bg-light) !important;
    color: var(--color-text-dark);
}

/* ----------------------------------------------------------- */
/* 2. PROFESSIONAL AI SCANNER LOADING SCREEN (NEW ANIMATION)   */
/* ----------------------------------------------------------- */
#loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, #0D4721 0%, #051A0B 100%);
    display: none; 
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    color: white;
}

/* High-Tech Lens Focus Animation */
.scanner-lens {
    position: relative;
    width: 150px;
    height: 150px;
    display: flex;
    justify-content: center;
    align-items: center;
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
    width: 15px;
    height: 15px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 0 20px #fff, 0 0 40px var(--color-secondary-green);
    animation: pulse-center 1s ease-in-out infinite alternate;
}

@keyframes rotate-lens {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes pulse-center {
    from { transform: scale(0.8); opacity: 0.5; }
    to { transform: scale(1.2); opacity: 1; }
}

.loading-text-container {
    text-align: center;
}

.loading-text {
    font-size: 1.8rem;
    font-weight: 800;
    letter-spacing: 5px;
    text-transform: uppercase;
    margin-bottom: 5px;
    color: #ffffff;
}

.sub-loading-text {
    font-family: 'Courier New', Courier, monospace;
    font-size: 1rem;
    color: #8db38b;
    letter-spacing: 1px;
}

.dots::after {
    content: '';
    animation: dots-anim 1.5s infinite;
}
@keyframes dots-anim {
    0% { content: ''; }
    33% { content: '.'; }
    66% { content: '..'; }
    100% { content: '...'; }
}

/* ----------------------------------------------------------- */
/* 3. ORIGINAL PAGE STYLES                                     */
/* ----------------------------------------------------------- */
.btn-primary-custom {
    background-color: var(--color-primary-dark);
    border-color: var(--color-primary-dark);
    color: white;
}
.btn-primary-custom:hover {
    background-color: var(--color-secondary-green);
    transform: translateY(-2px);
}

.btn-accent-custom {
    background-color: var(--color-accent-terracotta);
    border-color: var(--color-accent-terracotta);
    color: white;
}
.btn-accent-custom:hover {
    background-color: #9E4D31;
    color: white;
}

.badge-success-custom {
    background-color: var(--color-secondary-green) !important;
    color: white !important;
}

#hero-section {
    position: relative;
    height: 100vh; 
    width: 100%;
    background-image: url('../assets/img/dd.jpg'); 
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

.lift {
    transition: all .15s ease-in-out;
}
.lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 1rem 2.5rem rgba(0,0,0,.1) !important;
}

.detection-box {
    border: 2px dashed #e0e0e0;
    border-radius: 15px;
    background: #ffffff;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 280px; 
    padding: 2rem;
}

#drop-zone {
    border-color: var(--color-secondary-green);
    cursor: pointer;
}

#drop-zone.dragover {
    background-color: #f1f8e9;
    border-style: solid;
}

#days-display-box {
    border-style: solid; 
    border-color: #e9ecef;
}

#preview {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    display: none;
    margin-top: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
    
    .col-lg-11 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .detection-box {
        min-height: 240px;
        padding: 1.5rem;
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
    .col-md-4.mb-4,
    .col-md-8.mb-4 {
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
    
    /* Step 1 Badge Alignment Fix */
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
    
    /* Detection boxes */
    .detection-box {
        min-height: 200px;
        padding: 1rem;
    }
    
    .detection-box i {
        font-size: 2rem !important;
    }
    
    .detection-box p.h5 {
        font-size: 1rem;
    }
    
    #days_diff {
        font-size: 2rem !important;
    }
    
    #days-display-box {
        min-height: auto;
    }
    
    /* Drop zone */
    #drop-zone {
        min-height: 200px;
    }
    
    #preview {
        max-height: 150px;
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
    
    /* Detection boxes for small mobile */
    .detection-box {
        min-height: 160px;
        padding: 0.8rem;
    }
    
    .detection-box i {
        font-size: 1.5rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    .detection-box p.h5 {
        font-size: 0.85rem;
    }
    
    .detection-box p.small {
        font-size: 0.65rem;
    }
    
    #days_diff {
        font-size: 1.5rem !important;
    }
    
    #drop-zone {
        min-height: 160px;
    }
    
    #upload-instructions i {
        font-size: 1.5rem !important;
    }
    
    #preview {
        max-height: 120px;
    }
    
    /* Step 1 container alignment */
    .text-center.mb-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    /* Loader scaling */
    .scanner-lens {
        width: 100px;
        height: 100px;
    }
    
    .loading-text {
        font-size: 1.2rem;
        letter-spacing: 3px;
    }
    
    .sub-loading-text {
        font-size: 0.8rem;
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
    
    .col-md-4.mb-4,
    .col-md-8.mb-4 {
        margin-bottom: 0.5rem !important;
    }
    
    .card-body.p-lg-5 {
        padding: 1rem !important;
    }
    
    .detection-box {
        min-height: 150px;
    }
    
    .row.mt-2 {
        margin-top: 0 !important;
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

/* Fix for form elements on mobile */
@media (max-width: 768px) {
    select.form-control,
    input.form-control {
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
    
    /* Date inputs */
    input[type="date"] {
        min-height: 44px;
    }
    
    /* Drop zone improvements */
    #drop-zone {
        cursor: pointer;
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

/* Fix for detection box alignment on mobile */
@media (max-width: 768px) {
    .detection-box {
        width: 100%;
        box-sizing: border-box;
    }
    
    #days-display-box {
        margin-bottom: 0;
    }
    
    .row.mt-2 .col-md-4 {
        margin-bottom: 0.8rem;
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
            <div class="loading-text">RUNNING AI DIAGNOSIS<span class="dots"></span></div>
            <div id="dynamic-subtext" class="sub-loading-text">Initializing neural scan...</div>
        </div>
    </div>
    
<?php include ('fnav.php'); ?>

    <section id="hero-section">
        <div class="container hero-content">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1 class="display-1 font-weight-bolder mb-4">Plant Doctor AI</h1>
                    <p class="lead opacity-8 mb-5">
                        Identify crop diseases instantly with our advanced neural network analysis. 
                        Upload a photo of the infected leaf to get an immediate diagnosis and treatment plan.
                    </p>
                    
                    <a href="#prediction-form" class="btn btn-accent-custom btn-lg btn-icon shadow-lg lift">
                        <span class="btn-inner--text">Start New Scan</span>
                        <span class="btn-inner--icon"><i class="ni ni-bold-down"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="prediction-form" class="py-7">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    
                    <div class="text-center mb-5">
                        <span class="badge badge-pill mb-3 text-uppercase badge-success-custom">Step 1: Data Collection</span>
                        <h2 class="display-4 font-weight-bold" style="color: var(--color-primary-dark);">Diagnostic Parameters</h2>
                    </div>

                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-body p-lg-5">
                            <form id="ml-prediction-form" action="fdisease_predict.php" method="POST" enctype="multipart/form-data">
                                
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <label class="form-control-label h6 text-uppercase">Target Crop</label>
                                        <select name="crop_name" class="form-control form-control-lg" required>
                                            <option value="">--Select Crop--</option>
                                            <option value="Grapevine">Grapevine</option>
                                            <option value="Corn">Corn</option>
                                            <option value="Tomato">Tomato</option>
                                            <option value="Potato">Potato</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-control-label h6 text-uppercase">Planting Date</label>
                                        <input type="date" id="planting_date" name="planting_date" class="form-control form-control-lg" required>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-control-label h6 text-uppercase">Infection Noticed</label>
                                        <input type="date" id="disease_date" name="disease_date" class="form-control form-control-lg" required>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-4 mb-4">
                                        <label class="form-control-label h6 text-uppercase">Crop Age Analysis</label>
                                        <div id="days-display-box" class="detection-box">
                                            <i class="ni ni-calendar-grid-58 mb-3" style="font-size: 3.5rem; color: var(--color-accent-terracotta);"></i>
                                            <input type="text" id="days_diff" readonly 
                                                   style="border: none; background: transparent; text-align: center; font-size: 3rem; font-weight: 800; color: var(--color-primary-dark); width: 100%;" 
                                                   placeholder="0 Days">
                                            <p class="small text-uppercase font-weight-bold mt-2" style="letter-spacing: 1px; color: #8898aa;">Growth Duration</p>
                                        </div>
                                    </div>

                                    <div class="col-md-8 mb-4">
                                        <label class="form-control-label h6 text-uppercase">Visual Evidence (Leaf Image)</label>
                                        <div id="drop-zone" class="detection-box">
                                            <div id="upload-instructions" class="text-center">
                                                <i class="ni ni-camera-compact mb-3" style="font-size: 3.5rem; color: var(--color-secondary-green);"></i>
                                                <p class="h5 font-weight-bold">Drag & Drop leaf photo here</p>
                                                <p class="small text-muted">or click to browse files</p>
                                            </div>
                                            <input type="file" name="leaf_image" id="leaf_image" accept="image/*" class="d-none" required>
                                            <img id="preview" src="#" alt="Leaf Preview">
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" id="submit-btn" class="btn btn-primary-custom btn-lg btn-block shadow-lg py-3 lift">
                                        <i class="ni ni-zoom-split-in mr-2"></i> ANALYZE PLANT HEALTH
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('footer.php'); ?>

    <script>
    /* FORM SUBMISSION LOADER LOGIC */
    const mlForm = document.getElementById('ml-prediction-form');
    const loaderOverlay = document.getElementById('loader-overlay');
    const submitBtn = document.getElementById('submit-btn');
    const subtext = document.getElementById('dynamic-subtext');

    const analysisSteps = [
        "Uploading leaf sample...",
        "Preprocessing image data...",
        "Scanning cellular patterns...",
        "Consulting Neural Network...",
        "Generating diagnostic report..."
    ];

    mlForm.addEventListener('submit', function() {
        loaderOverlay.style.display = 'flex';
        submitBtn.disabled = true;
        
        let step = 0;
        const messageInterval = setInterval(() => {
            if (step < analysisSteps.length) {
                subtext.innerText = analysisSteps[step];
                step++;
            } else {
                clearInterval(messageInterval);
            }
        }, 1800);
    });

    /* ORIGINAL LOGIC CONTINUES BELOW */
    const plantingInput = document.getElementById('planting_date');
    const diseaseInput = document.getElementById('disease_date');
    const daysDiffInput = document.getElementById('days_diff');
    const daysBox = document.getElementById('days-display-box');

    function calculateDays() {
        if (plantingInput.value && diseaseInput.value) {
            const plantingDate = new Date(plantingInput.value);
            const diseaseDate = new Date(diseaseInput.value);
            const diffTime = diseaseDate - plantingDate;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const result = diffDays >= 0 ? diffDays : 0;
            daysDiffInput.value = result + " Days";
            daysBox.style.borderColor = "var(--color-accent-terracotta)";
            daysBox.style.backgroundColor = "#fffcfb";
        }
    }

    plantingInput.addEventListener('change', calculateDays);
    diseaseInput.addEventListener('change', calculateDays);

    const dropZone = document.getElementById('drop-zone');
    const leafInput = document.getElementById('leaf_image');
    const preview = document.getElementById('preview');
    const instructions = document.getElementById('upload-instructions');

    dropZone.addEventListener('click', () => leafInput.click());
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if(e.dataTransfer.files.length){
            leafInput.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        }
    });
    leafInput.addEventListener('change', () => {
        if(leafInput.files.length) handleFile(leafInput.files[0]);
    });

    function handleFile(file){
        const reader = new FileReader();
        reader.onload = function(e){
            preview.src = e.target.result;
            preview.style.display = "block";
            instructions.style.display = "none";
            dropZone.style.borderStyle = "solid";
        };
        reader.readAsDataURL(file);
    }
    </script>
</body>
</html>
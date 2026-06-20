<?php
// PHP Logic (UNCHANGED)
include ('fsession.php'); 
ini_set('memory_limit', '-1'); 

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit; 
}
$user_check = $_SESSION['farmer_login_user'];
$query4 = "SELECT * from farmerlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);

$crops_list = [
    'arhar' => 'Arhar', 'bajra' => 'Bajra', 'barley' => 'Barley', 'cotton' => 'Cotton', 
    'gram' => 'Gram', 'jowar' => 'Jowar', 'jute' => 'Jute', 'lentil' => 'Lentil', 
    'maize' => 'Maize', 'moong' => 'Moong', 'ragi' => 'Ragi', 'rice' => 'Rice', 
    'soyabean' => 'Soyabean', 'urad' => 'Urad', 'wheat' => 'Wheat'
];
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
    
    body { background-color: #f4f6f9 !important; font-family: 'Open Sans', sans-serif; }

    /* Gradient Header: Black to Dark Green */
    .section-shaped-custom {
        background: linear-gradient(180deg, #000000 0%, #062606 100%);
        padding-top: 9rem !important;
        padding-bottom: 13rem !important; 
    }
    
    /* Modern Card UI */
    .main-form-card {
        border-radius: 1.5rem !important;
        border: none !important;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
        margin-top: -120px; 
        overflow: hidden;
    }

    .card-header-theme {
        background: #ffffff !important;
        border-bottom: 1px solid #f1f3f5 !important;
        padding: 2.5rem 1rem 1.5rem !important;
    }

    /* Input Styling */
    .form-group label {
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--color-primary-dark);
        margin-bottom: 10px;
    }

    .input-group-custom {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        padding: 5px 15px;
    }

    .input-group-custom:focus-within {
        background: #ffffff;
        border-color: var(--color-secondary-green);
        box-shadow: 0 0 0 4px rgba(79, 119, 45, 0.1);
    }

    .input-group-custom i {
        color: var(--color-secondary-green);
        font-size: 1.1rem;
        margin-right: 12px;
    }

    .input-group-custom select, 
    .input-group-custom input {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        height: 50px;
        width: 100%;
        color: var(--color-text-dark);
        font-weight: 600;
    }

    /* Market Price Badge Style */
    .alert-price {
        background: linear-gradient(90deg, #fffaf4 0%, #ffffff 100%) !important;
        border: 1px solid #ffe8cc !important;
        border-left: 6px solid var(--color-accent-terracotta) !important;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(184, 92, 56, 0.08);
    }

    /* Button Styling */
    .btn-theme {
        background: linear-gradient(135deg, var(--color-secondary-green) 0%, #2d4a15 100%);
        border: none;
        border-radius: 12px;
        padding: 18px 45px;
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: 1px;
        color: white;
        text-transform: uppercase;
        box-shadow: 0 8px 20px rgba(79, 119, 45, 0.25);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-theme:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 25px rgba(184, 92, 56, 0.3);
        background: var(--color-accent-terracotta);
    }

    .section-title {
        color: var(--color-primary-dark);
        font-weight: 800;
        letter-spacing: -1px;
    }

    /* ========== RESPONSIVE MEDIA QUERIES (ADDED ONLY) ========== */
    
    /* Tablet and Mobile Devices */
    @media (max-width: 992px) {
        .section-shaped-custom {
            padding-top: 7rem !important;
            padding-bottom: 10rem !important;
        }
        
        .main-form-card {
            margin-top: -100px;
        }
        
        .display-1 {
            font-size: 2.5rem !important;
        }
        
        .lead {
            font-size: 1.1rem !important;
        }
    }
    
    /* Mobile Devices (Portrait & Landscape) */
    @media (max-width: 768px) {
        .section-shaped-custom {
            padding-top: 6rem !important;
            padding-bottom: 8rem !important;
        }
        
        .main-form-card {
            margin-top: -80px;
            border-radius: 1.2rem !important;
        }
        
        .card-header-theme {
            padding: 1.8rem 1rem 1.2rem !important;
        }
        
        .card-body.p-lg-5 {
            padding: 1.5rem !important;
        }
        
        .display-1 {
            font-size: 1.8rem !important;
        }
        
        .lead {
            font-size: 0.95rem !important;
        }
        
        .section-title {
            font-size: 1.5rem;
        }
        
        .badge.badge-pill {
            font-size: 0.7rem;
            padding: 0.4rem 1rem !important;
        }
        
        .alert-price {
            padding: 1rem;
        }
        
        .alert-price .col {
            padding: 0;
        }
        
        .alert-price h5 {
            font-size: 0.9rem;
        }
        
        .alert-price p {
            font-size: 0.75rem;
        }
        
        .alert-price .h4 {
            font-size: 1rem !important;
        }
        
        /* Stack form fields on mobile */
        .row > .col-md-4 {
            margin-bottom: 0.5rem !important;
        }
        
        .form-group label {
            font-size: 0.75rem;
            margin-bottom: 6px;
        }
        
        .input-group-custom {
            padding: 3px 12px;
        }
        
        .input-group-custom select, 
        .input-group-custom input {
            height: 42px;
            font-size: 0.9rem;
        }
        
        .input-group-custom i {
            font-size: 0.95rem;
            margin-right: 8px;
        }
        
        .btn-theme {
            padding: 14px 30px;
            font-size: 0.85rem;
        }
        
        .mt-5 {
            margin-top: 2rem !important;
        }
        
        .icon-shape {
            width: 35px;
            height: 35px;
        }
        
        .icon-shape i {
            font-size: 0.9rem;
        }
    }
    
    /* Small Mobile Devices (<=480px) */
    @media (max-width: 480px) {
        .section-shaped-custom {
            padding-top: 5rem !important;
            padding-bottom: 6rem !important;
        }
        
        .main-form-card {
            margin-top: -60px;
        }
        
        .display-1 {
            font-size: 1.4rem !important;
        }
        
        .lead {
            font-size: 0.85rem !important;
        }
        
        .section-title {
            font-size: 1.3rem;
        }
        
        .card-header-theme p {
            font-size: 0.8rem;
        }
        
        .card-body.p-lg-5 {
            padding: 1rem !important;
        }
        
        .alert-price .row {
            flex-direction: column;
            text-align: center;
        }
        
        .alert-price .col-auto {
            margin-bottom: 8px;
        }
        
        .alert-price .close {
            position: absolute;
            right: 5px;
            top: 5px;
        }
        
        .btn-theme {
            padding: 12px 25px;
            font-size: 0.8rem;
            width: 100%;
        }
        
        .input-group-custom select, 
        .input-group-custom input {
            height: 38px;
            font-size: 0.85rem;
        }
        
        .input-group-custom .badge {
            font-size: 0.7rem;
        }
        
        .text-muted small {
            font-size: 0.7rem;
        }
    }
    
    /* Landscape Mode for Mobile */
    @media (max-width: 768px) and (orientation: landscape) {
        .section-shaped-custom {
            padding-top: 5rem !important;
            padding-bottom: 6rem !important;
        }
        
        .main-form-card {
            margin-top: -70px;
        }
        
        .display-1 {
            font-size: 1.6rem !important;
        }
        
        .row > .col-md-4 {
            margin-bottom: 0.25rem !important;
        }
    }
    
    /* Large Desktop Screens (maintain original spacing) */
    @media (min-width: 1400px) {
        .container {
            max-width: 1320px;
        }
        
        .display-1 {
            font-size: 4rem;
        }
    }
    
    /* Touch-friendly adjustments for mobile */
    @media (max-width: 768px) {
        select, input, button {
            cursor: pointer;
        }
        
        button:active {
            transform: scale(0.98);
        }
        
        .input-group-custom {
            min-height: 48px;
        }
    }
</style>

<body id="top">
    <?php include ('fnav.php'); ?> 

    <section class="section section-shaped section-lg section-shaped-custom">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <span class="badge badge-pill badge-success mb-3 px-4 py-2">FARMER TRADING PANEL</span>
                    <h1 class="display-1 text-white font-weight-bold">Maximize Your Profits</h1>
                    <p class="lead text-white opacity-8">Register your stock and let the best market prices come to you.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11"> 
                    
                    <div class="card main-form-card">
                        <div class="card-header card-header-theme text-center">
                            <h2 class="section-title mb-0">
                                <i class="ni ni-shop mr-2"></i> Inventory Details
                            </h2>
                            <p class="text-muted mt-2">Update your available stock below</p>
                        </div>
                        
                        <div class="card-body p-lg-5"> 
                            <div class="alert alert-price alert-dismissible fade show mb-5" style="display: none;" id="popup" role="alert">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                            <i class="ni ni-active-40"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="mb-0 text-dark font-weight-bold">Real-time Market Insights</h5>
                                        <p class="mb-0 small">Average Price for <span id="crop_name_display" class="text-danger font-weight-bold"></span> is currently <span id="price_display" class="text-danger h4 font-weight-900 mx-1"></span> <span class="text-muted">INR/KG</span></p>
                                    </div>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            
                            <form role="form" onsubmit="return tradecrops()" id="sellcrops" action="ftradecropsScript.php" method="POST"> 
                                <div class="row"> 
                                    <div class="col-md-4 mb-4">
                                        <div class="form-group">
                                            <label>Select Crop Type</label>
                                            <div class="input-group-custom">
                                                <i class="ni ni-palette"></i>
                                                <select id="crops" name="crops" required>
                                                    <option value="">Choose Crop...</option>
                                                    <?php foreach ($crops_list as $value => $label): ?>
                                                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <div class="form-group">
                                            <label>Total Quantity</label>
                                            <div class="input-group-custom">
                                                <i class="ni ni-box-2"></i>
                                                <input type="number" name="trade_farmer_cropquantity" placeholder="e.g. 1000" min="1" required>
                                                <span class="badge badge-secondary ml-2">KG</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <div class="form-group">
                                            <label>Desired Price</label>
                                            <div class="input-group-custom">
                                                <i class="ni ni-money-coins"></i>
                                                <input type="number" name="trade_farmer_cost" placeholder="0.00" step="0.01" min="0" required>
                                                <span class="text-muted font-weight-bold">₹/KG</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-5">
                                    <button type="submit" name="Crop_submit" value="Crop_submit" class="btn btn-theme">
                                        <i class="ni ni-cloud-upload-96 mr-2"></i> Post Harvest Listing
                                    </button>
                                    <div class="mt-4">
                                        <small class="text-muted"><i class="ni ni-lock-circle-open mr-1"></i> Your listing will be reviewed and posted to the trader dashboard immediately.</small>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <?php require("footer.php");?>

    <script>
    document.getElementById("crops").addEventListener("change", function() {
        var crops = jQuery('#crops').val();
        document.getElementById("crop_name_display").innerHTML = jQuery('#crops option:selected').text();
        
        if (crops) {
            jQuery.ajax({
                url: 'fcheck_price.php',
                type: 'post',
                data: 'crops=' + crops,
                success: function(response) {
                    jQuery('#price_display').text(response); 
                    jQuery("#popup").fadeIn();
                }
            });
        } else {
            jQuery("#popup").fadeOut();
        }
    });
    </script>
</body>
</html>
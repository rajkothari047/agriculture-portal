<?php
include ('csession.php');
include ('../sql.php');

ini_set('memory_limit', '-1');

if(!isset($_SESSION['customer_login_user'])){
    header("location: ../index.php");
    exit;
} 

$user_check = $_SESSION['customer_login_user'];
$query4 = "SELECT * from custlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['cust_id'];
$para2 = $row4['cust_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=yes">
    <title>KisanMitra | Crop Stock & Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-dark:  #0A3D0A;
            --terracotta:  #B85C38;
            --green-mid:   #4F772D;
            --bg:          #F9F7F3;
            --text:        #1E293B;
            --muted:       #64748B;
            --border:      #E8E3DC;
            --white:       #FFFFFF;
            --terra-light: #FEF0E8;
            --green-light: #EDF4E5;
            --gold:        #C8960C;
            --gold-light:  #FDF4DC;
            --success:     #2E7D32;
            --success-light: #E8F5E9;
            --warning:     #E65100;
            --warning-light: #FFF3E0;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ── FULL WIDTH BANNER (same style) ── */
        .hero-banner {
            width: 100%;
            height: 320px;
            position: relative;
            overflow: hidden;
            background: var(--green-dark);
        }
        .hero-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 1;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(10,61,10,0.4), rgba(10,61,10,0.92));
        }
        .hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 40px 5%;
            z-index: 2;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            padding: 6px 18px;
            border-radius: 0px;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid rgba(255,255,255,0.25);
            margin-bottom: 15px;
        }
        .hero-badge i { color: #F4C542; }

        /* ── LAYOUT WRAPPER ── */
        .main-container {
            max-width: 1280px;
            margin: -50px auto 80px;
            padding: 0 24px;
            position: relative;
            z-index: 5;
        }

        /* ── SEARCH BAR SECTION ── */
        .search-section {
            background: var(--white);
            border-radius: 0px;
            padding: 20px 28px;
            margin-bottom: 32px;
            border: 1px solid var(--border);
            box-shadow: 0 8px 20px rgba(0,0,0,0.04);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .search-wrapper {
            flex: 2;
            min-width: 250px;
            position: relative;
        }
        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--green-mid);
            font-size: 1rem;
        }
        .search-input {
            width: 100%;
            padding: 14px 20px 14px 48px;
            border: 1px solid var(--border);
            border-radius: 0px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            transition: all 0.2s;
        }
        .search-input:focus {
            outline: none;
            border-color: var(--terracotta);
            box-shadow: 0 0 0 3px rgba(184,92,56,0.1);
        }
        .stats-badge {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .stat-chip {
            background: var(--green-light);
            padding: 8px 18px;
            border-radius: 0px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--green-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stat-chip i { color: var(--terracotta); }

        /* ── UNIQUE STOCK GRID (3D card style) ── */
        .stock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 28px;
            margin-bottom: 40px;
        }
        
        .stock-card {
            background: var(--white);
            border-radius: 0px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid var(--border);
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        }
        .stock-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 35px -12px rgba(10,61,10,0.2);
            border-color: var(--green-mid);
        }
        
        .card-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: var(--success-light);
            color: var(--success);
            padding: 4px 12px;
            border-radius: 0px;
            font-size: 0.65rem;
            font-weight: 800;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .card-image-placeholder {
            background: linear-gradient(135deg, var(--green-light) 0%, #D9E8CC 100%);
            padding: 28px 0;
            text-align: center;
            position: relative;
        }
        .card-image-placeholder i {
            font-size: 3.5rem;
            color: var(--green-dark);
            opacity: 0.7;
        }
        
        .card-body {
            padding: 20px 22px 24px;
        }
        
        .crop-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--green-dark);
            margin-bottom: 4px;
            text-transform: capitalize;
        }
        
        .stock-meter {
            margin: 18px 0;
        }
        .meter-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--muted);
        }
        .meter-bar {
            height: 8px;
            background: var(--border);
            border-radius: 0px;
            overflow: hidden;
        }
        .meter-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--green-mid), var(--green-dark));
            border-radius: 0px;
            width: 0%;
        }
        
        .quantity-highlight {
            background: var(--gold-light);
            border-radius: 0px;
            padding: 12px 16px;
            text-align: center;
            margin: 16px 0;
        }
        .quantity-number {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--gold);
            line-height: 1;
        }
        .quantity-unit {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--muted);
        }
        
        .btn-details {
            width: 100%;
            background: var(--green-dark);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 0px;
            font-weight: 700;
            font-size: 0.8rem;
            transition: all 0.2s;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-details:hover {
            background: var(--terracotta);
            transform: scale(1.01);
        }
        
        /* ── FIXED MODAL - SQUARE CORNERS ── */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(6px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.25s ease, opacity 0.25s ease;
            padding: 20px;
            box-sizing: border-box;
        }
        .modal-overlay.active {
            visibility: visible;
            opacity: 1;
        }
        .modal-container {
            background: var(--white);
            max-width: 520px;
            width: 100%;
            border-radius: 0px;
            position: relative;
            transform: scale(0.96);
            transition: transform 0.25s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            box-shadow: 0 30px 50px -20px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }
        .modal-overlay.active .modal-container {
            transform: scale(1);
        }
        
        /* Hide default scrollbar - use smooth internal scrolling without ugly bars */
        .modal-scroll-area {
            max-height: 70vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--terracotta) var(--border);
        }
        .modal-scroll-area::-webkit-scrollbar {
            width: 4px;
            background: transparent;
        }
        .modal-scroll-area::-webkit-scrollbar-track {
            background: var(--border);
            border-radius: 0px;
            margin: 8px 0;
        }
        .modal-scroll-area::-webkit-scrollbar-thumb {
            background: var(--terracotta);
            border-radius: 0px;
        }
        /* On mobile, make scroll area smaller and hide scrollbar for cleaner look */
        @media (max-width: 640px) {
            .modal-scroll-area {
                max-height: 65vh;
                scrollbar-width: none; /* Firefox */
            }
            .modal-scroll-area::-webkit-scrollbar {
                display: none; /* Chrome/Safari - cleaner look on mobile */
            }
        }
        
        .modal-header-custom {
            background: var(--green-dark);
            padding: 26px 28px 22px;
            color: white;
            text-align: center;
            position: relative;
        }
        .modal-header-custom i {
            font-size: 2.2rem;
            margin-bottom: 10px;
            color: #F4C542;
        }
        .modal-header-custom h2 {
            font-size: 1.6rem;
            margin: 8px 0 4px;
            text-transform: capitalize;
            font-weight: 800;
        }
        .modal-header-custom p {
            opacity: 0.85;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        
        /* Close button - always visible and touch friendly */
        .modal-close-btn {
            position: absolute;
            top: 18px;
            right: 20px;
            background: rgba(255,255,255,0.18);
            border: none;
            color: white;
            font-size: 1.1rem;
            width: 38px;
            height: 38px;
            border-radius: 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 15;
            backdrop-filter: blur(4px);
        }
        .modal-close-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.05);
        }
        
        .modal-body-custom {
            padding: 28px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin: 0 0 20px 0;
        }
        .info-tile {
            background: var(--bg);
            padding: 14px 12px;
            border-radius: 0px;
            border-left: 3px solid var(--terracotta);
            transition: 0.2s;
        }
        .info-tile i {
            color: var(--green-mid);
            margin-bottom: 6px;
            font-size: 1rem;
            display: inline-block;
        }
        .info-tile .label {
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
            letter-spacing: 0.4px;
            margin-top: 4px;
        }
        .info-tile .value {
            font-weight: 800;
            color: var(--text);
            font-size: 0.9rem;
            margin-top: 6px;
            word-break: break-word;
        }
        .info-note {
            background: var(--green-light);
            border-radius: 0px;
            padding: 14px 16px;
            margin-top: 6px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .info-note i {
            color: var(--green-mid);
            font-size: 1rem;
            margin-top: 2px;
        }
        .info-note span {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text);
            line-height: 1.45;
        }
        
        .modal-footer-custom {
            padding: 16px 28px 28px;
            display: flex;
            gap: 14px;
            justify-content: flex-end;
            border-top: 1px solid var(--border);
            background: var(--white);
            flex-wrap: wrap;
        }
        .btn-modal {
            padding: 10px 24px;
            border-radius: 0px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            border: none;
            font-family: 'Inter', sans-serif;
        }
        .btn-modal-primary {
            background: var(--terracotta);
            color: white;
        }
        .btn-modal-primary:hover {
            background: var(--green-dark);
            transform: translateY(-2px);
        }
        .btn-modal-secondary {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }
        .btn-modal-secondary:hover {
            background: var(--bg);
        }
        
        /* Empty state */
        .empty-stock {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 0px;
            border: 1px solid var(--border);
        }
        
        /* Side widget */
        .info-widget {
            background: linear-gradient(135deg, var(--green-dark), #1a4f1a);
            border-radius: 0px;
            padding: 24px 28px;
            margin-top: 20px;
            color: white;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-container { padding: 0 16px; margin: -40px auto 60px; }
            .hero-banner { height: 240px; }
            .search-section { flex-direction: column; align-items: stretch; padding: 16px 20px; }
            .stock-grid { gap: 18px; }
            .modal-container { max-width: 94%; border-radius: 0px; }
            .modal-header-custom { padding: 22px 20px 18px; }
            .modal-header-custom h2 { font-size: 1.35rem; }
            .modal-close-btn { top: 14px; right: 14px; width: 34px; height: 34px; font-size: 1rem; border-radius: 0px; }
            .modal-body-custom { padding: 22px; }
            .info-grid { gap: 12px; }
            .info-tile { padding: 12px 10px; }
            .modal-footer-custom { padding: 14px 22px 24px; }
            .btn-modal { padding: 8px 20px; font-size: 0.8rem; border-radius: 0px; }
            .stats-badge { justify-content: center; }
        }
        
        @media (max-width: 480px) {
            .hero-content h1 { font-size: 1.6rem; }
            .card-body { padding: 16px; }
            .quantity-number { font-size: 1.3rem; }
            .info-grid { grid-template-columns: 1fr; }
            .modal-scroll-area { max-height: 60vh; }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-card {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        /* Disable body scroll when modal is open */
        body.modal-open {
            overflow: hidden;
            padding-right: 0;
        }
    </style>
</head>
<body>

<?php include('cnav.php'); ?>

<!-- FULL WIDTH BANNER (consistent design) -->
<section class="hero-banner">
    <img src="../assets/img/stock2.jpg" alt="Fresh Harvest">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-warehouse"></i> Live Stock Inventory
        </div>
        <h1 style="color: #fff; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 5vw, 3rem);">
            Market <span style="color: #F4C542;">Stock & Supply</span>
        </h1>
        <p style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">
            <i class="fas fa-tractor"></i> Real-time availability from farm partners
        </p>
    </div>
</section>

<div class="main-container">
    
    <!-- Enhanced Search + Stats Section -->
    <div class="search-section">
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="cropSearchInput" class="search-input" placeholder="Search crops by name... (e.g., Rice, Wheat, Arhar)">
        </div>
        <div class="stats-badge">
            <div class="stat-chip"><i class="fas fa-seedling"></i> <span id="totalCropsCount">0</span> Varieties</div>
            <div class="stat-chip"><i class="fas fa-weight-hanging"></i> Grade-A Quality</div>
            <div class="stat-chip"><i class="fas fa-truck-fast"></i> Free Delivery*</div>
        </div>
    </div>
    
    <!-- Stock Grid (dynamic cards) -->
    <div class="stock-grid" id="stockGrid">
        <?php 
        $sql = "SELECT crop, quantity FROM production_approx WHERE quantity > 0 ORDER BY crop ASC";
        $query = mysqli_query($conn, $sql);
        $totalItems = mysqli_num_rows($query);
        $maxQuantity = 0;
        $cropsData = [];
        
        if(mysqli_num_rows($query) > 0) {
            while($res = mysqli_fetch_array($query)){ 
                $cropsData[] = $res;
                if($res['quantity'] > $maxQuantity) $maxQuantity = $res['quantity'];
            }
            
            // Reset pointer for display
            mysqli_data_seek($query, 0);
            $index = 0;
            while($res = mysqli_fetch_array($query)){ 
                $percentage = ($maxQuantity > 0) ? ($res['quantity'] / $maxQuantity) * 100 : 0;
                $stockLevel = ($percentage > 70) ? 'High Stock' : (($percentage > 30) ? 'Medium Stock' : 'Low Stock');
                $stockColor = ($percentage > 70) ? 'var(--success)' : (($percentage > 30) ? 'var(--gold)' : 'var(--warning)');
        ?>
        <div class="stock-card crop-item animate-card" data-crop-name="<?php echo strtolower($res['crop']); ?>" style="animation-delay: <?php echo $index * 0.05; ?>s">
            <div class="card-badge">
                <i class="fas fa-check-circle"></i> <?php echo $stockLevel; ?>
            </div>
            <div class="card-image-placeholder">
                <i class="fas fa-leaf"></i>
            </div>
            <div class="card-body">
                <h3 class="crop-title"><?php echo ucfirst($res['crop']); ?></h3>
                <p class="text-muted" style="font-size: 0.7rem;">Premium harvest · MSP compliant</p>
                
                <div class="stock-meter">
                    <div class="meter-label">
                        <span>Availability</span>
                        <span><?php echo round($percentage); ?>%</span>
                    </div>
                    <div class="meter-bar">
                        <div class="meter-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo $stockColor; ?>"></div>
                    </div>
                </div>
                
                <div class="quantity-highlight">
                    <div class="quantity-number"><?php echo number_format($res['quantity']); ?> <span style="font-size: 0.9rem;">KG</span></div>
                    <div class="quantity-unit">Available for immediate dispatch</div>
                </div>
                
                <button class="btn-details" 
                        data-crop="<?php echo htmlspecialchars($res['crop']); ?>" 
                        data-qty="<?php echo $res['quantity']; ?>"
                        data-percent="<?php echo round($percentage); ?>"
                        data-stocklevel="<?php echo $stockLevel; ?>">
                    <i class="fas fa-eye"></i> View Stock Details
                </button>
            </div>
        </div>
        <?php 
                $index++;
            }
        } else {
            echo '<div class="empty-stock"><i class="fas fa-box-open" style="font-size: 3rem; color: var(--border); margin-bottom: 15px; display: block;"></i><h3>No Stock Available</h3><p>New crop listings will appear here soon. Check back later!</p></div>';
        }
        ?>
    </div>
    
    <!-- Dynamic Total Count Update -->
    <div class="info-widget" id="infoWidget" style="display: <?php echo ($totalItems > 0) ? 'flex' : 'none'; ?>;">
        <div>
            <i class="fas fa-chart-line" style="font-size: 1.8rem; margin-right: 12px;"></i>
            <strong style="font-size: 1rem;">Smart Farming Tip:</strong> 
            <span style="font-size: 0.85rem; opacity: 0.9;">Order in bulk during harvest season to get best rates.</span>
        </div>
        <div>
            <a href="cbuy_crops.php" style="color: #F4C542; text-decoration: none; font-weight: 700;">Start Ordering →</a>
        </div>
    </div>
</div>

<!-- IMPROVED MODAL: Square corners design -->
<div id="stockModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header-custom">
            <button class="modal-close-btn" id="modalCloseBtn">
                <i class="fas fa-times"></i>
            </button>
            <i class="fas fa-tractor"></i>
            <h2 id="modalCropTitle" class="text-capitalize"></h2>
            <p>Stock & Cultivation Details</p>
        </div>
        <div class="modal-scroll-area">
            <div class="modal-body-custom">
                <div class="info-grid">
                    <div class="info-tile">
                        <i class="fas fa-weight-hanging"></i>
                        <div class="label">Total Available Stock</div>
                        <div class="value" id="modalQtyVal">—</div>
                    </div>
                    <div class="info-tile">
                        <i class="fas fa-chart-simple"></i>
                        <div class="label">Stock Level</div>
                        <div class="value" id="modalStockLevel">—</div>
                    </div>
                    <div class="info-tile">
                        <i class="fas fa-calendar-alt"></i>
                        <div class="label">Harvest Season</div>
                        <div class="value" id="modalSeason">Varies by region</div>
                    </div>
                    <div class="info-tile">
                        <i class="fas fa-star"></i>
                        <div class="label">Quality Grade</div>
                        <div class="value">Premium Grade-A</div>
                    </div>
                    <div class="info-tile">
                        <i class="fas fa-truck"></i>
                        <div class="label">Estimated Delivery</div>
                        <div class="value">3-5 business days</div>
                    </div>
                    <div class="info-tile">
                        <i class="fas fa-hand-holding-usd"></i>
                        <div class="label">Price Range</div>
                        <div class="value">Market competitive</div>
                    </div>
                </div>
                <div class="info-note">
                    <i class="fas fa-info-circle"></i>
                    <span>This crop is directly sourced from registered farmers. Minimum order quantity: 10 KG.</span>
                </div>
            </div>
        </div>
        <div class="modal-footer-custom">
            <button class="btn-modal btn-modal-secondary" id="modalCloseFooterBtn"><i class="fas fa-times"></i> Close</button>
            <a href="cbuy_crops.php" class="btn-modal btn-modal-primary"><i class="fas fa-shopping-cart"></i> Place Order</a>
        </div>
    </div>
</div>

<script>
    // Search functionality
    const searchInput = document.getElementById('cropSearchInput');
    const cropItems = document.querySelectorAll('.crop-item');
    const totalCropsSpan = document.getElementById('totalCropsCount');
    
    function updateTotalVisible() {
        let visible = 0;
        cropItems.forEach(item => {
            if (item.style.display !== 'none') visible++;
        });
        if (totalCropsSpan) totalCropsSpan.innerText = visible;
    }
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            cropItems.forEach(item => {
                const cropName = item.getAttribute('data-crop-name') || '';
                if (cropName.includes(value)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
            updateTotalVisible();
        });
    }
    
    // Modal handling with proper close on mobile
    const modal = document.getElementById('stockModal');
    const modalTitle = document.getElementById('modalCropTitle');
    const modalQty = document.getElementById('modalQtyVal');
    const modalStockLevel = document.getElementById('modalStockLevel');
    const modalSeason = document.getElementById('modalSeason');
    
    // Function to close modal
    function closeModal() {
        modal.classList.remove('active');
        document.body.classList.remove('modal-open');
    }
    
    // Function to open modal
    function openModal() {
        modal.classList.add('active');
        document.body.classList.add('modal-open');
    }
    
    // Close modal when clicking on the overlay (background)
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
    
    // Crop-specific season data
    const seasonMap = {
        'rice': 'Kharif (June - October)',
        'paddy': 'Kharif (June - October)',
        'wheat': 'Rabi (October - March)',
        'arhar': 'Monsoon (June - December)',
        'tur': 'Monsoon (June - December)',
        'dal': 'Monsoon (June - December)',
        'maize': 'Kharif & Rabi',
        'cotton': 'Kharif (May - September)',
        'sugarcane': 'Annual (12-18 months)',
        'soybean': 'Kharif (June - October)'
    };
    
    function getSeason(cropName) {
        const lower = cropName.toLowerCase();
        for (let key in seasonMap) {
            if (lower.includes(key)) return seasonMap[key];
        }
        return 'Seasonal availability';
    }
    
    // Attach click events to all detail buttons
    document.querySelectorAll('.btn-details').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const crop = this.getAttribute('data-crop');
            const qty = parseInt(this.getAttribute('data-qty')).toLocaleString();
            const percent = this.getAttribute('data-percent');
            const stockLevelText = this.getAttribute('data-stocklevel');
            
            modalTitle.innerText = crop;
            modalQty.innerText = qty + ' KG';
            modalStockLevel.innerText = stockLevelText + ' (' + percent + '%)';
            modalSeason.innerText = getSeason(crop);
            openModal();
        });
    });
    
    // Close buttons: both the X icon and the footer close button
    const closeBtn = document.getElementById('modalCloseBtn');
    const closeFooterBtn = document.getElementById('modalCloseFooterBtn');
    
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (closeFooterBtn) closeFooterBtn.addEventListener('click', closeModal);
    
    // Initialize total count
    if (totalCropsSpan) totalCropsSpan.innerText = cropItems.length;
    
    // Prevent body scroll when modal opens (already handled in openModal/closeModal)
</script>

<?php require("footer.php"); ?>
</body>
</html>
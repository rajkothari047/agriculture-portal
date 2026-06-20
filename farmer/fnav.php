<?php
if (!isset($para2)) {
    $para2 = "Agriculture Portal"; 
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>KisanMitra Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    :root { 
        --color-primary-dark: #0A3D0A; 
        --color-accent-terracotta: #B85C38;
    }

    html, body { 
        margin: 0; 
        padding: 0;
        background-color: var(--color-primary-dark); 
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* --- GLOBAL RESET TO KILL BLUE BROWSER EFFECTS --- */
    a, a:visited, a:active, a:focus, button {
        text-decoration: none !important;
        outline: none !important;
        -webkit-tap-highlight-color: transparent !important;
    }

    #main-header { 
        width: 100%; 
        background-color: var(--color-primary-dark); 
        box-shadow: 0 4px 12px rgba(0,0,0,0.3); 
        position: sticky;
        top: 0;
        z-index: 1000;
        height: 64px; 
    }

    .nav-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        width: 100%;
        box-sizing: border-box;
    }

    .nav-link-item {
        position: relative;
        color: white !important; 
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.6rem 0.8rem;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .nav-center-group {
        flex: 1;
        display: flex;
        justify-content: center;
        gap: 0.75rem;
    }

    .nav-link-item::after {
        content: '';
        position: absolute;
        height: 2px;
        width: 0;
        bottom: 0;
        left: 50%;
        background-color: var(--color-accent-terracotta);
        transition: all 0.3s ease-in-out;
    }

    .nav-link-item:hover::after {
        width: 100%;
        left: 0;
    }

    .nav-link-item:hover {
        color: var(--color-accent-terracotta) !important;
    }

    .dropdown { position: relative; }
    .dropdown-content { 
        position: absolute; 
        right: 0; top: 100%; 
        background: white; 
        border-radius: 0.5rem; 
        border: 1px solid #d1d5db; 
        opacity: 0; visibility: hidden; 
        transform: translateY(10px); 
        transition: all 0.2s; 
        z-index: 50; 
        min-width: 180px;
        padding: 0.25rem 0;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }

    .dropdown:hover .dropdown-content { 
        opacity: 1; visibility: visible; transform: translateY(0); 
    }

    .dropdown-content a { 
        display: flex; 
        align-items: center; 
        gap: 0.5rem; 
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
        color: #374151 !important; 
        transition: background 0.2s;
    }

    .dropdown-content a:not(.btn-logout):hover { 
        background: #f3f4f6 !important; 
        color: #374151 !important;
    }

    .btn-profile-dropdown {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
        color: white;
        font-weight: 700;
        border-radius: 9999px;
        background-color: var(--color-accent-terracotta);
        border: none;
        cursor: pointer;
    }

    .btn-logout {
        margin: 0.25rem 0.5rem !important;
        background-color: white !important; 
        color: #dc2626 !important; 
        border: none !important; 
        border-radius: 0.4rem;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
    }

    .btn-logout:hover {
        background-color: #dc2626 !important;
        color: white !important;
    }

    /* ========== MOBILE MENU STYLES ========== */
    @media (max-width: 767px) {
        .desktop-nav-items {
            display: none !important;
        }
        
        .nav-center-group {
            display: none !important;
        }
        
        .desktop-profile-dropdown {
            display: none !important;
        }
    }
    
    /* Hamburger button styles */
    .hamburger-btn {
        display: none;
        flex-direction: column;
        justify-content: space-between;
        width: 30px;
        height: 24px;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        z-index: 1001;
        position: relative;
    }
    
    .hamburger-line {
        width: 100%;
        height: 3px;
        background-color: white;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    
    @media (max-width: 767px) {
        .hamburger-btn {
            display: flex;
        }
    }
    
    /* Mobile Menu Overlay */
    .mobile-menu-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .mobile-menu-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    /* Mobile Menu Container */
    .mobile-menu-container {
        position: fixed;
        top: 0;
        right: -300px;
        width: 280px;
        height: 100%;
        background-color: white;
        z-index: 9999;
        box-shadow: -2px 0 15px rgba(0,0,0,0.2);
        transition: right 0.3s ease;
        overflow-y: auto;
        padding: 20px 20px 30px;
    }
    
    .mobile-menu-container.active {
        right: 0;
    }
    
    /* Close button styling */
    .close-menu-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #f3f4f6;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #374151;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .close-menu-btn:hover {
        background-color: #e5e7eb;
        transform: scale(1.05);
    }
    
    .mobile-menu-links-wrapper {
        margin-top: 60px;
    }
    
    .mobile-menu-link {
        display: block;
        padding: 15px 0;
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        text-decoration: none;
        border-bottom: 1px solid #e5e7eb;
        transition: color 0.2s;
    }
    
    .mobile-menu-link:hover {
        color: var(--color-accent-terracotta);
    }
    
    .mobile-dropdown-title {
        padding: 15px 0;
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    
    .mobile-dropdown-title i {
        transition: transform 0.3s;
    }
    
    .mobile-dropdown-title.open i {
        transform: rotate(180deg);
    }
    
    .mobile-submenu {
        padding-left: 15px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    
    .mobile-submenu.open {
        max-height: 400px;
    }
    
    .mobile-submenu a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 0;
        font-size: 0.9rem;
        color: #4b5563;
        text-decoration: none;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .mobile-submenu a:hover {
        color: var(--color-accent-terracotta);
    }
    
    .mobile-home-btn {
        display: block;
        margin-top: 20px;
        text-align: center;
        background-color: var(--color-accent-terracotta);
        color: white;
        padding: 12px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: background 0.2s;
    }
    
    .mobile-home-btn:hover {
        background-color: #a3502c;
    }
    
    .mobile-logout-btn {
        background-color: #dc2626 !important;
    }
    
    .mobile-logout-btn:hover {
        background-color: #b91c1c !important;
    }
    
    @media (min-width: 768px) {
        .mobile-only {
            display: none !important;
        }
        .mobile-menu-overlay, .mobile-menu-container {
            display: none;
        }
    }
</style>
</head>
<body>

<header id="main-header">
    <div class="nav-container">
        <!-- Left: Brand Logo -->
        <div style="display: flex; align-items: center;">
            <a href="../index.php" style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.5rem;">🌱</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: white;">
                    Kisan<span style="color: var(--color-accent-terracotta);">Mitra</span>
                </span>
            </a>
        </div>

        <!-- Center: Navigation Links (Desktop Only) -->
        <div class="nav-center-group desktop-nav-items">
            <div class="dropdown">
                <a href="#" class="nav-link-item">
                    <i data-lucide="sparkles" style="width:16px; height:16px;"></i> Prediction
                </a>
                <div class="dropdown-content">
                    <a href="fcrop_prediction.php"><i data-lucide="leaf" style="width:16px; height:16px;"></i> Crop Prediction</a>
                    <a href="fyield_prediction.php"><i data-lucide="trending-up" style="width:16px; height:16px;"></i> Yield Prediction</a>
                    <a href="frainfall_prediction.php"><i data-lucide="cloud-rain" style="width:16px; height:16px;"></i> Rainfall Prediction</a>
                    <a href="disease_detection.php"><i data-lucide="activity" style="width:16px; height:16px;"></i> Disease Detection</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#" class="nav-link-item">
                    <i data-lucide="lightbulb" style="width:16px; height:16px;"></i> Recommendation
                </a>
                <div class="dropdown-content">
                    <a href="fcrop_recommendation.php"><i data-lucide="leaf" style="width:16px; height:16px;"></i> Crop Recommendation</a>
                    <a href="ffertilizer_recommendation.php"><i data-lucide="droplet" style="width:16px; height:16px;"></i> Fertilizer Recommendation</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#" class="nav-link-item">
                    <i data-lucide="shopping-basket" style="width:16px; height:16px;"></i> Trade
                </a>
                <div class="dropdown-content">
                    <a href="ftradecrops.php"><i data-lucide="shopping-cart" style="width:16px; height:16px;"></i> Trade Crops</a>
                    <a href="fstock_crop.php"><i data-lucide="archive" style="width:16px; height:16px;"></i> Crop Stocks</a>
                    <a href="fselling_history.php"><i data-lucide="clipboard-list" style="width:16px; height:16px;"></i> Selling History</a>
                    <a href="fmarket_price.php"><i data-lucide="bar-chart-3" style="width:16px; height:16px;"></i> Market Prices</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#" class="nav-link-item">
                    <i data-lucide="wrench" style="width:16px; height:16px;"></i> Tools
                </a>
                <div class="dropdown-content">
                    <a href="fchatgpt.php"><i data-lucide="bot" style="width:16px; height:16px;"></i> Chat Bot</a>
                    <a href="fweather_prediction.php"><i data-lucide="cloud-lightning" style="width:16px; height:16px;"></i> Weather Forecast</a>
                    <a href="fnewsfeed.php"><i data-lucide="newspaper" style="width:16px; height:16px;"></i> News Feed</a>
                </div>
            </div>
        </div>

        <!-- Right: Profile Dropdown (Desktop Only) -->
        <div style="margin-left: auto;" class="desktop-profile-dropdown desktop-nav-items">
            <div class="dropdown">
                <button class="btn-profile-dropdown">
                    <i data-lucide="user" style="width:18px; height:18px;"></i> <?php echo htmlspecialchars($para2) ?>
                </button>
                <div class="dropdown-content">
                    <a href="fprofile.php"><i data-lucide="user" style="width:16px; height:16px;"></i> Profile</a>
                    <a href="flogout.php" class="btn-logout"><i data-lucide="log-out" style="width:16px; height:16px;"></i> Logout</a>
                </div>
            </div>
        </div>

        <!-- Hamburger Button (Mobile Only) -->
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

<!-- Mobile Menu Container -->
<div class="mobile-menu-container" id="mobileMenuContainer">
    <!-- Close Button -->
    <button class="close-menu-btn" id="closeMenuBtn">✕</button>
    
    <!-- Wrapper for menu items with top margin to avoid close button overlap -->
    <div class="mobile-menu-links-wrapper">

        <!-- Prediction Dropdown Mobile -->
        <div class="mobile-dropdown-item">
            <div class="mobile-dropdown-title" data-dropdown="prediction">
                <span><i data-lucide="sparkles" style="width:18px; height:18px; display: inline-block; margin-right: 10px;"></i> Prediction</span>
                <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </div>
            <div class="mobile-submenu" id="prediction-submenu">
                <a href="fcrop_prediction.php"><i data-lucide="leaf" class="w-4 h-4"></i> Crop Prediction</a>
                <a href="fyield_prediction.php"><i data-lucide="trending-up" class="w-4 h-4"></i> Yield Prediction</a>
                <a href="frainfall_prediction.php"><i data-lucide="cloud-rain" class="w-4 h-4"></i> Rainfall Prediction</a>
                <a href="disease_detection.php"><i data-lucide="activity" class="w-4 h-4"></i> Disease Detection</a>
            </div>
        </div>
        
        <!-- Recommendation Dropdown Mobile -->
        <div class="mobile-dropdown-item">
            <div class="mobile-dropdown-title" data-dropdown="recommendation">
                <span><i data-lucide="lightbulb" style="width:18px; height:18px; display: inline-block; margin-right: 10px;"></i> Recommendation</span>
                <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </div>
            <div class="mobile-submenu" id="recommendation-submenu">
                <a href="fcrop_recommendation.php"><i data-lucide="leaf" class="w-4 h-4"></i> Crop Recommendation</a>
                <a href="ffertilizer_recommendation.php"><i data-lucide="droplet" class="w-4 h-4"></i> Fertilizer Recommendation</a>
            </div>
        </div>
        
        <!-- Trade Dropdown Mobile -->
        <div class="mobile-dropdown-item">
            <div class="mobile-dropdown-title" data-dropdown="trade">
                <span><i data-lucide="shopping-basket" style="width:18px; height:18px; display: inline-block; margin-right: 10px;"></i> Trade</span>
                <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </div>
            <div class="mobile-submenu" id="trade-submenu">
                <a href="ftradecrops.php"><i data-lucide="shopping-cart" class="w-4 h-4"></i> Trade Crops</a>
                <a href="fstock_crop.php"><i data-lucide="archive" class="w-4 h-4"></i> Crop Stocks</a>
                <a href="fselling_history.php"><i data-lucide="clipboard-list" class="w-4 h-4"></i> Selling History</a>
                <a href="fmarket_price.php"><i data-lucide="bar-chart-3" class="w-4 h-4"></i> Market Prices</a>
            </div>
        </div>
        
        <!-- Tools Dropdown Mobile -->
        <div class="mobile-dropdown-item">
            <div class="mobile-dropdown-title" data-dropdown="tools">
                <span><i data-lucide="wrench" style="width:18px; height:18px; display: inline-block; margin-right: 10px;"></i> Tools</span>
                <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </div>
            <div class="mobile-submenu" id="tools-submenu">
                <a href="fchatgpt.php"><i data-lucide="bot" class="w-4 h-4"></i> Chat Bot</a>
                <a href="fweather_prediction.php"><i data-lucide="cloud-lightning" class="w-4 h-4"></i> Weather Forecast</a>
                <a href="fnewsfeed.php"><i data-lucide="newspaper" class="w-4 h-4"></i> News Feed</a>
            </div>
        </div>
        
        <!-- Profile Link -->
        <a href="fprofile.php" class="mobile-menu-link">
            <i data-lucide="user" style="width:18px; height:18px; display: inline-block; margin-right: 10px;"></i> Profile
        </a>
        
        <!-- Logout Button -->
        <a href="flogout.php" class="mobile-home-btn mobile-logout-btn">
            <i data-lucide="log-out" style="width:18px; height:18px; display: inline-block; margin-right: 8px;"></i> Logout
        </a>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Hamburger Menu Functionality
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileMenuContainer = document.getElementById('mobileMenuContainer');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const closeMenuBtn = document.getElementById('closeMenuBtn');

    function openMobileMenu() {
        mobileMenuContainer.classList.add('active');
        mobileMenuOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        mobileMenuContainer.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if(hamburgerBtn) {
        hamburgerBtn.addEventListener('click', openMobileMenu);
    }
    
    if(closeMenuBtn) {
        closeMenuBtn.addEventListener('click', closeMobileMenu);
    }
    
    if(mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    }

    // Mobile dropdown toggles
    const dropdownTitles = document.querySelectorAll('.mobile-dropdown-title');
    dropdownTitles.forEach(title => {
        title.addEventListener('click', (e) => {
            e.stopPropagation();
            const dropdownType = title.getAttribute('data-dropdown');
            const submenu = document.getElementById(`${dropdownType}-submenu`);
            if (submenu) {
                submenu.classList.toggle('open');
                title.classList.toggle('open');
                const chevronIcon = title.querySelector('i:last-child');
                if (chevronIcon && submenu.classList.contains('open')) {
                    chevronIcon.style.transform = 'rotate(180deg)';
                } else if (chevronIcon) {
                    chevronIcon.style.transform = 'rotate(0deg)';
                }
            }
        });
    });

    // Close mobile menu when clicking on any link
    const mobileLinks = document.querySelectorAll('.mobile-menu-link, .mobile-submenu a, .mobile-home-btn');
    mobileLinks.forEach(link => {
        link.addEventListener('click', closeMobileMenu);
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            if (mobileMenuContainer.classList.contains('active')) {
                closeMobileMenu();
            }
        }
    });
</script>

</body>
</html>
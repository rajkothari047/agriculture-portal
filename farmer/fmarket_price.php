<?php
session_start();
if (!isset($_SESSION['farmer_login_user'])) {
    header("location: ../index.php");
    exit();
}
include '../sql.php';

// Get user info
$user_check = $_SESSION['farmer_login_user'];
$query4 = "SELECT * FROM farmerlogin WHERE email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Market Price Portal - Farmer Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
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
        }

        /* Gradient Header - Matching your reference pages */
        .section-shaped-custom {
            background: linear-gradient(180deg, #000000 0%, #062606 100%);
            padding-top: 5rem !important;
            padding-bottom: 8rem !important; 
        }

        /* Main Card - Pulled up like reference */
        .main-card {
            border-radius: 1.5rem !important;
            border: none !important;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
            margin-top: -100px; 
            overflow: hidden;
        }

        .card-header-custom {
            background: #ffffff !important;
            border-bottom: 1px solid #f1f3f5 !important;
            padding: 2rem 1.5rem 1.5rem !important;
        }

        /* Form Input Styling - Matching reference */
        .form-group label {
            font-weight: 700;
            font-size: 0.8rem;
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

        /* Price Display Card */
        .price-card-modern {
            background: linear-gradient(135deg, #fffaf4 0%, #ffffff 100%);
            border: 1px solid #ffe8cc;
            border-left: 6px solid var(--color-accent-terracotta);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 20px rgba(184, 92, 56, 0.08);
        }

        .current-price-large {
            font-size: 64px;
            font-weight: 800;
            color: var(--color-accent-terracotta);
            line-height: 1;
            margin: 15px 0;
        }

        .price-range-text {
            font-size: 18px;
            color: #555;
        }

        .market-badge {
            display: inline-block;
            background: var(--color-secondary-green);
            color: white;
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
        }

        .update-time {
            font-size: 12px;
            color: #888;
            margin-top: 10px;
        }

        /* Button Styles - Matching reference */
        .btn-theme {
            background: linear-gradient(135deg, var(--color-secondary-green) 0%, #2d4a15 100%);
            border: none;
            border-radius: 12px;
            padding: 16px 35px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            color: white;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .btn-theme:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(79, 119, 45, 0.3);
            background: var(--color-accent-terracotta);
        }

        .btn-outline-theme {
            background: transparent;
            border: 2px solid var(--color-secondary-green);
            color: var(--color-secondary-green);
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-theme:hover {
            background: var(--color-secondary-green);
            color: white;
            transform: translateY(-2px);
        }

        /* Table Styles */
        .trend-table {
            width: 100%;
            border-collapse: collapse;
        }

        .trend-table th {
            background: var(--color-primary-dark);
            color: white;
            padding: 14px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
        }

        .trend-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
        }

        .trend-up { color: #28a745; font-weight: bold; }
        .trend-down { color: #dc3545; font-weight: bold; }
        .trend-stable { color: #ffc107; font-weight: bold; }

        /* Info Alert */
        .info-alert {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            border-radius: 12px;
            padding: 1.2rem;
            margin-top: 1.5rem;
        }

        /* Loading Spinner */
        .loading-spinner {
            text-align: center;
            padding: 60px;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e9ecef;
            border-top: 4px solid var(--color-secondary-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ========== RESPONSIVE MEDIA QUERIES (MATCHING YOUR REFERENCE) ========== */
        
        /* Tablet and Mobile Devices */
        @media (max-width: 992px) {
            .section-shaped-custom {
                padding-top: 4rem !important;
                padding-bottom: 6rem !important;
            }
            .main-card {
                margin-top: -80px;
            }
            .display-3 {
                font-size: 2.2rem !important;
            }
            .lead {
                font-size: 1rem !important;
            }
        }
        
        /* Mobile Devices */
        @media (max-width: 768px) {
            .section-shaped-custom {
                padding-top: 3rem !important;
                padding-bottom: 5rem !important;
            }
            .main-card {
                margin-top: -60px;
                border-radius: 1.2rem !important;
            }
            .card-header-custom {
                padding: 1.5rem 1rem 1.2rem !important;
            }
            .card-body.p-lg-5 {
                padding: 1.5rem !important;
            }
            .display-3 {
                font-size: 1.6rem !important;
            }
            .lead {
                font-size: 0.9rem !important;
            }
            .badge.badge-pill {
                font-size: 0.7rem;
                padding: 0.4rem 1rem !important;
            }
            .current-price-large {
                font-size: 44px;
            }
            .price-range-text {
                font-size: 14px;
            }
            .btn-theme {
                width: 100%;
                padding: 14px 25px;
                font-size: 0.85rem;
            }
            .btn-outline-theme {
                width: 100%;
                padding: 10px 20px;
                font-size: 0.85rem;
            }
            .filter-grid {
                gap: 15px;
            }
            .form-group label {
                font-size: 0.75rem;
                margin-bottom: 6px;
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
            .price-card-modern {
                padding: 1.2rem;
            }
            .price-card-modern .row {
                flex-direction: column;
                text-align: center;
            }
            .market-badge {
                display: inline-block;
                margin-bottom: 10px;
            }
            .trend-table th, .trend-table td {
                font-size: 11px;
                padding: 8px;
            }
            .info-alert {
                padding: 1rem;
                font-size: 0.85rem;
            }
            .loading-spinner {
                padding: 40px;
            }
        }
        
        /* Small Mobile Devices */
        @media (max-width: 480px) {
            .section-shaped-custom {
                padding-top: 2.5rem !important;
                padding-bottom: 4rem !important;
            }
            .main-card {
                margin-top: -40px;
            }
            .display-3 {
                font-size: 1.3rem !important;
            }
            .lead {
                font-size: 0.8rem !important;
            }
            .card-header-custom h2 {
                font-size: 1.3rem;
            }
            .card-header-custom p {
                font-size: 0.8rem;
            }
            .current-price-large {
                font-size: 36px;
            }
            .price-range-text {
                font-size: 12px;
            }
            .input-group-custom select, 
            .input-group-custom input {
                height: 38px;
                font-size: 0.85rem;
            }
            .trend-table th, .trend-table td {
                font-size: 10px;
                padding: 6px;
            }
            .btn-outline-theme {
                font-size: 0.75rem;
            }
        }
        
        /* Landscape Mode for Mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .section-shaped-custom {
                padding-top: 2rem !important;
                padding-bottom: 3rem !important;
            }
            .main-card {
                margin-top: -50px;
            }
            .display-3 {
                font-size: 1.4rem !important;
            }
        }
        
        /* Large Desktop Screens */
        @media (min-width: 1400px) {
            .container {
                max-width: 1320px;
            }
            .display-3 {
                font-size: 3.5rem;
            }
            .current-price-large {
                font-size: 72px;
            }
        }
        
        /* Touch-friendly adjustments */
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
</head>
<body id="top">

<?php include ('fnav.php'); ?>

<!-- Hero Section with Gradient - Matching reference pages -->
<section class="section section-shaped section-lg section-shaped-custom">
    <div class="container text-center">
        <span class="badge badge-pill badge-success mb-3 px-4 py-2">REAL-TIME MARKET DATA</span>
        <h1 class="display-3 text-white font-weight-bold mb-2">Live Market Prices</h1>
        <p class="lead text-white opacity-8">Get real-time vegetable prices from government APIs</p>
    </div>
</section>

<!-- Main Content Section -->
<section class="section pt-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                
                <div class="card main-card">
                    <div class="card-header-custom text-center">
                        <h2 class="h2 mb-0 font-weight-bold" style="color: var(--color-primary-dark);">
                            <i class="ni ni-shop mr-2"></i> Market Price Search
                        </h2>
                        <p class="text-muted mt-2">Select your location and crop to view current market rates</p>
                    </div>
                    
                    <div class="card-body p-lg-5 p-4">
                        
                        <!-- Search Filters -->
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="form-group">
                                    <label>Select State</label>
                                    <div class="input-group-custom">
                                        <i class="ni ni-map-big"></i>
                                        <select id="state" onchange="loadMarkets()">
                                            <option value="">Choose Crop...</option>
                                            <?php
                                            $states_query = "SELECT DISTINCT state FROM current_prices WHERE state IS NOT NULL AND state != '' ORDER BY state";
                                            $states_result = mysqli_query($conn, $states_query);
                                            if (mysqli_num_rows($states_result) > 0) {
                                                while($state = mysqli_fetch_assoc($states_result)) {
                                                    echo "<option value='" . htmlspecialchars($state['state']) . "'>" . htmlspecialchars($state['state']) . "</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>No states available</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="form-group">
                                    <label>Select Market</label>
                                    <div class="input-group-custom">
                                        <i class="ni ni-building"></i>
                                        <select id="market">
                                            <option value="">First Select State</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="form-group">
                                    <label>Select Commodity</label>
                                    <div class="input-group-custom">
                                        <i class="ni ni-favourite-28"></i>
                                        <select id="commodity">
                                            <option value="">Select Commodity</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-2">
                            <button class="btn-theme btn-lg px-5" onclick="getCurrentPrice()">
                                <i class="ni ni-zoom-split-in mr-2"></i> Get Today's Price
                            </button>
                        </div>
                        
                        <!-- Results Area -->
                        <div id="resultsArea" class="mt-5">
                            <div class="info-alert">
                                <i class="ni ni-bulb-61 mr-2"></i>
                                <strong>How to use:</strong><br>
                                1. Select your state from the dropdown<br>
                                2. Choose a market (optional - shows all markets if skipped)<br>
                                3. Select the vegetable/crop you want to check<br>
                                4. Click "Get Today's Price" to see current market rates
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php require("footer.php"); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Load markets based on selected state
function loadMarkets() {
    const state = document.getElementById('state').value;
    const marketSelect = document.getElementById('market');
    const commoditySelect = document.getElementById('commodity');
    
    if (state) {
        marketSelect.innerHTML = '<option value="">Loading markets...</option>';
        commoditySelect.innerHTML = '<option value="">Select Commodity</option>';
        
        fetch(`Market_Price/get_markets.php?state=${encodeURIComponent(state)}`)
            .then(response => response.json())
            .then(data => {
                marketSelect.innerHTML = '<option value="">All Markets</option>';
                if (data.length > 0) {
                    data.forEach(market => {
                        marketSelect.innerHTML += `<option value="${market.replace(/"/g, '&quot;')}">${market}</option>`;
                    });
                } else {
                    marketSelect.innerHTML = '<option value="">No markets found</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                marketSelect.innerHTML = '<option value="">Error loading markets</option>';
            });
        
        // Load commodities for this state
        fetch(`Market_Price/get_commodities.php?state=${encodeURIComponent(state)}`)
            .then(response => response.json())
            .then(data => {
                commoditySelect.innerHTML = '<option value="">Select Commodity</option>';
                if (data.length > 0) {
                    data.forEach(commodity => {
                        commoditySelect.innerHTML += `<option value="${commodity.replace(/"/g, '&quot;')}">${commodity}</option>`;
                    });
                }
            });
    } else {
        marketSelect.innerHTML = '<option value="">First Select State</option>';
        commoditySelect.innerHTML = '<option value="">Select Commodity</option>';
    }
}

// Get current price
async function getCurrentPrice() {
    const state = document.getElementById('state').value;
    const market = document.getElementById('market').value;
    const commodity = document.getElementById('commodity').value;
    
    if (!state) {
        alert("Please select a state first");
        return;
    }
    if (!commodity) {
        alert("Please select a commodity");
        return;
    }
    
    showLoading();
    
    const formData = new FormData();
    formData.append('state', state);
    formData.append('market', market);
    formData.append('commodity', commodity);
    
    try {
        const response = await fetch('Market_Price/get_current_price.php', { 
            method: 'POST', 
            body: formData 
        });
        const data = await response.json();
        
        if (data.success && data.price) {
            displayCurrentPrice(data.price, commodity);
        } else {
            showNoData(commodity, state);
        }
    } catch (error) {
        console.error('Error:', error);
        showNoData(commodity, state);
    }
}

// Display current price
function displayCurrentPrice(price, commodity) {
    const date = new Date(price.updated_at);
    const formattedTime = date.toLocaleString('en-IN', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
    
    document.getElementById('resultsArea').innerHTML = `
        <div class="price-card-modern">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-left">
                    <span class="market-badge">${price.market_name}</span>
                    <h2 class="mt-3 mb-1" style="color: var(--color-primary-dark);">${commodity}</h2>
                    <p class="text-muted mb-0"><i class="ni ni-pin-3"></i> ${price.state}</p>
                </div>
                <div class="col-md-6 text-center">
                    <div class="current-price-large">₹${Number(price.modal_price).toLocaleString('en-IN')}</div>
                    <div class="price-range-text">
                        Range: ₹${Number(price.min_price).toLocaleString('en-IN')} - ₹${Number(price.max_price).toLocaleString('en-IN')}
                    </div>
                    <div class="update-time">
                        <i class="ni ni-watch-time"></i> Updated: ${formattedTime}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <button class="btn-outline-theme" onclick="viewHistory('${commodity}', '${price.market_name}', '${price.state}')">
                <i class="ni ni-chart-bar-32 mr-2"></i> View Last 7 Days Price Trend
            </button>
        </div>
        
        <div class="info-alert mt-4">
            <i class="ni ni-bulb-61 mr-2"></i>
            <strong>Selling Tips:</strong><br>
            • Compare this price with other markets before selling<br>
            • Check the 7-day trend to decide the best selling day<br>
            • Prices shown are per quintal (100 kg)<br>
            • Actual price may vary based on quality and negotiation
        </div>
    `;
}

// View price history
async function viewHistory(commodity, market, state) {
    showLoading("Loading price trend data...");
    
    const formData = new FormData();
    formData.append('commodity', commodity);
    formData.append('market', market);
    
    try {
        const response = await fetch('Market_Price/get_price_history.php', { 
            method: 'POST', 
            body: formData 
        });
        const data = await response.json();
        
        if (data.success && data.history.length > 0) {
            displayHistory(data.history, commodity, market, data.trend);
        } else {
            document.getElementById('resultsArea').innerHTML = `
                <div class="info-alert">
                    <i class="ni ni-fat-remove"></i>
                    No historical data available for ${commodity} in ${market}<br>
                    <button class="btn-outline-theme mt-3" onclick="getCurrentPrice()">← Back to Current Price</button>
                </div>
            `;
        }
    } catch (error) {
        document.getElementById('resultsArea').innerHTML = `
            <div class="info-alert">
                Error loading history. Please try again.<br>
                <button class="btn-outline-theme mt-3" onclick="getCurrentPrice()">← Back to Current Price</button>
            </div>
        `;
    }
}

// Display history table
function displayHistory(history, commodity, market, trend) {
    let html = `
        <div class="price-card-modern">
            <h3 class="text-center mb-3" style="color: var(--color-primary-dark);">
                <i class="ni ni-chart-bar-32"></i> Price Trend for ${commodity}
            </h3>
            <p class="text-center text-muted">Market: ${market}</p>
            <div class="info-alert mb-4">${trend}</div>
            <div style="overflow-x: auto;">
                <table class="trend-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Price (₹/quintal)</th>
                            <th>Change</th>
                        </tr>
                    </thead>
                    <tbody>
    `;
    
    let prevPrice = null;
    history.forEach(record => {
        let change = '', changeClass = '';
        if (prevPrice !== null) {
            let diff = record.modal_price - prevPrice;
            if (diff > 0) { 
                change = `↑ +${diff}`; 
                changeClass = 'trend-up'; 
            } else if (diff < 0) { 
                change = `↓ ${diff}`; 
                changeClass = 'trend-down'; 
            } else { 
                change = '→ Stable'; 
                changeClass = 'trend-stable'; 
            }
        } else {
            change = '-';
        }
        
        const date = new Date(record.price_date);
        const formattedDate = date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
        
        html += `
            <tr>
                <td>${formattedDate}</td>
                <td><strong>₹${Number(record.modal_price).toLocaleString('en-IN')}</strong></td>
                <td class="${changeClass}">${change}</td>
            </tr>
        `;
        prevPrice = record.modal_price;
    });
    
    // Add prediction based on trend
    let prediction = '';
    if (history.length >= 2) {
        const lastTwo = history.slice(0, 2);
        if (lastTwo[0].modal_price > lastTwo[1].modal_price) {
            prediction = 'Price is rising! Consider selling soon for better returns.';
        } else if (lastTwo[0].modal_price < lastTwo[1].modal_price) {
            prediction = 'Price is falling. Consider storing if possible, or sell now to avoid further loss.';
        } else {
            prediction = 'Price is stable. Good time to sell.';
        }
    }
    
    html += `
                    </tbody>
                </table>
            </div>
            ${prediction ? `<div class="info-alert mt-4"><i class="ni ni-bulb-61 mr-2"></i> ${prediction}</div>` : ''}
            <div class="text-center mt-4">
                <button class="btn-outline-theme" onclick="getCurrentPrice()">← Back to Today's Price</button>
            </div>
        </div>
    `;
    
    document.getElementById('resultsArea').innerHTML = html;
}

// Show loading state
function showLoading(msg = "Fetching latest market prices...") {
    document.getElementById('resultsArea').innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p class="text-muted">${msg}</p>
        </div>
    `;
}

// Show no data message
function showNoData(commodity, state) {
    document.getElementById('resultsArea').innerHTML = `
        <div class="info-alert">
            <i class="ni ni-fat-remove"></i>
            <strong>No price data available for ${commodity} in ${state}</strong><br><br>
            Possible reasons:<br>
            • Prices not updated for this state yet<br>
            • Try a different commodity or market<br>
            • Run API fetch from admin panel to update prices<br><br>
            <button class="btn-outline-theme mt-2" onclick="getCurrentPrice()">Try Again</button>
        </div>
    `;
}

// Auto-load if state preselected
if (document.getElementById('state').value) {
    loadMarkets();
}
</script>

</body>
</html>
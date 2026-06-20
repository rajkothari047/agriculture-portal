<?php
include ('fsession.php');
require('../sql.php'); 
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit;
}

$user_check = $_SESSION['farmer_login_user'];

// 1. Get the Farmer ID (para1) from the login session
$query4 = "SELECT farmer_id FROM farmerlogin WHERE email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['farmer_id']; 

/**
 * 2. DYNAMIC SELLING HISTORY QUERY
 * We link 'orders' (o) to 'farmer_crops_trade' (f)
 * f.Trade_crop matches o.crop_name
 * f.farmer_fkid matches the logged-in farmer's ID
 */
$sql = "SELECT DISTINCT o.crop_name, o.quantity, o.total_price, o.order_date 
        FROM orders o
        INNER JOIN farmer_crops_trade f ON o.crop_name = f.Trade_crop
        WHERE f.farmer_fkid = '$para1' 
        AND o.status = 1
        ORDER BY o.order_date DESC";

$result = mysqli_query($conn, $sql);

// Prepare data for the list and charts
$chart_data = [];
$total_revenue = 0;
$crop_aggregation = [];

if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $cropName = ucfirst($row["crop_name"]);
        $price = (float)$row["total_price"];

        $chart_data[] = [
            'crop' => $cropName,
            'quantity' => $row["quantity"],
            'price' => $price,
            'date' => $row['order_date']
        ];

        $total_revenue += $price;

        if (!isset($crop_aggregation[$cropName])) {
            $crop_aggregation[$cropName] = 0;
        }
        $crop_aggregation[$cropName] += $price;
    }
}

// 3. Prepare JSON for Chart.js
$json_chart_labels = json_encode(array_keys($crop_aggregation));
$json_chart_prices = json_encode(array_values($crop_aggregation));
$chart_colors = ['#B85C38', '#4F772D', '#062c06', '#E63946', '#F4A261', '#2A9D8F'];
$json_chart_colors = json_encode($chart_colors);
?>

<!DOCTYPE html>
<html>
<?php include ('fheader.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/lucide@0.264.0/dist/lucide.min.js"></script>

<style>
    :root {
        --color-primary-dark: #0A3D0A;
        --color-accent-terracotta: #B85C38;
        --color-secondary-green: #4F772D;
        --color-bg-light: #F9F7F3;
        --color-text-dark: #1E293B;
    }

    /* BANNER STYLING */
    .page-banner {
        background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        min-height: 200px;
        height: auto;
        padding: 3rem 0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        border-bottom: 4px solid var(--color-accent-terracotta);
    }

    .banner-title {
        color: white;
        font-size: 2rem;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-bottom: 0;
        flex-wrap: wrap;
    }

    i[data-lucide] {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    i[data-lucide] svg {
        display: inline-block !important;
        height: 1.25em !important; 
        width: 1.25em !important;
        vertical-align: middle !important;
        stroke: currentColor !important; 
    }

    body {
        background-color: white !important;
        font-family: 'Arial', sans-serif;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Left Overview Panel */
    .left-panel {
        background-color: #ffffff; 
        padding: 2rem;
        width: 100%;
        margin: 0 auto;
        min-height: auto;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        height: 100%;
        border-radius: 8px;
    }

    .stat-box {
        background-color: var(--color-accent-terracotta) !important;
        color: white !important;
        padding: 1rem;
        margin-top: 1rem;
        text-align: center;
        font-weight: bold;
        width: 100%;
        max-width: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    /* Right Panel */
    .right-panel {
        padding: 0 1rem;
        width: 100%;
    }

    .control-box {
        background-color: var(--color-accent-terracotta) !important;
        padding: 1rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        display: flex;
        justify-content: flex-end; 
        gap: 10px;
        align-items: center;
        border-radius: 8px;
        flex-wrap: wrap;
    }
    
    .search-group {
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 100%;
    }

    .search-group input {
        border-radius: 4px 0 0 4px;
        border: none;
        padding: 10px 15px;
        width: 100%;
        height: 45px;
        outline: none;
    }

    .search-icon-btn {
        background-color: white !important;
        color: var(--color-accent-terracotta) !important;
        height: 45px;
        border: none;
        padding: 0 15px;
        display: flex;
        align-items: center;
        border-radius: 0 4px 4px 0;
    }

    .btn-graph {
        color: white !important;
        border: 1px solid rgba(255,255,255,0.5) !important;
        background-color: transparent;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .btn-graph:hover {
        background-color: white !important;
        color: var(--color-accent-terracotta) !important;
    }

    .history-entry {
        background-color: #ffffff;
        border-bottom: 1px solid #e0e0e0;
        padding: 15px;
        transition: background 0.2s;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .history-entry:hover {
        background-color: #fcfcfc;
    }

    .entry-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--color-primary-dark);
    }

    /* --- FIXED: Transaction Date and Price in Right Corner --- */
    .info-right {
        text-align: right;
    }
    
    .info-right p, 
    .info-right h6 {
        text-align: right;
    }
    
    /* View Details Button */
    .btn-detail-view {
        border-radius: 20px !important;
        padding: 8px 15px !important;
        font-size: 0.75rem !important;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .btn-detail-view:hover {
        background-color: var(--color-accent-terracotta) !important;
        color: white !important;
        border-color: var(--color-accent-terracotta) !important;
    }

    /* --- RESPONSIVE QUERIES --- */
    @media (min-width: 992px) {
        .page-banner {
            height: 250px;
        }
        .banner-title {
            font-size: 32px;
        }
        .left-panel {
            max-width: 85%;
            min-height: 320px;
            margin: 0 auto;
        }
        .right-panel {
            padding-right: 3rem; 
            padding-left: 1rem;
        }
        .search-group {
            max-width: 350px; 
        }
        .history-entry {
            border-radius: 0;
            margin-bottom: 0;
            box-shadow: none;
            border-left: 3px solid transparent;
        }
        .history-entry:hover {
            border-left: 3px solid var(--color-accent-terracotta);
        }
        /* Push info more to the right on desktop */
        .info-right {
            padding-right: 20px;
        }
    }

    @media (max-width: 991px) and (min-width: 768px) {
        .info-right {
            text-align: right;
            padding-right: 15px;
        }
        .btn-detail-view {
            padding: 6px 12px !important;
            font-size: 0.7rem !important;
        }
    }

    @media (max-width: 767px) {
        .info-right {
            text-align: left !important;
            margin-top: 12px;
            padding-left: 0;
        }
        .info-right p, 
        .info-right h6 {
            text-align: left !important;
        }
        .history-entry {
            padding: 12px;
        }
        .entry-icon {
            display: none;
        }
        .btn-detail-view {
            width: 100%;
            margin-top: 10px;
            padding: 10px !important;
        }
        .control-box {
            flex-direction: column;
            align-items: stretch;
        }
        .btn-graph {
            justify-content: center;
            width: 100%;
        }
        .search-group {
            max-width: 100%;
        }
        .left-panel {
            margin-bottom: 20px;
        }
        .banner-title {
            font-size: 1.4rem;
        }
        .banner-subtitle {
            font-size: 12px !important;
        }
    }
    
    @media (max-width: 480px) {
        .history-entry .col-md-4,
        .history-entry .col-md-5,
        .history-entry .col-md-3 {
            padding: 0 5px;
        }
        .card-title h6 {
            font-size: 0.9rem;
        }
        .text-muted small {
            font-size: 0.65rem;
        }
    }
</style>

<body id="top">
<?php include ('fnav.php'); ?>

<div class="page-banner">
    <div class="container text-center px-3">
        <h1 class="banner-title"><i data-lucide="hand-coins"></i> Manage Your Selling History</h1>
        <p class="banner-subtitle" style="color: rgba(255,255,255,0.9); font-size: 16px; margin-top: 10px; font-weight: 300;">
            <i data-lucide="info" style="width: 14px; height: 14px; margin-right: 5px;"></i>
            A comprehensive record of your crop sales, revenue insights, and transaction details.
        </p>
    </div>
</div>

<section class="section section-lg pt-0">
    <div class="container-fluid py-2 px-2 px-md-4" style="padding:0;">
        <div class="row no-gutters" style="margin:0;">
            <!-- LEFT COLUMN -->
            <div class="col-lg-3 col-12 mb-4 mb-lg-0" style="padding: 0 15px;">
                <div class="left-panel">
                    <h2 class="h4 font-weight-bold mb-2" style="color: var(--color-primary-dark);">
                        <i data-lucide="line-chart"></i> History Overview
                    </h2>
                    <p class="text-muted small mb-0">
                        <i data-lucide="list-checks"></i> Total Transactions: <b><?php echo count($chart_data); ?></b>
                    </p>
                    <div class="stat-box">
                        <h5 class="mb-1 small" style="color:white;"><i data-lucide="indian-rupee"></i> Total Revenue:</h5>
                        <p class="h3 mt-1 mb-0" style="color:white;">₹<?php echo number_format($total_revenue, 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-9 col-12" style="padding: 0 15px;">
                <div class="control-box">
                    <button class="btn btn-graph" data-toggle="modal" data-target="#dashboardModal">
                        <i data-lucide="bar-chart-2"></i> Dashboard Graph
                    </button>

                    <div class="search-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search entries dynamically...">
                        <div class="search-icon-btn">
                            <i data-lucide="search"></i>
                        </div>
                    </div>
                </div>

                <h4 class="text-primary-dark mb-3 mt-4">
                    <i data-lucide="receipt-text"></i> All Selling Entries
                </h4>
                
                <div id="history-list">
                    <?php if (count($chart_data) > 0): ?>
                        <?php foreach($chart_data as $entry): ?>
                        <div class="history-entry row align-items-center" style="margin:0;">
                            <div class="col-auto entry-icon pr-3">
                                <i data-lucide="package"></i>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <p class="mb-1 text-muted small mb-0"><i data-lucide="carrot"></i> Crop Sold</p>
                                <h6 class="entry-value mb-1 entry-crop-name font-weight-bold"><?php echo htmlspecialchars($entry['crop']); ?></h6>
                                <p class="mb-0 text-muted small">
                                    <i data-lucide="scale"></i> Quantity: <b><?php echo htmlspecialchars($entry['quantity']); ?> KG</b>
                                </p>
                            </div>
                            <!-- FIXED: Transaction Date and Price moved to Right Corner -->
                            <div class="col-md-5 col-sm-12 info-right">
                                <p class="mb-1 text-muted small mb-0"><i data-lucide="calendar"></i> Transaction Date</p>
                                <h6 class="entry-value mb-1"><?php echo date('d M, Y', strtotime($entry['date'])); ?></h6>
                                <p class="mb-0 text-muted small">
                                    <i data-lucide="indian-rupee"></i> Price: <b>₹<?php echo number_format($entry['price'], 2); ?></b>
                                </p>
                            </div>
                            <div class="col-md-3 col-sm-12 text-center mt-3 mt-md-0">
                                <button class="btn btn-sm btn-outline-secondary btn-detail-view" 
                                    data-toggle="modal" 
                                    data-target="#detailModal"
                                    data-crop="<?php echo htmlspecialchars($entry['crop']); ?>"
                                    data-qty="<?php echo htmlspecialchars($entry['quantity']); ?>"
                                    data-price="<?php echo number_format($entry['price'], 2); ?>"
                                    data-date="<?php echo date('d M, Y', strtotime($entry['date'])); ?>">
                                    <i data-lucide="eye"></i> View Details
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center mt-5">
                            <i data-lucide="cloud-off"></i> No selling history found.
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-warning text-center mt-5" id="no-results-alert" style="display:none;">
                        <i data-lucide="alert-triangle"></i> No entries match your search criteria.
                    </div>
                </div> 
            </div>
        </div>
    </div>
</section>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: var(--color-accent-terracotta); color: white;">
        <h5 class="modal-title" style="color:white;"><i data-lucide="info"></i> Transaction Info</h5>
        <button type="button" class="close" data-dismiss="modal"><span style="color:white;">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-12 text-center mb-3">
                <i data-lucide="file-text" style="width:50px; height:50px; color:var(--color-accent-terracotta);"></i>
            </div>
            <div class="col-6 text-muted">Crop:</div><div class="col-6 font-weight-bold" id="det-crop"></div>
            <div class="col-12"><hr></div>
            <div class="col-6 text-muted">Quantity:</div><div class="col-6 font-weight-bold" id="det-qty"></div>
            <div class="col-12"><hr></div>
            <div class="col-6 text-muted">Revenue:</div><div class="col-6 font-weight-bold text-success" id="det-price"></div>
            <div class="col-12"><hr></div>
            <div class="col-6 text-muted">Date:</div><div class="col-6 font-weight-bold" id="det-date"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Dashboard Modal -->
<div class="modal fade" id="dashboardModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl modal-dialog-centered"> 
    <div class="modal-content">
      <div class="modal-header" style="background: var(--color-primary-dark) !important; color:white;">
        <h5 class="modal-title" style="color:white;"><i data-lucide="bar-chart-3"></i> Revenue Performance</h5>
        <button type="button" class="close" data-dismiss="modal"><span style="color:white;">×</span></button>
      </div>
      <div class="modal-body p-3">
        <div class="row">
            <div class="col-md-8 mb-4 mb-md-0">
                <div class="chart-container w-100 h-100" style="position: relative; min-height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead><tr><th></th><th>Crop</th><th>Revenue</th></tr></thead>
                        <tbody id="legendTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require("footer.php");?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
jQuery.noConflict();
(function($) {
    const chartLabels = <?php echo $json_chart_labels; ?>;
    const chartPrices = <?php echo $json_chart_prices; ?>;
    const chartColors = <?php echo $json_chart_colors; ?>;
    let salesChartInstance = null;

    // Fixed Detail Button Logic - FIXED
    $(document).on('click', '.btn-detail-view', function() {
        var crop = $(this).data('crop');
        var qty = $(this).data('qty');
        var price = $(this).data('price');
        var date = $(this).data('date');
        
        $('#det-crop').text(crop);
        $('#det-qty').text(qty + ' KG');
        $('#det-price').text('₹' + price);
        $('#det-date').text(date);
    });

    // Search Logic
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        let count = 0;
        $('#history-list .history-entry').each(function() {
            const isMatch = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(isMatch);
            if(isMatch) count++;
        });
        $('#no-results-alert').toggle(count === 0 && value !== "");
    });

    // Graph Logic
    $('#dashboardModal').on('shown.bs.modal', function () {
        let html = '';
        if(chartLabels.length > 0) {
            chartLabels.forEach((crop, i) => {
                html += `<tr><td><span style="height:12px;width:12px;border-radius:50%;display:inline-block;background:${chartColors[i % chartColors.length]}"></span></td><td>${crop}</td><td>₹${Number(chartPrices[i]).toLocaleString('en-IN')}</td></tr>`;
            });
        } else {
            html = '<tr><td colspan="3" class="text-center">No data available</td></tr>';
        }
        $('#legendTableBody').html(html);

        const ctx = document.getElementById('salesChart').getContext('2d');
        if (salesChartInstance) salesChartInstance.destroy();
        
        if(chartLabels.length > 0) {
            salesChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{ label: 'Revenue (₹)', data: chartPrices, backgroundColor: chartColors.slice(0, chartLabels.length) }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    });

    $(document).ready(function(){ 
        if (typeof lucide !== 'undefined') lucide.replace(); 
    });
})(jQuery);
</script>
</body>
</html>
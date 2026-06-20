<?php
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

/* =========================
   ✅ NEW STOCK UPDATE LOGIC
   ========================= */

// Get all completed orders which are not yet updated
$order_sql = "SELECT * FROM orders WHERE status='Completed' AND stock_updated='0'";
$order_query = mysqli_query($conn, $order_sql);

while($order = mysqli_fetch_assoc($order_query)){

    $crop_name = $order['crop_name'];
    $qty_bought = $order['quantity'];

    // Reduce stock
    $update_stock = "UPDATE production_approx 
                     SET quantity = quantity - $qty_bought 
                     WHERE crop = '$crop_name'";

    mysqli_query($conn, $update_stock);

    // Mark as updated so it doesn't run again
    $mark_done = "UPDATE orders 
                  SET stock_updated='1' 
                  WHERE id='".$order['id']."'";

    mysqli_query($conn, $mark_done);
}

/* =========================
   EXISTING CODE (UNCHANGED)
   ========================= */

// Data extraction for Table & Chart
$data_sql = "SELECT crop, quantity FROM production_approx WHERE quantity > 0";
$data_query = mysqli_query($conn, $data_sql);

$crop_labels = []; 
$crop_quantities = []; 
$table_data = []; 

while($row = mysqli_fetch_assoc($data_query)){
    $table_data[] = $row;
    $crop_labels[] = $row['crop'];
    $crop_quantities[] = $row['quantity'];
}

$json_crop_labels = json_encode($crop_labels);
$json_crop_quantities = json_encode($crop_quantities);
?>

<!DOCTYPE html>
<html lang="en">
<?php include ('fheader.php'); ?>

<style>
    :root {
        --color-primary-dark: #0A3D0A;
        --color-accent-terracotta: #B85C38;
        --color-secondary-green: #4F772D;
        --color-bg-light: #F4F6F9;
        --color-text-dark: #1E293B;
    }
    
    body { background-color: var(--color-bg-light) !important; font-family: 'Open Sans', sans-serif; }

    .section-shaped-custom {
        background: linear-gradient(180deg, #000000 0%, #062606 100%);
        padding-top: 5rem !important;
        padding-bottom: 8rem !important; 
    }

    .main-content-moveup { margin-top: -80px; }

    .dashboard-card {
        border-radius: 1.25rem !important;
        border: none !important;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
        height: 100%;
    }

    .card-header-custom {
        background: #ffffff !important;
        border-bottom: 1px solid #f1f3f5 !important;
        padding: 1.5rem !important;
        border-top-left-radius: 1.25rem !important;
        border-top-right-radius: 1.25rem !important;
    }

    .table-header-theme {
        background-color: #f8f9fa !important;
        border-bottom: 2px solid var(--color-secondary-green) !important;
    }
    .table-header-theme th {
        color: var(--color-primary-dark) !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    
    .chart-canvas-container { height: 300px; position: relative; }

    .legend-table { font-size: 0.85rem; }
    .color-box {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 3px;
        margin-right: 10px;
    }

    /* ========== RESPONSIVE MEDIA QUERIES (ADDED ONLY) ========== */
    
    /* Tablet Devices */
    @media (max-width: 992px) {
        .section-shaped-custom {
            padding-top: 4rem !important;
            padding-bottom: 6rem !important;
        }
        
        .main-content-moveup {
            margin-top: -60px;
        }
        
        .display-3 {
            font-size: 2.2rem !important;
        }
        
        .chart-canvas-container {
            height: 250px;
        }
        
        .card-header-custom {
            padding: 1.2rem !important;
        }
        
        .card-header-custom h4 {
            font-size: 1.2rem;
        }
    }
    
    /* Mobile Devices */
    @media (max-width: 768px) {
        .section-shaped-custom {
            padding-top: 3rem !important;
            padding-bottom: 5rem !important;
        }
        
        .main-content-moveup {
            margin-top: -40px;
        }
        
        .display-3 {
            font-size: 1.6rem !important;
        }
        
        .badge.badge-pill {
            font-size: 0.7rem;
            padding: 0.3rem 0.8rem !important;
        }
        
        .dashboard-card {
            margin-bottom: 1rem;
        }
        
        .chart-canvas-container {
            height: 220px;
        }
        
        .card-header-custom {
            padding: 1rem !important;
        }
        
        .card-header-custom h4 {
            font-size: 1rem;
        }
        
        .card-body.p-4 {
            padding: 1.2rem !important;
        }
        
        /* Table responsiveness */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-header-theme th {
            font-size: 0.65rem;
            white-space: nowrap;
        }
        
        .table tbody td {
            font-size: 0.85rem;
            white-space: nowrap;
        }
        
        .legend-table {
            font-size: 0.75rem;
        }
        
        .legend-table td {
            padding: 0.3rem 0.2rem;
        }
        
        .color-box {
            width: 10px;
            height: 10px;
            margin-right: 6px;
        }
        
        hr.my-4 {
            margin: 1rem 0 !important;
        }
        
        h6.text-uppercase {
            font-size: 0.6rem !important;
            margin-bottom: 0.8rem !important;
        }
        
        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.75rem;
        }
        
        .card-footer {
            padding: 0.8rem !important;
        }
    }
    
    /* Small Mobile Devices */
    @media (max-width: 480px) {
        .section-shaped-custom {
            padding-top: 2.5rem !important;
            padding-bottom: 4rem !important;
        }
        
        .main-content-moveup {
            margin-top: -30px;
        }
        
        .display-3 {
            font-size: 1.3rem !important;
        }
        
        .section-shaped-custom p {
            font-size: 0.8rem;
        }
        
        .chart-canvas-container {
            height: 180px;
        }
        
        .card-body.p-4 {
            padding: 0.8rem !important;
        }
        
        .legend-table {
            font-size: 0.7rem;
        }
        
        .table-header-theme th {
            font-size: 0.55rem;
        }
        
        .table tbody td {
            font-size: 0.75rem;
        }
        
        .badge-dot .status {
            font-size: 0.7rem;
        }
        
        .card-header-custom h4 i {
            font-size: 0.9rem;
        }
    }
    
    /* Landscape Mode */
    @media (max-width: 768px) and (orientation: landscape) {
        .section-shaped-custom {
            padding-top: 2rem !important;
            padding-bottom: 3rem !important;
        }
        
        .main-content-moveup {
            margin-top: -35px;
        }
        
        .chart-canvas-container {
            height: 200px;
        }
        
        .row > .col-lg-6 {
            margin-bottom: 1rem !important;
        }
    }
    
    /* Table column stacking for very small screens (optional - keeps horizontal scroll) */
    @media (max-width: 576px) {
        .table-responsive {
            overflow-x: auto;
            display: block;
            width: 100%;
        }
        
        #myTable {
            min-width: 300px;
        }
        
        .legendContainer .table-responsive {
            overflow-x: visible;
        }
    }
</style>

<body id="top">
    <?php include ('fnav.php'); ?>

    <section class="section section-shaped section-lg section-shaped-custom">
        <div class="container text-center">
            <span class="badge badge-pill badge-success mb-2 px-3 py-1">ANALYTICS CENTER</span>
            <h1 class="display-3 text-white font-weight-bold mb-1">Stock Portfolio</h1>
            <p class="text-white opacity-8">Real-time overview of your current harvest and inventory distribution.</p>
        </div>
    </section>

    <section class="section pt-0 main-content-moveup">
        <div class="container">
            <div class="row">
                
                <div class="col-lg-6 mb-4">
                    <div class="card dashboard-card">
                        <div class="card-header-custom text-center">
                            <h4 class="mb-0 font-weight-bold" style="color: var(--color-primary-dark)">
                                <i class="ni ni-chart-pie-35 mr-2 text-success"></i> Inventory Share
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <div id="chart-wrapper" class="chart-canvas-container">
                                <canvas id="cropChart"></canvas>
                            </div>
                            <hr class="my-4">
                            <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 0.7rem;">Crop Legend</h6>
                            <div id="legendContainer" class="table-responsive"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card dashboard-card">
                        <div class="card-header-custom text-center">
                            <h4 class="mb-0 font-weight-bold" style="color: var(--color-primary-dark)">
                                <i class="ni ni-bullet-list-67 mr-2 text-success"></i> Stock Details
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-items-center mb-0" id="myTable">
                                    <thead class="table-header-theme">
                                        <tr>
                                            <th class="text-center">Crop Name</th>
                                            <th class="text-center">Current Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-dark font-weight-600"> 
                                        <?php 
                                        if (!empty($table_data)) {
                                            foreach($table_data as $res){ 
                                        ?> 
                                        <tr>
                                            <td class="text-center"> 
                                                <span class="badge badge-dot mr-4">
                                                    <i class="bg-success"></i>
                                                    <span class="status"><?php echo htmlspecialchars($res['crop']); ?></span>
                                                </span>
                                            </td>
                                            <td class="text-center"> <?php echo number_format($res['quantity']); ?> <small class="text-muted">KG</small> </td>
                                        </tr>
                                        <?php 
                                            }
                                        } else {
                                            echo '<tr><td colspan="2" class="text-center py-5 text-muted">No active crop listings found.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>     
                        </div>
                        <div class="card-footer bg-white border-0 text-center pb-4">
                            <a href="ftradecrops.php" class="btn btn-sm btn-outline-success rounded-pill">Update Inventory</a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <?php require("footer.php");?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        const cropLabels = <?php echo $json_crop_labels; ?>;
        const cropQuantities = <?php echo $json_crop_quantities; ?>;
        const baseColors = ['#0A3D0A', '#B85C38', '#4F772D', '#2D6A4F', '#D4A373', '#E9C46A', '#264653', '#E76F51']; 
        const chartColors = cropLabels.map((_, index) => baseColors[index % baseColors.length]);

        function generateLegendTable(labels, colors) {
            let tableHtml = '<table class="table table-borderless table-sm legend-table mb-0">';
            for (let i = 0; i < labels.length; i++) {
                tableHtml += `<tr><td style="width:20px"><span class="color-box" style="background-color: ${colors[i]};"></span></td><td class="font-weight-600">${labels[i]}</td></tr>`;
            }
            tableHtml += '</table>';
            document.getElementById('legendContainer').innerHTML = tableHtml;
        }

        document.addEventListener("DOMContentLoaded", function() {
            if (cropQuantities.length > 0) {
                const ctx = document.getElementById('cropChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut', 
                    data: {
                        labels: cropLabels,
                        datasets: [{
                            data: cropQuantities,
                            backgroundColor: chartColors,
                            borderWidth: 0,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
                generateLegendTable(cropLabels, chartColors);
            } else {
                document.getElementById('chart-wrapper').innerHTML = '<div class="text-center py-5 text-muted">No data to display</div>';
            }
        });
    </script>
</body>
</html>
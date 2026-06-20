<?php
include('csession.php');
include('../sql.php');

if (!isset($_SESSION['customer_login_user'])) {
    header("location: ../index.php");
    exit;
}

$user = $_SESSION['customer_login_user'];

// Fetch completed orders (status = 1 means completed/verified)
$query = "SELECT * FROM orders WHERE customer_user = '$user' AND status = 1 ORDER BY order_date DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Statistics for completed orders
$total_query = "SELECT SUM(total_price) as total_spent, COUNT(*) as order_count, SUM(quantity) as total_kg FROM orders WHERE customer_user = '$user' AND status = 1";
$total_result = mysqli_query($conn, $total_query);

if (!$total_result) {
    die("Query Error: " . mysqli_error($conn));
}

$total_data = mysqli_fetch_assoc($total_result);
$grand_total = $total_data['total_spent'] ?? 0;
$order_count = $total_data['order_count'] ?? 0;
$total_kg = $total_data['total_kg'] ?? 0;

// Get latest order date
$latest_query = "SELECT MAX(order_date) as last_date FROM orders WHERE customer_user = '$user' AND status = 1";
$latest_result = mysqli_query($conn, $latest_query);
$latest_data = mysqli_fetch_assoc($latest_result);
$last_order_date = $latest_data['last_date'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>KisanMitra | Transaction Ledger · Completed Orders</title>
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
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        /* ── FULL WIDTH BANNER ── */
        .hero-banner {
            width: 100%;
            height: 300px;
            position: relative;
            overflow: hidden;
            background: var(--green-dark);
        }
        .hero-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.55;
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

        /* ── UNIQUE GRID STRUCTURE (2fr + 1fr) ── */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 28px;
            align-items: start;
        }

        /* ── SUMMARY CARDS (3 columns) ── */
        .summary-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: var(--white);
            padding: 22px 20px;
            border-radius: 0px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.04);
            border: 1px solid var(--border);
            transition: transform 0.2s ease, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }
        .stat-card i { 
            font-size: 1.8rem; 
            color: var(--green-mid);
        }
        .stat-card .stat-icon-terra { color: var(--terracotta); }
        .stat-card .stat-icon-gold { color: var(--gold); }
        .stat-val { 
            font-size: 1.7rem; 
            font-weight: 800; 
            color: var(--green-dark);
            letter-spacing: -0.02em;
        }
        .stat-lbl { 
            font-size: 0.7rem; 
            color: var(--muted); 
            font-weight: 700; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── CONTENT CARD (main area) ── */
        .content-card {
            background: var(--white);
            border-radius: 0px;
            border: 1px solid var(--border);
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 28px;
        }

        .card-header {
            padding: 20px 28px;
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .card-header h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--green-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline {
            background: var(--green-dark);
            color: #fff;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 0px;
            font-size: 0.75rem;
            font-weight: 700;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-outline:hover {
            background: var(--green-mid);
            transform: translateY(-2px);
        }

        /* Status Pill (success) */
        .status-pill {
            background: var(--success-light);
            color: var(--success);
            border: 1px solid rgba(46,125,64,0.2);
            padding: 5px 14px;
            border-radius: 0px;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Desktop Table */
        .desktop-tbl {
            overflow-x: auto;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table thead th {
            padding: 18px 24px;
            text-align: left;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            background: #FDFCF9;
            border-bottom: 1px solid var(--border);
        }
        .data-table tbody td {
            padding: 20px 24px;
            border-bottom: 1px solid #F3EFE8;
            vertical-align: middle;
        }
        .data-table tbody tr:hover {
            background: #FEFCF8;
        }

        .order-id-mono {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 0.7rem;
            background: #F3EFE8;
            display: inline-block;
            padding: 4px 10px;
            border-radius: 0px;
            color: var(--muted);
        }
        .crop-name-lg {
            font-weight: 800;
            font-size: 1rem;
            color: var(--text);
        }
        .qty-badge {
            font-size: 0.65rem;
            font-weight: 700;
            background: var(--green-light);
            padding: 3px 10px;
            border-radius: 0px;
            color: var(--green-mid);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .price-amount {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--green-dark);
        }

        /* Mobile Card List */
        .mob-list {
            display: none;
            padding: 20px;
        }
        .mobile-order-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 0px;
            padding: 16px;
            margin-bottom: 14px;
            transition: 0.1s;
        }
        .mobile-order-card .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        /* ── SIDEBAR CARDS ── */
        .sidebar-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .info-card {
            background: var(--white);
            padding: 24px;
            border-radius: 0px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .info-card h4 {
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stat-highlight {
            background: var(--green-light);
            border-radius: 0px;
            padding: 16px;
            margin: 15px 0;
            text-align: center;
        }
        .stat-highlight .number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--green-dark);
        }
        .insight-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .help-card-dark {
            background: var(--green-dark);
            color: white;
        }
        .help-card-dark h4 {
            color: #F4C542;
        }

        /* Extra insight banner */
        .insight-banner {
            background: var(--gold-light);
            border-radius: 0px;
            padding: 18px 22px;
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
            border: 1px solid rgba(200,150,12,0.2);
        }

        /* Order Lifecycle Items */
        .lifecycle-item {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .lifecycle-icon {
            width: 32px;
            height: 32px;
            background: var(--success-light);
            border-radius: 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--success);
        }
        .lifecycle-icon-gold {
            background: var(--gold-light);
            color: var(--gold);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .sidebar-section { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        }

        @media (max-width: 768px) {
            .summary-row { grid-template-columns: 1fr; gap: 14px; }
            .sidebar-section { grid-template-columns: 1fr; }
            .hero-banner { height: 240px; }
            .desktop-tbl { display: none; }
            .mob-list { display: block !important; }
            .main-container { padding: 0 16px; margin: -40px auto 60px; }
            .card-header { padding: 16px 20px; }
        }

        @media (max-width: 480px) {
            .hero-content h1 { font-size: 1.6rem; }
            .stat-val { font-size: 1.3rem; }
        }

        .spin-slow {
            animation: spin 2s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

<?php include('cnav.php'); ?>

<!-- FULL WIDTH BANNER (identical design) -->
<section class="hero-banner">
    <img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1600&q=80" alt="Harvest Banner">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-check-circle"></i> Completed Transactions
        </div>
        <h1 style="color: #fff; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 5vw, 3rem);">
            Ledger & <span style="color: #F4C542;">Settlements</span>
        </h1>
        <p style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">
            <i class="fas fa-user-check"></i> Verified orders for <strong><?php echo htmlspecialchars($user); ?></strong>
        </p>
    </div>
</section>

<div class="main-container">

    <!-- Summary Row (3 stats) -->
    <div class="summary-row">
        <div class="stat-card">
            <i class="fas fa-indian-rupee-sign stat-icon-gold"></i>
            <span class="stat-val">₹<?php echo number_format($grand_total, 0); ?></span>
            <span class="stat-lbl">Total Investment</span>
        </div>
        <div class="stat-card">
            <i class="fas fa-truck-fast stat-icon-terra"></i>
            <span class="stat-val"><?php echo $order_count; ?></span>
            <span class="stat-lbl">Orders Completed</span>
        </div>
        <div class="stat-card">
            <i class="fas fa-weight-hanging"></i>
            <span class="stat-val"><?php echo number_format($total_kg); ?> KG</span>
            <span class="stat-lbl">Total Volume</span>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- LEFT COLUMN: Main Orders Table -->
        <div class="main-content-area">
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-receipt" style="color: var(--green-mid);"></i> Recent Settlements</h3>
                    <a href="cbuy_crops.php" class="btn-outline"><i class="fas fa-plus-circle"></i> New Order</a>
                </div>

                <!-- Desktop Table -->
                <div class="desktop-tbl">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Product & Volume</th>
                                <th>Settlement Date</th>
                                <th>Net Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): 
                                while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><span class="order-id-mono"><i class="fas fa-hashtag"></i> <?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></span></td>
                                    <td>
                                        <div class="crop-name-lg"><?php echo ucfirst($row['crop_name']); ?></div>
                                        <div><span class="qty-badge"><i class="fas fa-weight-hanging"></i> <?php echo $row['quantity']; ?> KG</span></div>
                                    </td>
                                    <td style="font-weight: 500; font-size:0.85rem;">
                                        <?php echo date('d M Y', strtotime($row['order_date'])); ?>
                                        <div style="font-size:0.65rem; color:var(--muted);"><?php echo date('h:i A', strtotime($row['order_date'])); ?></div>
                                    </td>
                                    <td><span class="price-amount">₹<?php echo number_format($row['total_price'], 2); ?></span></td>
                                    <td><span class="status-pill"><i class="fas fa-check-circle"></i> Completed</span></td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="5" style="padding: 60px 20px; text-align: center;">
                                        <i class="fas fa-file-invoice" style="font-size: 3rem; color: var(--border); margin-bottom: 12px; display: block;"></i>
                                        <p style="color: var(--muted); font-weight: 500;">No completed orders yet.</p>
                                        <a href="cbuy_crops.php" class="btn-outline" style="margin-top: 15px; display: inline-block;">Browse Crops</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="mob-list">
                    <?php if (mysqli_num_rows($result) > 0): 
                        mysqli_data_seek($result, 0);
                        while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="mobile-order-card">
                            <div class="flex-between">
                                <span class="order-id-mono"><i class="fas fa-receipt"></i> #<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                <span class="status-pill"><i class="fas fa-check"></i> Success</span>
                            </div>
                            <div style="margin: 12px 0;">
                                <div style="font-weight: 800; font-size: 1.1rem;"><?php echo ucfirst($row['crop_name']); ?></div>
                                <div class="qty-badge" style="margin-top: 6px;"><i class="fas fa-weight-hanging"></i> <?php echo $row['quantity']; ?> KG</div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                                <span style="font-size: 0.7rem; color: var(--muted);"><i class="far fa-calendar"></i> <?php echo date('d M Y', strtotime($row['order_date'])); ?></span>
                                <span class="price-amount" style="font-size: 1rem;">₹<?php echo number_format($row['total_price'], 2); ?></span>
                            </div>
                        </div>
                    <?php endwhile; else: ?>
                        <div style="text-align: center; padding: 40px 20px;">
                            <i class="fas fa-box-open" style="font-size: 2.5rem; color: var(--border);"></i>
                            <p style="margin-top: 10px;">No transactions found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Extra insight banner (similar to notice) -->
            <div class="insight-banner">
                <i class="fas fa-shield-alt" style="color: var(--gold); font-size: 1.5rem;"></i>
                <p style="font-size: 0.8rem; color: #7A5C00; font-weight: 500;">All completed orders are verified by admin and recorded in your permanent ledger. You can download this statement anytime.</p>
            </div>
        </div>

        <!-- RIGHT COLUMN: Sidebar with insights & help -->
        <div class="sidebar-section">
            <!-- Summary Insights Card -->
            <div class="info-card">
                <h4><i class="fas fa-chart-line"></i> Financial Snapshot</h4>
                <div class="insight-row">
                    <span>Average order value</span>
                    <strong style="color: var(--green-dark);">₹<?php echo ($order_count > 0) ? number_format($grand_total / $order_count, 0) : 0; ?></strong>
                </div>
                <div class="insight-row">
                    <span>Total kg delivered</span>
                    <strong><?php echo number_format($total_kg); ?> KG</strong>
                </div>
                <div class="insight-row">
                    <span>Last completed</span>
                    <strong><?php echo $last_order_date ? date('d M Y', strtotime($last_order_date)) : '—'; ?></strong>
                </div>
                <div class="stat-highlight">
                    <div class="number"><?php echo $order_count; ?></div>
                    <div style="font-size:0.7rem; font-weight:600;">Successful deliveries</div>
                </div>
            </div>

            <!-- Order Journey (status flow) -->
            <div class="info-card">
                <h4><i class="fas fa-map-pin"></i> Order Lifecycle</h4>
                <div style="display: flex; flex-direction: column; gap: 18px;">
                    <div class="lifecycle-item">
                        <div class="lifecycle-icon"><i class="fas fa-check"></i></div>
                        <div><strong style="font-size:0.85rem;">Order placed</strong><div style="font-size:0.7rem; color:var(--muted);">Payment recorded</div></div>
                    </div>
                    <div class="lifecycle-item">
                        <div class="lifecycle-icon"><i class="fas fa-user-check"></i></div>
                        <div><strong style="font-size:0.85rem;">Admin verification</strong><div style="font-size:0.7rem; color:var(--muted);">Payment confirmed</div></div>
                    </div>
                    <div class="lifecycle-item">
                        <div class="lifecycle-icon"><i class="fas fa-truck"></i></div>
                        <div><strong style="font-size:0.85rem;">Logistics & delivery</strong><div style="font-size:0.7rem; color:var(--muted);">Completed successfully</div></div>
                    </div>
                    <div class="lifecycle-item">
                        <div class="lifecycle-icon lifecycle-icon-gold"><i class="fas fa-file-invoice"></i></div>
                        <div><strong style="font-size:0.85rem;">Ledger updated</strong><div style="font-size:0.7rem; color:var(--muted);">Permanent record</div></div>
                    </div>
                </div>
            </div>

            <!-- Help / Support Card (dark) -->
            <div class="info-card help-card-dark" style="background: var(--green-dark);">
                <h4 style="color: #F4C542;"><i class="fas fa-headset"></i> Support & Invoices</h4>
                <p style="font-size: 0.8rem; margin-bottom: 18px; color: #e2e8f0;">Need a tax invoice or have dispute regarding any transaction?</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="cprofile.php" style="background: rgba(255,255,240,0.15); color: white; padding: 6px 16px; border-radius: 0px; font-size:0.7rem; font-weight: 700; text-decoration: none;"><i class="fas fa-envelope"></i> Contact</a>
                    <a href="#" onclick="window.print();return false;" style="background: rgba(255,255,240,0.15); color: white; padding: 6px 16px; border-radius: 0px; font-size:0.7rem; font-weight: 700; text-decoration: none;"><i class="fas fa-print"></i> Print Ledger</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
</body>
</html>
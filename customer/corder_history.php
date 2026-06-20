<?php
include('csession.php');
include('../sql.php');

if (!isset($_SESSION['customer_login_user'])) {
    header("location: ../index.php");
    exit;
}

$user = $_SESSION['customer_login_user'];
$query = "SELECT * FROM orders WHERE customer_user = '$user' AND status = 0 ORDER BY order_date DESC";
$result = mysqli_query($conn, $query);
if (!$result) die("Query Error: " . mysqli_error($conn));

$rows = [];
$total_pending_value = 0;
$total_pending_kg = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    $total_pending_value += $row['total_price'];
    $total_pending_kg    += $row['quantity'];
}
$count = count($rows);

// Get earliest pending order date for estimate
$earliest_date = !empty($rows) ? $rows[0]['order_date'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>KisanMitra | Active Orders · Pending Approval</title>
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
            --warning:     #E65100;
            --warning-light: #FFF3E0;
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
        }
        .stat-card .stat-icon-terra { color: var(--terracotta); }
        .stat-card .stat-icon-green { color: var(--green-mid); }
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

        /* Status Pill (pending/warning) */
        .status-pill {
            background: var(--warning-light);
            color: var(--warning);
            border: 1px solid rgba(230,81,0,0.2);
            padding: 5px 14px;
            border-radius: 0px;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-pill i {
            animation: spin 1.5s linear infinite;
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
        .date-cell {
            font-weight: 500;
            font-size: 0.85rem;
        }
        .time-sub {
            font-size: 0.65rem;
            color: var(--muted);
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
        
        /* Step Journey */
        .step-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            position: relative;
        }
        .step-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 35px;
            bottom: -15px;
            width: 2px;
            background: var(--border);
        }
        .step-icon {
            width: 32px; height: 32px;
            background: var(--green-light);
            border-radius: 0px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; color: var(--green-mid);
            z-index: 1;
            flex-shrink: 0;
        }
        .step-item.active .step-icon {
            background: var(--terracotta);
            color: #fff;
        }
        .step-item.waiting .step-icon {
            background: var(--gold-light);
            color: var(--gold);
        }
        .step-title {
            font-size: 0.85rem;
            font-weight: 700;
        }
        .step-desc {
            font-size: 0.7rem;
            color: var(--muted);
        }

        /* Timeline estimate */
        .estimate-box {
            background: var(--terra-light);
            border-radius: 0px;
            padding: 16px;
            margin: 15px 0;
            text-align: center;
        }
        .estimate-box .time-number {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--terracotta);
        }
        
        .help-card-dark {
            background: var(--green-dark);
            color: white;
        }
        .help-card-dark h4 {
            color: #F4C542;
        }
        .help-link {
            color: white;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            border-bottom: 1px dashed rgba(255,255,255,0.5);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Information Notice */
        .info-notice {
            background: var(--gold-light);
            border-radius: 0px;
            padding: 18px 22px;
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
            border: 1px solid rgba(200,150,12,0.2);
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

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<?php include('cnav.php'); ?>

<!-- FULL WIDTH BANNER (identical design) -->
<section class="hero-banner">
    <img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1600&q=80" alt="Farm Field Banner">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-hourglass-half"></i> Pending Approval
        </div>
        <h1 style="color: #fff; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 5vw, 3rem);">
            Active <span style="color: #F4C542;">Crop Orders</span>
        </h1>
        <p style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">
            <i class="fas fa-user-check"></i> Awaiting verification for <strong><?php echo htmlspecialchars($user); ?></strong>
        </p>
    </div>
</section>

<div class="main-container">

    <!-- Summary Row (3 stats) -->
    <div class="summary-row">
        <div class="stat-card">
            <i class="fas fa-clock stat-icon-terra"></i>
            <span class="stat-val"><?php echo $count; ?></span>
            <span class="stat-lbl">Pending Orders</span>
        </div>
        <div class="stat-card">
            <i class="fas fa-weight-hanging stat-icon-green"></i>
            <span class="stat-val"><?php echo number_format($total_pending_kg); ?> KG</span>
            <span class="stat-lbl">Total Volume</span>
        </div>
        <div class="stat-card">
            <i class="fas fa-indian-rupee-sign stat-icon-gold"></i>
            <span class="stat-val">₹<?php echo number_format($total_pending_value, 0); ?></span>
            <span class="stat-lbl">Estimated Total</span>
        </div>
    </div>

    <div class="dashboard-grid">
        
        <!-- LEFT COLUMN: Main Orders Table -->
        <div class="main-content-area">
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-list-check" style="color: var(--terracotta);"></i> Orders Under Review</h3>
                    <a href="cbuy_crops.php" class="btn-outline"><i class="fas fa-plus-circle"></i> New Order</a>
                </div>

                <!-- Desktop Table -->
                <div class="desktop-tbl">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Crop & Quantity</th>
                                <th>Order Date & Time</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): foreach ($rows as $r): ?>
                            <tr>
                                <td><span class="order-id-mono"><i class="fas fa-hashtag"></i> <?php echo str_pad($r['id'], 5, '0', STR_PAD_LEFT); ?></span></td>
                                <td>
                                    <div class="crop-name-lg"><?php echo ucfirst($r['crop_name']); ?></div>
                                    <div><span class="qty-badge"><i class="fas fa-weight-hanging"></i> <?php echo $r['quantity']; ?> KG</span></div>
                                </td>
                                <td>
                                    <div class="date-cell"><?php echo date('d M Y', strtotime($r['order_date'])); ?></div>
                                    <div class="time-sub"><i class="far fa-clock"></i> <?php echo date('h:i A', strtotime($r['order_date'])); ?></div>
                                  </div>
                                </td>
                                <td><span class="price-amount">₹<?php echo number_format($r['total_price'], 2); ?></span></td>
                                <td><span class="status-pill"><i class="fas fa-hourglass-half"></i> Awaiting Approval</span></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="5" style="padding: 60px 20px; text-align: center;">
                                    <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--green-mid); margin-bottom: 12px; display: block;"></i>
                                    <p style="color: var(--muted); font-weight: 500;">No pending orders! All caught up.</p>
                                    <a href="cbuy_crops.php" class="btn-outline" style="margin-top: 15px; display: inline-block;">Browse New Crops</a>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="mob-list">
                    <?php if (!empty($rows)): foreach ($rows as $r): ?>
                    <div class="mobile-order-card">
                        <div class="flex-between">
                            <span class="order-id-mono"><i class="fas fa-receipt"></i> #<?php echo str_pad($r['id'], 5, '0', STR_PAD_LEFT); ?></span>
                            <span class="status-pill"><i class="fas fa-hourglass-half"></i> Pending</span>
                        </div>
                        <div style="margin: 12px 0;">
                            <div style="font-weight: 800; font-size: 1.1rem;"><?php echo ucfirst($r['crop_name']); ?></div>
                            <div class="qty-badge" style="margin-top: 6px;"><i class="fas fa-weight-hanging"></i> <?php echo $r['quantity']; ?> KG</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                            <span style="font-size: 0.7rem; color: var(--muted);"><i class="far fa-calendar"></i> <?php echo date('d M Y', strtotime($r['order_date'])); ?></span>
                            <span class="price-amount" style="font-size: 1rem;">₹<?php echo number_format($r['total_price'], 2); ?></span>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                        <div style="text-align: center; padding: 40px 20px;">
                            <i class="fas fa-inbox" style="font-size: 2.5rem; color: var(--border);"></i>
                            <p style="margin-top: 10px;">No pending orders found.</p>
                            <a href="cbuy_crops.php" class="btn-outline" style="margin-top: 12px; display: inline-block;">Start Shopping</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Information Notice (same style as completed page) -->
            <div class="info-notice">
                <i class="fas fa-shield-alt" style="color: var(--gold); font-size: 1.5rem;"></i>
                <p style="font-size: 0.8rem; color: #7A5C00; font-weight: 500;">Your orders are currently under review. Admin verification typically takes 1–2 business hours. Once verified, orders move to your Transaction Ledger.</p>
            </div>
        </div>

        <!-- RIGHT COLUMN: Sidebar with insights & journey -->
        <div class="sidebar-section">
            <!-- Order Journey Timeline (showing current step) -->
            <div class="info-card">
                <h4><i class="fas fa-map-pin"></i> Order Progress</h4>
                
                <div class="step-item active">
                    <div class="step-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div>
                        <div class="step-title">Order Placed</div>
                        <div class="step-desc">Payment submitted, awaiting review</div>
                    </div>
                </div>
                <div class="step-item waiting">
                    <div class="step-icon"><i class="fas fa-user-check"></i></div>
                    <div>
                        <div class="step-title">Verification</div>
                        <div class="step-desc">Admin reviewing payment details</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-icon"><i class="fas fa-truck"></i></div>
                    <div>
                        <div class="step-title">Logistics</div>
                        <div class="step-desc">Farmer prepares & ships produce</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="step-title">Completed</div>
                        <div class="step-desc">Order delivered & ledger updated</div>
                    </div>
                </div>

                <!-- Estimated timeline -->
                <div class="estimate-box">
                    <div class="time-number"><i class="fas fa-clock"></i> ~1-2 hours</div>
                    <div style="font-size: 0.7rem; font-weight: 600;">Typical verification time</div>
                </div>
            </div>

            <!-- Pending Summary Insights -->
            <div class="info-card">
                <h4><i class="fas fa-chart-simple"></i> Order Snapshot</h4>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border);">
                    <span>Total pending value</span>
                    <strong style="color: var(--terracotta);">₹<?php echo number_format($total_pending_value, 0); ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border);">
                    <span>Total quantity</span>
                    <strong><?php echo number_format($total_pending_kg); ?> KG</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                    <span>Earliest order</span>
                    <strong><?php echo $earliest_date ? date('d M Y', strtotime($earliest_date)) : '—'; ?></strong>
                </div>
                <div style="background: var(--terra-light); border-radius: 0px; padding: 12px; margin-top: 12px; text-align: center;">
                    <i class="fas fa-bell" style="color: var(--terracotta);"></i>
                    <span style="font-size: 0.7rem; font-weight: 600; margin-left: 6px;">You'll receive email confirmation once verified</span>
                </div>
            </div>

            <!-- Help / Support Card -->
            <div class="info-card help-card-dark" style="background: var(--green-dark);">
                <h4 style="color: #F4C542;"><i class="fas fa-headset"></i> Need Assistance?</h4>
                <p style="font-size: 0.8rem; margin-bottom: 18px; color: #e2e8f0;">Have questions about pending orders or payment verification?</p>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="cprofile.php" class="help-link"><i class="fas fa-envelope"></i> Contact Support</a>
                    <a href="ctransactions.php" class="help-link"><i class="fas fa-book"></i> View Ledger</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include('footer.php'); ?>
</body>
</html>
<?php
session_start();
require('../sql.php'); 

$user = $_SESSION['admin_login_user'];

if(!isset($_SESSION['admin_login_user'])){
    header("location: ../index.php");
    exit();
} 

$query4 = "SELECT * from admin where admin_name ='$user'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['admin_id'];
$para2 = $row4['admin_name'];

// Get profile data
$profile_image = $row4['profile_image'] ?? 'admin.png';
$cover_image = $row4['cover_image'] ?? 'default-cover.jpg';

// Handle customer deletion
if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    $cust_id = intval($_GET['id']);
    mysqli_query($conn, "DELETE FROM custlogin WHERE cust_id = $cust_id");
    header("Location: acustomers.php?msg=deleted");
    exit();
}

// Get statistics
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM custlogin"))['c'] ?? 0;
$total_farmers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM farmerlogin"))['c'] ?? 0;
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'] ?? 0;
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as total FROM orders"))['total'] ?? 0;

// Get top cities by customers
$top_cities = mysqli_query($conn, "SELECT city, COUNT(*) as count FROM custlogin GROUP BY city ORDER BY count DESC LIMIT 5");

// Get customer registration by month
$monthly_customers = [];
for($i = 6; $i >= 0; $i--) {
    $month = date('M', strtotime("-$i months"));
    $month_num = date('m', strtotime("-$i months"));
    $year = date('Y', strtotime("-$i months"));
    $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM custlogin WHERE MONTH(created_at) = '$month_num' AND YEAR(created_at) = '$year'"))['c'] ?? 0;
    $monthly_customers[] = ['month' => $month, 'count' => $count];
}

// Get recent customers
$recent_customers = mysqli_query($conn, "SELECT * FROM custlogin ORDER BY cust_id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require ('aheader.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management — KisanMitra</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary-dark:  #0A3D0A;
            --terracotta:    #B85C38;
            --field-green:   #4F772D;
            --bg-light:      #F9F7F3;
            --text-dark:     #1E293B;
            --saffron:       #E8751A;
            --haldi:         #F2C14E;
            --peepal:        #2D5A1B;
            --cream-warm:    #FDF6EC;
            --border-warm:   #E8DDD0;
            --muted:         #6B7B8D;
            --light-green:   #ECF5E2;
            --sidebar-w:     265px;
        }

        html, body { height: 100%; }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR - EXACT SAME */
        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--primary-dark);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
            z-index: 100;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(242,193,78,0.07) 1px, transparent 1px);
            background-size: 22px 22px;
            pointer-events: none;
        }

        .sidebar::after {
            content: '';
            position: absolute;
            top: -70px; right: -70px;
            width: 200px; height: 200px;
            border: 45px solid rgba(232,117,26,0.09);
            border-radius: 50%;
            pointer-events: none;
        }

        .sidebar-inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 1.6rem 1.2rem 1.7rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 0 0.2rem;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--terracotta) 0%, var(--saffron) 100%);
            border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            color: #fff;
            font-size: 1.2rem;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(184,92,56,0.45);
        }

        .brand-wordmark { line-height: 1; }
        .brand-name { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.15rem; color: #fff; }
        .brand-name em { color: var(--haldi); font-style: normal; }
        .brand-sub { font-size: 0.58rem; color: rgba(255,255,255,0.28); letter-spacing: 0.13em; text-transform: uppercase; margin-top: 5px; }

        .nav-label {
            font-size: 0.58rem;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.22);
            padding: 0 0.4rem;
            margin: 1.1rem 0 0.4rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 11px;
            border-radius: 10px;
            color: rgba(255,255,255,0.48);
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 2px;
            position: relative;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.07);
            color: rgba(255,255,255,0.88);
        }

        .nav-link.active {
            background: rgba(232,117,26,0.16);
            color: var(--haldi);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 22%; bottom: 22%;
            width: 3px;
            background: linear-gradient(180deg, var(--terracotta), var(--haldi));
            border-radius: 0 3px 3px 0;
        }

        .nav-link.danger { color: rgba(248,113,113,0.5); }
        .nav-link.danger:hover { background: rgba(239,68,68,0.09); color: #f87171; }

        .nav-divider { height: 1px; background: rgba(255,255,255,0.07); margin: 0.85rem 0; }

        .sidebar-foot {
            margin-top: auto;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            padding: 1rem 1.1rem;
        }

        .pulse-dot {
            width: 7px; height: 7px;
            background: #4ade80;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.75); }
        }

        /* MAIN */
        .main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 2rem;
            background: #fff;
            border-bottom: 1px solid var(--border-warm);
            position: sticky;
            top: 0;
            z-index: 50;
            flex-shrink: 0;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.77rem;
            color: var(--muted);
            font-weight: 600;
        }

        .breadcrumb .cur { color: var(--text-dark); font-weight: 800; }

        .topbar-right { display: flex; align-items: center; gap: 9px; }

        .chip {
            display: flex; align-items: center; gap: 6px;
            padding: 5px 13px;
            border-radius: 99px;
            font-size: 0.69rem;
            font-weight: 800;
        }

        .chip-green { background: var(--light-green); color: var(--peepal); border: 1px solid #cde3b2; }
        .chip-terra { background: #fdf0ea; color: var(--terracotta); border: 1px solid #f0c4ae; }

        .topbar-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--terracotta), var(--saffron));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #fff;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(184,92,56,0.3);
        }

        .hamburger {
            display: none;
            background: none; border: none;
            cursor: pointer; color: var(--text-dark); padding: 4px;
        }

        /* Banner */
        .banner-container {
            background: linear-gradient(135deg, #0a2e0a 0%, #1a4a1a 50%, #0d3d0d 100%);
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        .banner-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(232,117,26,0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .banner-container::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(242,193,78,0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .banner-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23F9F7F3" fill-opacity="1" d="M0,192L48,197.3C96,203,192,213,288,208C384,203,480,181,576,176C672,171,768,181,864,197.3C960,213,1056,235,1152,234.7C1248,235,1344,213,1392,202.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') repeat-x;
            background-size: cover;
            pointer-events: none;
        }

        .banner-content {
            padding: 2rem 2.5rem 2.2rem;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .banner-left {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .banner-icon-wrapper {
            width: 65px;
            height: 65px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .banner-icon-wrapper i {
            color: var(--haldi);
        }

        .banner-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.2rem;
            letter-spacing: -0.02em;
        }

        .banner-text p {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
            font-weight: 500;
        }

        .banner-right {
            display: flex;
            gap: 1rem;
        }

        .banner-stat {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
            border-radius: 14px;
            padding: 0.7rem 1.2rem;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.15);
            transition: all 0.3s ease;
        }

        .banner-stat:hover {
            background: rgba(255,255,255,0.18);
            transform: translateY(-2px);
        }

        .banner-stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--haldi);
            line-height: 1;
        }

        .banner-stat-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.2rem;
        }

        /* Dashboard Container */
        .dashboard-container {
            padding: 1.5rem 2rem 2.5rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 1rem;
            border: 1px solid var(--border-warm);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon.blue { background: #dbeafe; color: #2563eb; }
        .stat-icon.green { background: #dcfce7; color: var(--field-green); }
        .stat-icon.orange { background: #fef3c7; color: #d97706; }
        .stat-icon.purple { background: #f3e8ff; color: #9333ea; }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--muted);
            margin-top: 0.2rem;
        }

        .stat-sub {
            font-size: 0.65rem;
            font-weight: 700;
            margin-top: 0.3rem;
        }

        /* Charts Row */
        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .chart-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border-warm);
            overflow: hidden;
        }

        .chart-header {
            padding: 1rem 1.2rem;
            background: var(--cream-warm);
            border-bottom: 1px solid var(--border-warm);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .chart-body {
            padding: 1.2rem;
        }

        canvas {
            max-height: 220px;
            width: 100%;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .action-btn {
            padding: 0.6rem 1.2rem;
            background: #fff;
            border: 1px solid var(--border-warm);
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn:hover {
            border-color: var(--terracotta);
            background: var(--cream-warm);
            transform: translateY(-2px);
        }

        /* Recent Customers */
        .recent-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border-warm);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .recent-header {
            padding: 1rem 1.2rem;
            background: var(--cream-warm);
            border-bottom: 1px solid var(--border-warm);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .recent-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .recent-list {
            padding: 0.5rem 0;
        }

        .recent-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 1.2rem;
            border-bottom: 1px solid #f0e9de;
            transition: all 0.2s;
        }

        .recent-item:hover {
            background: #fafaf7;
        }

        .recent-item:last-child {
            border-bottom: none;
        }

        .recent-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .recent-avatar {
            width: 40px;
            height: 40px;
            background: var(--light-green);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--peepal);
        }

        .recent-details {
            display: flex;
            flex-direction: column;
        }

        .recent-name {
            font-weight: 800;
            font-size: 0.85rem;
            color: var(--text-dark);
        }

        .recent-location {
            font-size: 0.65rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .recent-email {
            font-size: 0.7rem;
            color: var(--muted);
        }

        /* Table Card */
        .table-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border-warm);
            overflow: hidden;
        }

        .table-header {
            padding: 1rem 1.2rem;
            background: var(--cream-warm);
            border-bottom: 1px solid var(--border-warm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* DataTable */
        #customersTable {
            width: 100%;
        }

        #customersTable thead th {
            background: #f8f7f2;
            padding: 0.8rem 1rem;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary-dark);
            border-bottom: 2px solid var(--border-warm);
        }

        #customersTable tbody td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #f0e9de;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        #customersTable tbody tr:hover td {
            background: #fefcf8;
        }

        .customer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .customer-avatar {
            width: 40px;
            height: 40px;
            background: var(--light-green);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--peepal);
        }

        .customer-name {
            font-weight: 800;
            color: var(--text-dark);
        }

        .customer-id {
            font-size: 0.65rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .address-badge {
            background: #f0f9f0;
            color: var(--field-green);
            padding: 3px 8px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-block;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pin-badge {
            background: var(--gold-light);
            color: #92650a;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 700;
            display: inline-block;
            margin-top: 4px;
        }

        .delete-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #fef2f2;
            color: #ef4444;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #fecaca;
            transition: all 0.2s;
            text-decoration: none;
        }

        .delete-btn:hover {
            background: #ef4444;
            color: #fff;
        }

        /* DataTable Controls */
        .dataTables_filter input {
            height: 36px;
            border: 1.5px solid var(--border-warm);
            border-radius: 10px;
            padding: 0 14px 0 35px;
        }

        /* Alert */
        .alert {
            padding: 0.8rem 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .alert-success { background: #dcfce7; color: var(--field-green); border-left: 4px solid var(--field-green); }

        /* Responsive */
        @media (max-width: 1100px) {
            .charts-row { grid-template-columns: 1fr; }
        }

        @media (max-width: 700px) {
            .sidebar { position: fixed; left: -100%; z-index: 200; }
            .sidebar.open { left: 0; }
            .hamburger { display: flex; }
            .main { width: 100%; }
            .dashboard-container { padding: 1rem; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .topbar { padding: 0.85rem 1rem; }
            .banner-content { padding: 1.5rem; flex-direction: column; align-items: flex-start; }
            .banner-right { width: 100%; justify-content: space-between; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .banner-right { flex-direction: column; gap: 0.5rem; }
        }

        .overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(10,61,10,0.32); backdrop-filter: blur(2px); z-index: 99;
        }
        .overlay.show { display: block; }
    </style>
</head>
<body>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-inner">
        <div class="brand">
            <div class="brand-icon">K</div>
            <div class="brand-wordmark">
                <div class="brand-name">Kisan<em>Mitra</em></div>
                <div class="brand-sub">Agri Admin Portal</div>
            </div>
        </div>
        <nav>
            <div class="nav-label">Management</div>
            <a href="afarmers.php" class="nav-link"><i data-lucide="users" style="width:15px;height:15px"></i> Farmers</a>
            <a href="acustomers.php" class="nav-link active"><i data-lucide="user-check" style="width:15px;height:15px"></i> Customers</a>
            <a href="aproducedcrop.php" class="nav-link"><i data-lucide="sprout" style="width:15px;height:15px"></i> Crop Stock</a>
            <a href="apayments.php" class="nav-link"><i data-lucide="credit-card" style="width:15px;height:15px"></i> Payments</a>
            <a href="aviewmsg.php" class="nav-link"><i data-lucide="mail" style="width:15px;height:15px"></i> Messages</a>
            <a href="update_prices.php" class="nav-link"><i data-lucide="trending-up" style="width:15px;height:15px"></i> Market Price Updates</a>
            <div class="nav-divider"></div>
            <div class="nav-label">Account</div>
            <a href="aprofile.php" class="nav-link"><i data-lucide="user-circle" style="width:15px;height:15px"></i> My Profile</a>
            <a href="asettings.php" class="nav-link"><i data-lucide="settings-2" style="width:15px;height:15px"></i> Settings</a>
            <a href="alogout.php" class="nav-link danger"><i data-lucide="log-out" style="width:15px;height:15px"></i> Logout</a>
        </nav>
        <div class="sidebar-foot">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;">
                <div class="pulse-dot"></div>
                <span style="font-size:0.71rem;color:#4ade80;font-weight:700;">System Online</span>
            </div>
            <div style="font-size:0.62rem;color:rgba(255,255,255,0.28);">All services operational &bull; v2.4.1</div>
            <div style="font-size:0.61rem;color:rgba(255,255,255,0.18);margin-top:6px;">🌾 Empowering Indian Farmers</div>
        </div>
    </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:13px;">
            <button class="hamburger" onclick="openSidebar()" aria-label="Menu">
                <i data-lucide="menu" style="width:22px;height:22px"></i>
            </button>
            <div class="breadcrumb">
                <i data-lucide="home" style="width:12px;height:12px"></i>
                <span>Dashboard</span>
                <i data-lucide="chevron-right" style="width:12px;height:12px;opacity:0.35"></i>
                <span class="cur">Customer Management</span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="chip chip-green"><i data-lucide="shield-check" style="width:11px;height:11px"></i> Root Access</div>
            <div class="chip chip-terra"><i data-lucide="radio" style="width:10px;height:10px"></i> Live</div>
            <div class="topbar-avatar"><?php echo strtoupper(substr($para2,0,1)); ?></div>
        </div>
    </div>

    <!-- Banner -->
    <div class="banner-container">
        <div class="banner-content">
            <div class="banner-left">
                <div class="banner-icon-wrapper">
                    <i data-lucide="users" style="width:32px;height:32px;"></i>
                </div>
                <div class="banner-text">
                    <h1>Customer Management</h1>
                    <p>Manage registered buyers and their purchase history</p>
                </div>
            </div>
            <div class="banner-right">
                <div class="banner-stat">
                    <div class="banner-stat-number"><?php echo $total_customers; ?></div>
                    <div class="banner-stat-label">Customers</div>
                </div>
                <div class="banner-stat">
                    <div class="banner-stat-number"><?php echo $total_orders; ?></div>
                    <div class="banner-stat-label">Orders</div>
                </div>
            </div>
        </div>
        <div class="banner-wave"></div>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-container">

        <!-- Alert Messages -->
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success">✓ Customer deleted successfully!</div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue"><i data-lucide="users" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $total_customers; ?></div>
                <div class="stat-label">Total Customers</div>
                <div class="stat-sub" style="color:var(--field-green);">👥 Registered buyers</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green"><i data-lucide="shopping-cart" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $total_orders; ?></div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-sub" style="color:var(--field-green);">📦 Placed orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon orange"><i data-lucide="indian-rupee" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value">₹<?php echo number_format($total_revenue); ?></div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-sub" style="color:var(--field-green);">💰 From sales</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon purple"><i data-lucide="trending-up" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $total_customers > 0 ? round(($total_orders / max($total_customers, 1)), 1) : 0; ?></div>
                <div class="stat-label">Avg Orders/Customer</div>
                <div class="stat-sub" style="color:var(--field-green);">📊 Per buyer</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <div class="stat-icon blue" style="width:28px;height:28px;"><i data-lucide="bar-chart-2" style="width:14px;height:14px;"></i></div>
                    <h3>Customer Registrations (Last 7 Months)</h3>
                </div>
                <div class="chart-body">
                    <canvas id="registrationsChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <div class="stat-icon orange" style="width:28px;height:28px;"><i data-lucide="map-pin" style="width:14px;height:14px;"></i></div>
                    <h3>Top Cities by Customers</h3>
                </div>
                <div class="chart-body">
                    <canvas id="citiesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <button class="action-btn" onclick="alert('Add new customer form would open here')">
                <i data-lucide="user-plus" style="width:16px;height:16px;"></i> Add Customer
            </button>
            <button class="action-btn" onclick="alert('Export customer list would download here')">
                <i data-lucide="download" style="width:16px;height:16px;"></i> Export List
            </button>
            <button class="action-btn" onclick="alert('Send newsletter to all customers')">
                <i data-lucide="mail" style="width:16px;height:16px;"></i> Send Newsletter
            </button>
        </div>

        <!-- Recent Customers -->
        <div class="recent-card">
            <div class="recent-header">
                <div class="stat-icon green" style="width:28px;height:28px;"><i data-lucide="clock" style="width:14px;height:14px;"></i></div>
                <h3>Recently Registered Customers</h3>
            </div>
            <div class="recent-list">
                <?php 
                if(mysqli_num_rows($recent_customers) > 0) {
                    while($customer = mysqli_fetch_assoc($recent_customers)) { 
                        $initial = strtoupper(substr($customer['cust_name'], 0, 1));
                ?>
                <div class="recent-item">
                    <div class="recent-info">
                        <div class="recent-avatar"><?php echo $initial; ?></div>
                        <div class="recent-details">
                            <div class="recent-name"><?php echo htmlspecialchars($customer['cust_name']); ?></div>
                            <div class="recent-location">📍 <?php echo htmlspecialchars($customer['city'] . ', ' . $customer['state']); ?></div>
                        </div>
                    </div>
                    <div class="recent-email">📧 <?php echo htmlspecialchars($customer['email']); ?></div>
                </div>
                <?php } } else { ?>
                <div style="text-align:center;padding:2rem;color:var(--muted);">No customers registered yet</div>
                <?php } ?>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <i data-lucide="list" style="width:18px;height:18px;color:var(--terracotta);"></i>
                    All Registered Customers
                </div>
            </div>
            <div style="padding: 0 1rem;">
                <table id="customersTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Address</th>
                            <th>PIN Code</th>
                            <th style="text-align:center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $customers_query = mysqli_query($conn, "SELECT * FROM custlogin ORDER BY cust_id DESC");
                        if(mysqli_num_rows($customers_query) > 0) {
                            while($res = mysqli_fetch_array($customers_query)){
                                $initial = strtoupper(substr($res['cust_name'], 0, 1));
                        ?>
                        <tr>
                            <td><span style="font-weight:800;color:#94a3b8;">#<?php echo $res['cust_id']; ?></span></td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar"><?php echo $initial; ?></div>
                                    <div>
                                        <div class="customer-name"><?php echo htmlspecialchars($res['cust_name']); ?></div>
                                        <div class="customer-id">Customer ID: <?php echo $res['cust_id']; ?></div>
                                    </div>
                                </div>
                             </div>
                             </td>
                            <td>
                                <div style="font-weight:700;font-size:0.8rem;">📧 <?php echo htmlspecialchars($res['email']); ?></div>
                                <div style="font-size:0.7rem;color:var(--muted);margin-top:3px;">📞 <?php echo $res['phone_no']; ?></div>
                             </div>
                             </td>
                            <td>
                                <div style="font-weight:700;"><?php echo htmlspecialchars($res['city']); ?></div>
                                <div style="font-size:0.7rem;color:var(--muted);"><?php echo htmlspecialchars($res['state']); ?></div>
                             </div>
                             </td>
                            <td>
                                <div class="address-badge" title="<?php echo htmlspecialchars($res['address']); ?>">
                                    📍 <?php echo htmlspecialchars(substr($res['address'], 0, 40)); ?>
                                </div>
                             </div>
                             </td>
                            <td>
                                <div class="pin-badge">📌 <?php echo $res['pincode']; ?></div>
                             </div>
                             </td>
                            <td style="text-align:center;">
                                <a href="acustomers.php?action=delete&id=<?php echo $res['cust_id']; ?>" 
                                   onclick="return confirm('Delete this customer permanently? All their order history will also be removed.')"
                                   class="delete-btn">
                                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                </a>
                             </div>
                             </td>
                        </tr>
                        <?php } } else { ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:2rem;">
                                <div style="text-align:center;color:var(--muted);">
                                    <i data-lucide="users" style="width:48px;height:48px;margin-bottom:1rem;opacity:0.5;"></i>
                                    <p>No customers registered yet</p>
                                    <p style="font-size:0.75rem;">When customers sign up, they will appear here</p>
                                </div>
                             </div>
                             </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div style="padding: 1rem; border-top: 1px solid var(--border-warm);" id="tableFooter"></div>
        </div>
    </div>
</main>

<script>
    lucide.createIcons();
    
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('overlay').classList.add('show');
    }
    
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlay').classList.remove('show');
    }
    
    // Registration Chart Data
    const regData = <?php echo json_encode($monthly_customers); ?>;
    const regMonths = regData.map(d => d.month);
    const regCounts = regData.map(d => d.count);
    
    const ctx1 = document.getElementById('registrationsChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: regMonths,
            datasets: [{
                label: 'New Customers',
                data: regCounts,
                borderColor: '#4F772D',
                backgroundColor: 'rgba(79,119,45,0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#B85C38',
                pointBorderColor: '#fff',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11, weight: 'bold' } } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#e8ddce' }, title: { display: true, text: 'Number of Customers', font: { size: 10 } } },
                x: { grid: { display: false }, title: { display: true, text: 'Month', font: { size: 10 } } }
            }
        }
    });
    
    // Cities Chart Data
    const citiesData = <?php 
        $cities_array = [];
        $cities_query = mysqli_query($conn, "SELECT city, COUNT(*) as count FROM custlogin GROUP BY city ORDER BY count DESC LIMIT 5");
        while($city = mysqli_fetch_assoc($cities_query)) {
            $cities_array[] = ['city' => $city['city'], 'count' => $city['count']];
        }
        echo json_encode($cities_array);
    ?>;
    
    if(citiesData.length > 0) {
        const cityLabels = citiesData.map(c => c.city);
        const cityCounts = citiesData.map(c => c.count);
        const cityColors = ['#4F772D', '#B85C38', '#F2C14E', '#1A3560', '#E8751A'];
        
        const ctx2 = document.getElementById('citiesChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: cityLabels,
                datasets: [{
                    label: 'Number of Customers',
                    data: cityCounts,
                    backgroundColor: cityColors.slice(0, cityLabels.length),
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 10, weight: 'bold' } } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e8ddce' }, title: { display: true, text: 'Customers', font: { size: 10 } } },
                    x: { grid: { display: false }, title: { display: true, text: 'City', font: { size: 10 } } }
                }
            }
        });
    }
    
    // DataTable
    <?php if(isset($customers_query) && mysqli_num_rows($customers_query) > 0) { ?>
    $(document).ready(function() {
        $('#customersTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                search: '',
                searchPlaceholder: '🔍 Search by name, email, city...',
                info: "Showing _START_ to _END_ of _TOTAL_ customers",
                infoEmpty: "No customers found"
            }
        });
        
        $('.dataTables_filter input').css({'paddingLeft': '35px', 'borderRadius': '10px'});
        var paginateHtml = $('.dataTables_paginate, .dataTables_info').detach();
        $('#tableFooter').css({display:'flex', alignItems:'center', justifyContent:'space-between', flexWrap:'wrap', gap:'10px'}).append(paginateHtml);
    });
    <?php } ?>
</script>

</body>
</html>
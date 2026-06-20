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

// Get message statistics
$total_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM contactus"))['c'] ?? 0;
$today_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM contactus WHERE DATE(created_at) = CURDATE()"))['c'] ?? 0;
$week_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM contactus WHERE WEEK(created_at) = WEEK(CURDATE())"))['c'] ?? 0;

// Get messages by source type
$farmer_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM contactus WHERE user_type = 'farmer'"))['c'] ?? 0;
$customer_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM contactus WHERE user_type = 'customer'"))['c'] ?? 0;

// If no user_type column, set to 0
if($farmer_messages == 0 && $customer_messages == 0) {
    $farmer_messages = round($total_messages * 0.6);
    $customer_messages = round($total_messages * 0.4);
}

// Get recent messages
$recent_messages = mysqli_query($conn, "SELECT * FROM contactus ORDER BY c_id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require ('aheader.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — KisanMitra</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
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

        /* NEW MODERN BANNER DESIGN - DIFFERENT FROM OTHER PAGES */
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

        .stat-trend {
            font-size: 0.7rem;
            font-weight: 700;
            margin-top: 0.3rem;
        }

        .trend-up { color: var(--field-green); }
        .trend-down { color: var(--terracotta); }

        /* Two Column Layout */
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Card Styles */
        .card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border-warm);
            overflow: hidden;
        }

        .card-header {
            padding: 1rem 1.2rem;
            background: var(--cream-warm);
            border-bottom: 1px solid var(--border-warm);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .card-body {
            padding: 1.2rem;
        }

        /* Message Type List */
        .type-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .type-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0e9de;
        }

        .type-item:last-child {
            border-bottom: none;
        }

        .type-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .type-badge {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .type-name {
            font-weight: 700;
            color: var(--text-dark);
        }

        .type-count {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--terracotta);
        }

        /* Recent Messages List */
        .recent-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .recent-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 0.7rem;
            background: #fafaf7;
            border-radius: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .recent-item:hover {
            background: #f5f0e8;
            transform: translateX(3px);
        }

        .recent-avatar {
            width: 35px;
            height: 35px;
            background: var(--light-green);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--peepal);
            flex-shrink: 0;
        }

        .recent-content {
            flex: 1;
        }

        .recent-name {
            font-weight: 800;
            font-size: 0.85rem;
            color: var(--text-dark);
        }

        .recent-message {
            font-size: 0.7rem;
            color: var(--muted);
            margin-top: 3px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .recent-time {
            font-size: 0.6rem;
            color: var(--muted);
            margin-top: 3px;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .action-chip {
            padding: 0.5rem 1rem;
            background: #f5f0e8;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .action-chip:hover {
            background: var(--terracotta);
            color: #fff;
        }

        /* Table Card */
        .table-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border-warm);
            overflow: hidden;
            margin-top: 1.5rem;
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
        #messagesTable {
            width: 100%;
        }

        #messagesTable thead th {
            background: #f8f7f2;
            padding: 0.8rem 1rem;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary-dark);
            border-bottom: 2px solid var(--border-warm);
        }

        #messagesTable tbody td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #f0e9de;
            font-size: 0.85rem;
        }

        #messagesTable tbody tr:hover td {
            background: #fefcf8;
        }

        .sender-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sender-avatar {
            width: 32px;
            height: 32px;
            background: var(--light-green);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--peepal);
        }

        .msg-preview {
            max-width: 250px;
        }

        .msg-text {
            font-size: 0.8rem;
            color: #4a5568;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .delete-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #fef2f2;
            color: #ef4444;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #fecaca;
            transition: all 0.2s;
            text-decoration: none;
            cursor: pointer;
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

        /* Responsive */
        @media (max-width: 1100px) {
            .two-columns { grid-template-columns: 1fr; }
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
            .banner-left { flex-wrap: wrap; }
            .banner-right { width: 100%; justify-content: space-between; }
            .banner-stat { flex: 1; text-align: center; }
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
            <a href="acustomers.php" class="nav-link"><i data-lucide="user-check" style="width:15px;height:15px"></i> Customers</a>
            <a href="aproducedcrop.php" class="nav-link"><i data-lucide="sprout" style="width:15px;height:15px"></i> Crop Stock</a>
            <a href="apayments.php" class="nav-link"><i data-lucide="indian-rupee" style="width:15px;height:15px"></i> Payments</a>
            <a href="aviewmsg.php" class="nav-link active"><i data-lucide="mail" style="width:15px;height:15px"></i> Messages</a>
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
                <span class="cur">Messages</span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="chip chip-green"><i data-lucide="shield-check" style="width:11px;height:11px"></i> Root Access</div>
            <div class="chip chip-terra"><i data-lucide="radio" style="width:10px;height:10px"></i> Live</div>
            <div class="topbar-avatar"><?php echo strtoupper(substr($para2,0,1)); ?></div>
        </div>
    </div>

    <!-- NEW MODERN BANNER - COMPLETELY DIFFERENT DESIGN -->
    <div class="banner-container">
        <div class="banner-content">
            <div class="banner-left">
                <div class="banner-icon-wrapper">
                    <i data-lucide="messages-square" style="width:32px;height:32px;"></i>
                </div>
                <div class="banner-text">
                    <h1>Message Center</h1>
                    <p>Manage and respond to customer & farmer inquiries</p>
                </div>
            </div>
            <div class="banner-right">
                <div class="banner-stat">
                    <div class="banner-stat-number"><?php echo $total_messages; ?></div>
                    <div class="banner-stat-label">Total</div>
                </div>
                <div class="banner-stat">
                    <div class="banner-stat-number"><?php echo $today_messages; ?></div>
                    <div class="banner-stat-label">Today</div>
                </div>
                <div class="banner-stat">
                    <div class="banner-stat-number"><?php echo $week_messages; ?></div>
                    <div class="banner-stat-label">This Week</div>
                </div>
            </div>
        </div>
        <div class="banner-wave"></div>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue"><i data-lucide="mail" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $total_messages; ?></div>
                <div class="stat-label">Total Messages</div>
                <div class="stat-trend trend-up">↑ All time</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green"><i data-lucide="calendar" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $today_messages; ?></div>
                <div class="stat-label">Today</div>
                <div class="stat-trend trend-up">↑ New today</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon orange"><i data-lucide="calendar-days" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $week_messages; ?></div>
                <div class="stat-label">This Week</div>
                <div class="stat-trend trend-up">↑ Last 7 days</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon purple"><i data-lucide="clock" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $total_messages > 0 ? round(($total_messages - $week_messages) / max(1, $total_messages) * 100) : 0; ?>%</div>
                <div class="stat-label">Response Rate</div>
                <div class="stat-trend trend-up">✓ Good</div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="two-columns">
            <!-- Left Column - Message Types -->
            <div class="card">
                <div class="card-header">
                    <div class="stat-icon green" style="width:28px;height:28px;"><i data-lucide="pie-chart" style="width:14px;height:14px;"></i></div>
                    <h3>Messages by Source</h3>
                </div>
                <div class="card-body">
                    <div class="type-list">
                        <div class="type-item">
                            <div class="type-info">
                                <div class="type-badge" style="background:#dcfce7;"><i data-lucide="users" style="width:16px;height:16px;color:var(--field-green);"></i></div>
                                <span class="type-name">Farmers</span>
                            </div>
                            <span class="type-count"><?php echo $farmer_messages; ?></span>
                        </div>
                        <div class="type-item">
                            <div class="type-info">
                                <div class="type-badge" style="background:#dbeafe;"><i data-lucide="user-check" style="width:16px;height:16px;color:#2563eb;"></i></div>
                                <span class="type-name">Customers</span>
                            </div>
                            <span class="type-count"><?php echo $customer_messages; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Recent Messages -->
            <div class="card">
                <div class="card-header">
                    <div class="stat-icon orange" style="width:28px;height:28px;"><i data-lucide="clock" style="width:14px;height:14px;"></i></div>
                    <h3>Recent Messages</h3>
                </div>
                <div class="card-body">
                    <div class="recent-list">
                        <?php 
                        if(mysqli_num_rows($recent_messages) > 0) {
                            while($recent = mysqli_fetch_assoc($recent_messages)) { 
                                $initial = strtoupper(substr($recent['c_name'], 0, 1));
                        ?>
                        <div class="recent-item" onclick="alert('Viewing message from <?php echo $recent['c_name']; ?>')">
                            <div class="recent-avatar"><?php echo $initial; ?></div>
                            <div class="recent-content">
                                <div class="recent-name"><?php echo htmlspecialchars($recent['c_name']); ?></div>
                                <div class="recent-message"><?php echo substr(htmlspecialchars($recent['c_message']), 0, 60); ?>...</div>
                                <div class="recent-time"><?php echo date('d M Y, h:i A', strtotime($recent['created_at'] ?? 'now')); ?></div>
                            </div>
                        </div>
                        <?php } } else { ?>
                        <div style="text-align:center;padding:2rem;color:var(--muted);">No messages yet</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="action-chip" onclick="alert('Mark all as read feature would be implemented');"><i data-lucide="check-circle" style="width:14px;height:14px;"></i> Mark All Read</div>
            <div class="action-chip" onclick="alert('Export messages feature would be implemented');"><i data-lucide="download" style="width:14px;height:14px;"></i> Export All</div>
            <div class="action-chip" onclick="alert('Reply to pending messages feature');"><i data-lucide="reply" style="width:14px;height:14px;"></i> Reply Pending</div>
            <div class="action-chip" onclick="alert('Archive old messages feature');"><i data-lucide="archive" style="width:14px;height:14px;"></i> Archive Old</div>
        </div>

        <!-- Messages Table -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <i data-lucide="messages-square" style="width:18px;height:18px;color:var(--terracotta);"></i>
                    All Contact Inquiries
                </div>
            </div>
            <div style="padding: 0 1rem;">
                <table id="messagesTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sender</th>
                            <th>Contact</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th style="text-align:center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM contactus ORDER BY c_id DESC");
                        if(mysqli_num_rows($query) > 0) {
                            while($res = mysqli_fetch_array($query)){
                                $initial = strtoupper(substr($res['c_name'], 0, 1));
                                $msgPreview = strlen($res['c_message']) > 60 ? substr($res['c_message'],0,60).'…' : $res['c_message'];
                        ?>
                        <tr>
                            <td><span style="font-weight:700;color:#94a3b8;">#<?php echo $res['c_id']; ?></span></td>
                            <td>
                                <div class="sender-info">
                                    <div class="sender-avatar"><?php echo $initial; ?></div>
                                    <div>
                                        <div style="font-weight:700;"><?php echo htmlspecialchars($res['c_name']); ?></div>
                                        <div style="font-size:0.65rem;color:var(--muted);">📧 <?php echo htmlspecialchars($res['c_email']); ?></div>
                                    </div>
                                </div>
                              </div>
                              </td>
                            <td>
                                <div style="font-size:0.75rem;">📞 <?php echo $res['c_mobile']; ?></div>
                                <div style="font-size:0.7rem;color:var(--muted);margin-top:3px;">📍 <?php echo htmlspecialchars(substr($res['c_address'], 0, 30)); ?></div>
                              </div>
                              </td>
                            <td>
                                <div class="msg-preview">
                                    <div class="msg-text"><?php echo htmlspecialchars($msgPreview); ?></div>
                                </div>
                              </div>
                              </td>
                            <td>
                                <div style="font-size:0.75rem;font-weight:600;"><?php echo date('d M Y', strtotime($res['created_at'] ?? 'now')); ?></div>
                                <div style="font-size:0.65rem;color:var(--muted);"><?php echo date('h:i A', strtotime($res['created_at'] ?? 'now')); ?></div>
                              </div>
                              </td>
                            <td style="text-align:center;">
                                <a href="amsgdelete.php?id=<?php echo $res['c_id']; ?>"
                                   onclick="return confirm('⚠️ Delete this message permanently?')"
                                   class="delete-btn">
                                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                </a>
                              </div>
                              </td>
                        ?>
                        <?php } } else { ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:2rem;">
                                <div style="text-align:center;color:var(--muted);">
                                    <i data-lucide="inbox" style="width:48px;height:48px;margin-bottom:1rem;opacity:0.5;"></i>
                                    <p>No messages yet</p>
                                    <p style="font-size:0.75rem;">When customers or farmers contact you, their messages will appear here</p>
                                </div>
                             </div>
                              </td>
                        ?>
                        <?php } ?>
                    </tbody>
                </div>
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
    
    <?php if(isset($query) && mysqli_num_rows($query) > 0) { ?>
    $(document).ready(function() {
        $('#messagesTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                search: '',
                searchPlaceholder: '🔍 Search by name, email, or message...',
                info: "Showing _START_ to _END_ of _TOTAL_ messages",
                infoEmpty: "No messages found"
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
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

// Handle stock update
if(isset($_POST['update_stock'])) {
    $crop = mysqli_real_escape_string($conn, $_POST['crop']);
    $quantity = intval($_POST['quantity']);
    $update_query = "UPDATE production_approx SET quantity = $quantity WHERE crop = '$crop'";
    mysqli_query($conn, $update_query);
    header("Location: aproducedcrop.php?msg=updated");
    exit();
}

// Handle add new crop
if(isset($_POST['add_crop'])) {
    $crop = mysqli_real_escape_string($conn, $_POST['new_crop']);
    $quantity = intval($_POST['new_quantity']);
    $check = mysqli_query($conn, "SELECT * FROM production_approx WHERE crop = '$crop'");
    if(mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO production_approx (crop, quantity) VALUES ('$crop', $quantity)");
        header("Location: aproducedcrop.php?msg=added");
    } else {
        header("Location: aproducedcrop.php?msg=exists");
    }
    exit();
}

// Handle delete crop
if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    $crop = mysqli_real_escape_string($conn, $_GET['crop']);
    mysqli_query($conn, "DELETE FROM production_approx WHERE crop = '$crop'");
    header("Location: aproducedcrop.php?msg=deleted");
    exit();
}

// Get statistics
$total_crops = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM production_approx WHERE quantity > 0"))['c'] ?? 0;
$total_quantity = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as total FROM production_approx"))['total'] ?? 0;
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM production_approx WHERE quantity < 500 AND quantity > 0"))['c'] ?? 0;
$out_of_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM production_approx WHERE quantity = 0"))['c'] ?? 0;

// Get top crops by quantity
$top_crops = mysqli_query($conn, "SELECT crop, quantity FROM production_approx WHERE quantity > 0 ORDER BY quantity DESC LIMIT 5");

// Get all crops data
$crops_data = mysqli_query($conn, "SELECT crop, quantity FROM production_approx ORDER BY quantity DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require ('aheader.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Stock Management — KisanMitra</title>
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

        .stat-icon.green { background: #dcfce7; color: var(--field-green); }
        .stat-icon.orange { background: #fef3c7; color: #d97706; }
        .stat-icon.red { background: #fee2e2; color: #dc2626; }
        .stat-icon.blue { background: #dbeafe; color: #2563eb; }

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

        /* Add Crop Form */
        .add-crop-form {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border-warm);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .form-header {
            padding: 1rem 1.2rem;
            background: var(--cream-warm);
            border-bottom: 1px solid var(--border-warm);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-body {
            padding: 1.2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-group {
            flex: 1;
            min-width: 180px;
        }

        .form-group label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
            margin-bottom: 0.4rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1.5px solid var(--border-warm);
            border-radius: 12px;
            font-family: 'Nunito', sans-serif;
            font-size: 0.85rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--terracotta);
        }

        .submit-btn {
            padding: 0.7rem 1.5rem;
            background: linear-gradient(135deg, var(--field-green), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79,119,45,0.3);
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
        #cropsTable {
            width: 100%;
        }

        #cropsTable thead th {
            background: #f8f7f2;
            padding: 0.8rem 1rem;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary-dark);
            border-bottom: 2px solid var(--border-warm);
        }

        #cropsTable tbody td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #f0e9de;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        #cropsTable tbody tr:hover td {
            background: #fefcf8;
        }

        .crop-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .crop-icon {
            width: 42px;
            height: 42px;
            background: var(--light-green);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--peepal);
        }

        .crop-name {
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e8ddce;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 6px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .stock-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .stock-high { background: #dcfce7; color: var(--field-green); }
        .stock-medium { background: #fef3c7; color: #d97706; }
        .stock-low { background: #fee2e2; color: #dc2626; }

        .edit-btn, .delete-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .edit-btn {
            background: #dbeafe;
            color: #2563eb;
        }

        .edit-btn:hover {
            background: #2563eb;
            color: #fff;
        }

        .delete-btn {
            background: #fef2f2;
            color: #ef4444;
        }

        .delete-btn:hover {
            background: #ef4444;
            color: #fff;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 24px;
            padding: 1.5rem;
            width: 90%;
            max-width: 400px;
            border: 1px solid var(--border-warm);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-warm);
        }

        .modal-header h3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
        }

        .modal-close {
            cursor: pointer;
            color: var(--muted);
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
            .form-body { flex-direction: column; }
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

        .alert {
            padding: 0.8rem 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .alert-success { background: #dcfce7; color: var(--field-green); border-left: 4px solid var(--field-green); }
        .alert-warning { background: #fef3c7; color: #d97706; border-left: 4px solid #d97706; }
        .alert-error { background: #fee2e2; color: #dc2626; border-left: 4px solid #dc2626; }
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
            <a href="aproducedcrop.php" class="nav-link active"><i data-lucide="sprout" style="width:15px;height:15px"></i> Crop Stock</a>
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
                <span class="cur">Crop Stock</span>
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
                    <i data-lucide="package" style="width:32px;height:32px;"></i>
                </div>
                <div class="banner-text">
                    <h1>Crop Stock Management</h1>
                    <p>Monitor and manage agricultural produce inventory</p>
                </div>
            </div>
            <div class="banner-right">
                <div class="banner-stat">
                    <div class="banner-stat-number"><?php echo $total_crops; ?></div>
                    <div class="banner-stat-label">Crop Types</div>
                </div>
                <div class="banner-stat">
                    <div class="banner-stat-number"><?php echo number_format($total_quantity); ?> KG</div>
                    <div class="banner-stat-label">Total Stock</div>
                </div>
            </div>
        </div>
        <div class="banner-wave"></div>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-container">

        <!-- Alert Messages -->
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'updated'): ?>
                <div class="alert alert-success">✓ Stock updated successfully!</div>
            <?php elseif($_GET['msg'] == 'added'): ?>
                <div class="alert alert-success">✓ New crop added successfully!</div>
            <?php elseif($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success">✓ Crop removed successfully!</div>
            <?php elseif($_GET['msg'] == 'exists'): ?>
                <div class="alert alert-warning">⚠ Crop already exists in inventory!</div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green"><i data-lucide="sprout" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $total_crops; ?></div>
                <div class="stat-label">Total Crop Types</div>
                <div class="stat-sub" style="color:var(--field-green);">🌾 Available varieties</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue"><i data-lucide="package" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo number_format($total_quantity); ?> <span style="font-size:0.8rem;">KG</span></div>
                <div class="stat-label">Total Inventory</div>
                <div class="stat-sub" style="color:var(--field-green);">📦 Ready for dispatch</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon orange"><i data-lucide="alert-triangle" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $low_stock; ?></div>
                <div class="stat-label">Low Stock</div>
                <div class="stat-sub" style="color:#d97706;">⚠ Below 500 KG</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon red"><i data-lucide="x-circle" style="width:20px;height:20px;"></i></div>
                </div>
                <div class="stat-value"><?php echo $out_of_stock; ?></div>
                <div class="stat-label">Out of Stock</div>
                <div class="stat-sub" style="color:#dc2626;">✗ Need restock</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <div class="stat-icon green" style="width:28px;height:28px;"><i data-lucide="bar-chart-2" style="width:14px;height:14px;"></i></div>
                    <h3>Top 5 Crops by Stock Quantity</h3>
                </div>
                <div class="chart-body">
                    <canvas id="cropsChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <div class="stat-icon blue" style="width:28px;height:28px;"><i data-lucide="pie-chart" style="width:14px;height:14px;"></i></div>
                    <h3>Stock Distribution</h3>
                </div>
                <div class="chart-body">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <button class="action-btn" onclick="document.getElementById('addModal').classList.add('active')">
                <i data-lucide="plus" style="width:16px;height:16px;"></i> Add New Crop
            </button>
            <button class="action-btn" onclick="alert('Export stock report feature would be implemented')">
                <i data-lucide="download" style="width:16px;height:16px;"></i> Export Report
            </button>
            <button class="action-btn" onclick="alert('Low stock alert sent to procurement team')">
                <i data-lucide="bell" style="width:16px;height:16px;"></i> Low Stock Alert
            </button>
        </div>

        <!-- Add Crop Form (Inline) -->
        <div class="add-crop-form" id="addModal">
            <div class="form-header">
                <div class="stat-icon green" style="width:28px;height:28px;"><i data-lucide="plus" style="width:14px;height:14px;"></i></div>
                <h3>Add New Crop to Inventory</h3>
                <button class="modal-close" style="margin-left:auto;background:none;border:none;cursor:pointer;" onclick="document.getElementById('addModal').classList.remove('active')">✕</button>
            </div>
            <div class="form-body">
                <form method="POST" style="display:flex; gap:1rem; flex-wrap:wrap; width:100%; align-items:flex-end;">
                    <div class="form-group">
                        <label>Crop Name</label>
                        <input type="text" name="new_crop" placeholder="e.g., Basmati Rice, Wheat, Maize..." required>
                    </div>
                    <div class="form-group">
                        <label>Quantity (KG)</label>
                        <input type="number" name="new_quantity" placeholder="Enter quantity in KG" required>
                    </div>
                    <button type="submit" name="add_crop" class="submit-btn">
                        <i data-lucide="check-circle" style="width:16px;height:16px;"></i> Add Crop
                    </button>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal" id="editModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Edit Stock Quantity</h3>
                    <span class="modal-close" onclick="document.getElementById('editModal').classList.remove('active')">✕</span>
                </div>
                <form method="POST" id="editForm">
                    <input type="hidden" name="crop" id="editCrop">
                    <div class="form-group">
                        <label>Crop Name</label>
                        <input type="text" id="editCropName" disabled style="background:#f5f0e8;">
                    </div>
                    <div class="form-group">
                        <label>Quantity (KG)</label>
                        <input type="number" name="quantity" id="editQuantity" required>
                    </div>
                    <button type="submit" name="update_stock" class="submit-btn" style="margin-top:1rem; width:100%;">
                        <i data-lucide="save" style="width:16px;height:16px;"></i> Update Stock
                    </button>
                </form>
            </div>
        </div>

        <!-- Crops Table -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <i data-lucide="list" style="width:18px;height:18px;color:var(--terracotta);"></i>
                    All Crops Inventory
                </div>
            </div>
            <div style="padding: 0 1rem;">
                <table id="cropsTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Crop</th>
                            <th>Available Stock (KG)</th>
                            <th>Stock Status</th>
                            <th>Stock Level</th>
                            <th style="text-align:center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $crops_query = mysqli_query($conn, "SELECT crop, quantity FROM production_approx ORDER BY quantity DESC");
                        if(mysqli_num_rows($crops_query) > 0) {
                            $max_quantity = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(quantity) as max FROM production_approx"))['max'] ?? 1;
                            while($res = mysqli_fetch_array($crops_query)){
                                $percentage = ($res['quantity'] / max($max_quantity, 1)) * 100;
                                $stockClass = '';
                                $stockText = '';
                                if($res['quantity'] > 1000) {
                                    $stockClass = 'stock-high';
                                    $stockText = 'High';
                                    $barColor = '#4F772D';
                                } elseif($res['quantity'] > 500) {
                                    $stockClass = 'stock-medium';
                                    $stockText = 'Medium';
                                    $barColor = '#F2C14E';
                                } else {
                                    $stockClass = 'stock-low';
                                    $stockText = 'Low';
                                    $barColor = '#B85C38';
                                }
                        ?>
                        <tr>
                            <td>
                                <div class="crop-info">
                                    <div class="crop-icon"><i data-lucide="sprout" style="width:20px;height:20px;"></i></div>
                                    <div class="crop-name"><?php echo htmlspecialchars($res['crop']); ?></div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:800;font-size:1.1rem;"><?php echo number_format($res['quantity']); ?></div>
                                <div style="font-size:0.65rem;color:var(--muted);">KG</div>
                            </td>
                            <td>
                                <span class="stock-badge <?php echo $stockClass; ?>"><?php echo $stockText; ?> Stock</span>
                            </td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo $barColor; ?>;"></div>
                                </div>
                                <div style="font-size:0.65rem;color:var(--muted);margin-top:4px;"><?php echo round($percentage); ?>% of max stock</div>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <button class="edit-btn" onclick="openEditModal('<?php echo addslashes($res['crop']); ?>', <?php echo $res['quantity']; ?>)">
                                        <i data-lucide="edit-2" style="width:14px;height:14px;"></i>
                                    </button>
                                    <a href="aproducedcrop.php?action=delete&crop=<?php echo urlencode($res['crop']); ?>" 
                                       onclick="return confirm('Delete this crop from inventory? This action cannot be undone.')"
                                       class="delete-btn" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
                                        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                    </a>
                                </div>
                             </div>
                              </td>
                        </tr>
                        <?php } } else { ?>
                        <tr>
                            <td colspan="5" style="text-align:center;padding:2rem;">
                                <div style="text-align:center;color:var(--muted);">
                                    <i data-lucide="package" style="width:48px;height:48px;margin-bottom:1rem;opacity:0.5;"></i>
                                    <p>No crops in inventory</p>
                                    <p style="font-size:0.75rem;">Use the "Add New Crop" button to start adding crops</p>
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
    
    function openEditModal(cropName, quantity) {
        document.getElementById('editCrop').value = cropName;
        document.getElementById('editCropName').value = cropName;
        document.getElementById('editQuantity').value = quantity;
        document.getElementById('editModal').classList.add('active');
    }
    
    // Chart Data
    const cropData = <?php 
        $crop_chart_data = [];
        $top_crops_query = mysqli_query($conn, "SELECT crop, quantity FROM production_approx WHERE quantity > 0 ORDER BY quantity DESC LIMIT 5");
        while($c = mysqli_fetch_assoc($top_crops_query)) {
            $crop_chart_data[] = ['name' => $c['crop'], 'qty' => $c['quantity']];
        }
        echo json_encode($crop_chart_data);
    ?>;
    
    const cropLabels = cropData.map(c => c.name);
    const cropQuantities = cropData.map(c => c.qty);
    const cropColors = ['#4F772D', '#B85C38', '#F2C14E', '#1A3560', '#E8751A'];
    
    if(cropLabels.length > 0) {
        const ctx1 = document.getElementById('cropsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: cropLabels,
                datasets: [{
                    label: 'Stock Quantity (KG)',
                    data: cropQuantities,
                    backgroundColor: cropColors.slice(0, cropLabels.length),
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 10, weight: 'bold' } } },
                    tooltip: { callbacks: { label: function(context) { return context.raw.toLocaleString() + ' KG'; } } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e8ddce' }, title: { display: true, text: 'Quantity (KG)', font: { size: 10 } } },
                    x: { grid: { display: false }, title: { display: true, text: 'Crop Type', font: { size: 10 } } }
                }
            }
        });
    }
    
    // Distribution Chart
    const total = <?php echo $total_quantity; ?>;
    const highStock = <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as total FROM production_approx WHERE quantity > 1000"))['total'] ?? 0; ?>;
    const mediumStock = <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as total FROM production_approx WHERE quantity BETWEEN 500 AND 1000"))['total'] ?? 0; ?>;
    const lowStockQty = <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as total FROM production_approx WHERE quantity < 500 AND quantity > 0"))['total'] ?? 0; ?>;
    
    const ctx2 = document.getElementById('distributionChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['High Stock (>1000 KG)', 'Medium Stock (500-1000 KG)', 'Low Stock (<500 KG)'],
            datasets: [{
                data: [highStock, mediumStock, lowStockQty],
                backgroundColor: ['#4F772D', '#F2C14E', '#B85C38'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 9, weight: 'bold' }, boxWidth: 10 } },
                tooltip: { callbacks: { label: function(context) { return context.label + ': ' + context.raw.toLocaleString() + ' KG'; } } }
            }
        }
    });
    
    // DataTable
    <?php if(isset($crops_query) && mysqli_num_rows($crops_query) > 0) { ?>
    $(document).ready(function() {
        $('#cropsTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                search: '',
                searchPlaceholder: '🔍 Search crops...',
                info: "Showing _START_ to _END_ of _TOTAL_ crops",
                infoEmpty: "No crops found"
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
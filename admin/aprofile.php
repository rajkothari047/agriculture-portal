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

// Get profile data from the same table
$profile_image = $row4['profile_image'] ?? 'admin.png';
$cover_image = $row4['cover_image'] ?? 'default-cover.jpg';
$bio = $row4['bio'] ?? 'Agricultural Administrator managing KisanMitra platform';
$phone = $row4['phone'] ?? '';
$admin_email = $row4['email'] ?? '';
$address = $row4['address'] ?? '';

// Get counts for stats
$farmers_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM farmerlogin"))['c'] ?? 0;
$customers_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM custlogin"))['c'] ?? 0;
$crop_listings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM farmer_crops_trade"))['c'] ?? 0;
$transactions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require ('aheader.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile — KisanMitra</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;0,800;1,400&family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

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
            --marigold:      #F0A500;
            --peepal:        #2D5A1B;
            --indigo-deep:   #1A3560;
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

        /* SIDEBAR */
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
        .brand-sub { font-size: 0.58rem; color: rgba(255,255,255,0.28); letter-spacing: 0.13em; text-transform: uppercase; margin-top: 2px; }

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

        .hero {
            height: 235px;
            background: linear-gradient(160deg, rgba(10,61,10,0.78) 0%, rgba(184,92,56,0.5) 100%), url('../assets/uploads/cover/<?php echo $cover_image; ?>') center/cover no-repeat;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 38px, rgba(255,255,255,0.018) 38px, rgba(255,255,255,0.018) 39px);
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 5px;
            background: repeating-linear-gradient(90deg, var(--saffron) 0, var(--saffron) 20px, var(--haldi) 20px, var(--haldi) 36px, var(--field-green) 36px, var(--field-green) 56px, var(--primary-dark) 56px, var(--primary-dark) 72px);
        }

        .hero-content {
            position: absolute;
            bottom: 1.7rem; left: 2.5rem; right: 2.5rem;
            display: flex; align-items: flex-end; justify-content: space-between;
        }

        .hero-title { font-family: 'Playfair Display', serif; }
        .hero-title .ht-sub { font-size: 0.82rem; color: rgba(255,255,255,0.5); letter-spacing: 0.05em; display: block; margin-top: -60px; }
        .hero-title .ht-name { font-size: 2.2rem; font-weight: 700; color: #fff; line-height: 1; letter-spacing: -0.02em; display: block; }

        .hero-badge {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 11px;
            padding: 8px 15px;
            color: #fff;
            font-size: 0.73rem; font-weight: 700;
        }

        .profile-row {
            display: flex;
            align-items: flex-end;
            gap: 1.6rem;
            padding: 0 2.5rem;
            margin-top: -55px;
            position: relative;
            z-index: 10;
            flex-wrap: wrap;
        }

        .avatar-frame {
            width: 112px; height: 112px;
            border-radius: 26px;
            border: 4px solid #fff;
            box-shadow: 0 8px 28px rgba(10,61,10,0.16), 0 2px 6px rgba(0,0,0,0.07);
            overflow: hidden;
            flex-shrink: 0;
            position: relative;
            background: var(--cream-warm);
        }

        .avatar-frame img { width: 100%; height: 100%; object-fit: cover; }

        .avatar-check {
            position: absolute;
            bottom: -5px; right: -5px;
            width: 25px; height: 25px;
            background: var(--field-green);
            border: 2.5px solid #fff;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .profile-info { padding-bottom: 0.85rem; flex: 1; min-width: 200px; }

        .profile-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem; font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em; line-height: 1.1;
        }

        .profile-sub {
            font-size: 0.76rem;
            color: #ffffff;
            font-weight: 600;
            margin-top: 4px;
            background: rgba(0, 0, 0, 0.5);
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .tag-row { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 9px; }

        .tag {
            padding: 4px 12px; border-radius: 99px;
            font-size: 0.67rem; font-weight: 800;
            letter-spacing: 0.06em; text-transform: uppercase;
        }

        .t-dark  { background: var(--primary-dark); color: var(--haldi); }
        .t-terra { background: #fdf0ea; color: var(--terracotta); border: 1px solid #f0c4ae; }
        .t-green { background: var(--light-green); color: var(--peepal); border: 1px solid #cde3b2; }
        .t-gold  { background: #fffbe6; color: #996600; border: 1px solid #f5d060; }

        .profile-actions {
            margin-left: auto;
            padding-bottom: 0.85rem;
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex; 
            align-items: center; 
            gap: 7px;
            padding: 9px 17px; 
            border-radius: 10px;
            font-size: 0.8rem; 
            font-weight: 700;
            cursor: pointer; 
            text-decoration: none; 
            border: none;
            transition: all 0.2s ease; 
            font-family: 'Nunito', sans-serif;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--field-green) 0%, var(--primary-dark) 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(79,119,45,0.35);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(79,119,45,0.4); }

        .btn-terra {
            background: linear-gradient(135deg, var(--terracotta) 0%, var(--saffron) 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(184,92,56,0.33);
        }
        .btn-terra:hover { transform: translateY(-2px); }

        .btn-ghost {
            background: #fff; 
            color: var(--text-dark);
            border: 1.5px solid var(--border-warm);
        }
        .btn-ghost:hover { border-color: var(--terracotta); background: #fdf0ea; }

        .stats-wrap { padding: 1.5rem 2.5rem 0; }

        .stats-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            border-radius: 18px; overflow: hidden;
            border: 1px solid var(--border-warm);
            box-shadow: 0 2px 12px rgba(10,61,10,0.04);
        }

        .stat-cell {
            background: #fff;
            padding: 1.25rem 1rem;
            text-align: center;
            border-right: 1px solid var(--border-warm);
            position: relative;
            transition: background 0.2s;
        }

        .stat-cell:last-child { border-right: none; }
        .stat-cell:hover { background: var(--cream-warm); }

        .stat-cell::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
        }

        .stat-cell:nth-child(1)::before { background: var(--field-green); }
        .stat-cell:nth-child(2)::before { background: var(--terracotta); }
        .stat-cell:nth-child(3)::before { background: var(--haldi); }
        .stat-cell:nth-child(4)::before { background: var(--indigo-deep); }

        .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 2rem; font-weight: 700;
            color: var(--text-dark); line-height: 1;
        }

        .stat-label {
            font-size: 0.66rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: var(--muted); margin-top: 5px;
        }

        .stat-sub { font-size: 0.68rem; font-weight: 700; margin-top: 3px; }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 336px;
            gap: 1.5rem;
            padding: 1.75rem 2.5rem 4rem;
        }

        .col-left  { display: flex; flex-direction: column; gap: 1.5rem; }
        .col-right { display: flex; flex-direction: column; gap: 1.5rem; }

        .card {
            background: #fff;
            border: 1px solid var(--border-warm);
            border-radius: 20px; overflow: hidden;
            box-shadow: 0 2px 10px rgba(10,61,10,0.04);
        }

        .card-head {
            padding: 1.05rem 1.45rem;
            border-bottom: 1px solid #f0e9de;
            display: flex; align-items: center; justify-content: space-between;
            background: var(--cream-warm);
        }

        .card-title {
            display: flex; align-items: center; gap: 9px;
            font-family: 'Playfair Display', serif;
            font-size: 0.98rem; font-weight: 700; color: var(--text-dark);
        }

        .cicon {
            width: 30px; height: 30px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .card-body { padding: 1.35rem 1.45rem; }

        .cap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.9rem; }

        .cap-item {
            display: flex; gap: 11px; padding: 1rem;
            border-radius: 13px; border: 1px solid #ede5d5;
            background: var(--bg-light); align-items: flex-start;
            transition: all 0.22s ease;
        }

        .cap-item:hover {
            border-color: var(--terracotta);
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(184,92,56,0.08);
        }

        .cap-ic { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cap-name { font-weight: 800; font-size: 0.8rem; color: var(--text-dark); font-family: 'Playfair Display', serif; }
        .cap-desc { font-size: 0.71rem; color: var(--muted); margin-top: 3px; line-height: 1.5; }

        .sig-block {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #0d5010 100%);
            border-radius: 14px; padding: 1.15rem 1.35rem;
            display: flex; align-items: center; gap: 1rem;
            margin-top: 1.25rem; position: relative; overflow: hidden;
        }

        .sig-block::before {
            content: '';
            position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
            background: linear-gradient(180deg, var(--terracotta), var(--haldi), var(--field-green));
        }

        .sig-ic {
            width: 42px; height: 42px;
            background: rgba(255,255,255,0.1); border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            color: var(--haldi); flex-shrink: 0;
        }

        .sig-block strong { display: block; font-size: 0.79rem; color: #fff; font-weight: 700; }
        .sig-block p { font-size: 0.68rem; color: rgba(255,255,255,0.38); margin-top: 3px; line-height: 1.55; }

        .act-item {
            display: flex; gap: 11px; padding: 0.85rem 0;
            border-bottom: 1px solid #f0e9de; align-items: flex-start;
        }
        .act-item:last-child { border-bottom: none; }
        .act-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
        .act-text { font-size: 0.79rem; color: var(--text-dark); line-height: 1.5; }
        .act-time { font-size: 0.68rem; color: var(--muted); margin-top: 2px; }

        .sys-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.68rem 0; border-bottom: 1px solid #f0e9de;
        }
        .sys-row:last-child { border-bottom: none; }
        .sys-label { font-size: 0.79rem; color: var(--muted); font-weight: 600; display: flex; align-items: center; gap: 8px; }

        .pill {
            padding: 3px 11px; border-radius: 99px;
            font-size: 0.63rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.07em;
        }

        .p-green { background: #e6f4d7; color: #286010; }
        .p-blue  { background: #dbeafe; color: #1e40af; }
        .p-amber { background: #fef3c7; color: #92400e; }

        .prog-item { margin-bottom: 1.05rem; }
        .prog-item:last-child { margin-bottom: 0; }
        .prog-head { display: flex; justify-content: space-between; margin-bottom: 7px; }
        .prog-name { font-size: 0.77rem; font-weight: 700; color: var(--text-dark); }
        .prog-pct  { font-size: 0.71rem; font-weight: 800; color: var(--muted); }
        .prog-track { height: 7px; background: #f0e9de; border-radius: 99px; overflow: hidden; }
        .prog-fill  { height: 100%; border-radius: 99px; }

        .info-row {
            display: flex; padding: 0.7rem 0;
            border-bottom: 1px solid #f0e9de; gap: 12px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-key { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.09em; color: var(--muted); width: 100px; flex-shrink: 0; padding-top: 1px; }
        .info-val { font-size: 0.81rem; color: var(--text-dark); font-weight: 600; }

        .qa-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 0.82rem 1rem; border-radius: 12px;
            border: 1px solid var(--border-warm);
            background: var(--bg-light);
            color: var(--text-dark); font-size: 0.81rem; font-weight: 700;
            cursor: pointer; text-decoration: none;
            transition: all 0.2s ease; margin-bottom: 7px;
            font-family: 'Nunito', sans-serif;
        }
        .qa-btn:last-child { margin-bottom: 0; }
        .qa-btn:hover {
            background: #fff; border-color: var(--terracotta);
            box-shadow: 0 4px 12px rgba(184,92,56,0.09);
            transform: translateX(4px);
        }
        .qa-ic { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .qa-arrow { margin-left: auto; color: var(--muted); }

        @media (max-width: 1100px) {
            .content-grid { grid-template-columns: 1fr; }
            .col-right { flex-direction: row; flex-wrap: wrap; }
            .col-right .card { flex: 1 1 280px; }
            .stats-strip { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 700px) {
            .sidebar { position: fixed; left: -100%; transition: left 0.28s ease; }
            .sidebar.open { left: 0; box-shadow: 6px 0 40px rgba(0,0,0,0.22); }
            .hamburger { display: flex; }
            .main { width: 100%; }
            .profile-row { 
                flex-wrap: wrap; 
                gap: 1rem; 
                padding: 0 1.2rem; 
                margin-top: -46px;
                justify-content: flex-start;
            }
            .profile-actions { 
                margin-left: 0; 
                width: 100%; 
                justify-content: flex-start;
                gap: 10px;
            }
            .profile-actions .btn {
                white-space: nowrap;
                flex: 0 0 auto;
            }
            .content-grid { padding: 1.2rem 1.2rem 3rem; }
            .stats-wrap { padding: 1.2rem 1.2rem 0; }
            .cap-grid { grid-template-columns: 1fr; }
            .topbar { padding: 0.85rem 1.2rem; }
            .hero-content { left: 1.2rem; right: 1.2rem; }
            .hero-title .ht-name { font-size: 1.7rem; }
            .col-right { flex-direction: column; }
        }

        @media (max-width: 550px) {
            .profile-actions {
                flex-direction: column;
                width: 100%;
                align-items: stretch;
            }
            .profile-actions .btn {
                width: 100%;
                justify-content: center;
                white-space: nowrap;
            }
        }

        @media (max-width: 480px) {
            .stats-strip { grid-template-columns: 1fr 1fr; }
            .hero-badge { display: none; }
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
            <a href="aviewmsg.php" class="nav-link"><i data-lucide="mail" style="width:15px;height:15px"></i> Messages</a>
            <a href="update_prices.php" class="nav-link"><i data-lucide="trending-up" style="width:15px;height:15px"></i> Market Price Updates</a>
            <div class="nav-divider"></div>
            <div class="nav-label">Account</div>
            <a href="aprofile.php" class="nav-link active"><i data-lucide="user-circle" style="width:15px;height:15px"></i> My Profile</a>
            <a href="asettings.php" class="nav-link"><i data-lucide="settings-2" style="width:15px;height:15px"></i> Settings</a>
            <a href="alogout.php" class="nav-link danger"><i data-lucide="log-out" style="width:15px;height:15px"></i> Logout</a>
        </nav>
        <div class="sidebar-foot">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;">
                <div class="pulse-dot"></div>
                <span style="font-size:0.71rem;color:#4ade80;font-weight:700;">System Online</span>
            </div>
            <div style="font-size:0.62rem;color:rgba(255,255,255,0.28);">All services operational &bull; v2.4.1</div>
            <div style="font-size:0.61rem;color:rgba(255,255,255,0.18);margin-top:6px;letter-spacing:0.03em;">🌾 Empowering Indian Farmers</div>
        </div>
    </div>
</aside>

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
                <span class="cur">Admin Profile</span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="chip chip-green"><i data-lucide="shield-check" style="width:11px;height:11px"></i> Root Access</div>
            <div class="chip chip-terra"><i data-lucide="radio" style="width:10px;height:10px"></i> Live</div>
            <div class="topbar-avatar"><?php echo strtoupper(substr($para2,0,1)); ?></div>
        </div>
    </div>

    <div class="hero">
        <div class="hero-content">
            <div class="hero-title">
                <span class="ht-sub">KisanMitra — Administrator</span>
            </div>
            <div class="hero-badge">
                <i data-lucide="fingerprint" style="width:14px;height:14px;color:var(--haldi)"></i>
                Session ADM-00<?php echo $para1; ?>
            </div>
        </div>
    </div>

    <div class="profile-row">
        <div style="position:relative;flex-shrink:0;">
            <div class="avatar-frame">
                <img src="../assets/uploads/profile/<?php echo $profile_image; ?>" alt="Admin" onerror="this.src='../assets/img/admin.png'">
            </div>
            <div class="avatar-check">
                <i data-lucide="check" style="width:12px;height:12px;color:#fff"></i>
            </div>
        </div>
        <div class="profile-info">
            <div class="profile-name"><?php echo htmlspecialchars($para2); ?></div>
            <div class="profile-sub"><?php echo htmlspecialchars($bio); ?></div>
            <div class="tag-row">
                <span class="tag t-dark">ID #<?php echo $para1; ?></span>
                <span class="tag t-terra">Root Access</span>
                <span class="tag t-green">Verified Admin</span>
                <span class="tag t-gold">🌾 Agriculture</span>
            </div>
        </div>
        <div class="profile-actions">
            <a href="asettings.php" class="btn btn-ghost"><i data-lucide="settings" style="width:14px;height:14px"></i> Settings</a>
            <a href="alogout.php" class="btn btn-terra"><i data-lucide="log-out" style="width:14px;height:14px"></i> Logout</a>
            <a href="aeditprofile.php" class="btn btn-primary"><i data-lucide="edit-2" style="width:14px;height:14px"></i> Edit Profile</a>
        </div>
    </div>

    <div class="stats-wrap">
        <div class="stats-strip">
            <div class="stat-cell">
                <div class="stat-num"><?php echo $farmers_count; ?></div>
                <div class="stat-label">Farmers</div>
                <div class="stat-sub" style="color:var(--field-green);">↑ Active</div>
            </div>
            <div class="stat-cell">
                <div class="stat-num"><?php echo $customers_count; ?></div>
                <div class="stat-label">Customers</div>
                <div class="stat-sub" style="color:var(--terracotta);">↑ Registered</div>
            </div>
            <div class="stat-cell">
                <div class="stat-num"><?php echo $crop_listings; ?></div>
                <div class="stat-label">Crop Listings</div>
                <div class="stat-sub" style="color:#996600;">In Market</div>
            </div>
            <div class="stat-cell">
                <div class="stat-num"><?php echo $transactions; ?></div>
                <div class="stat-label">Transactions</div>
                <div class="stat-sub" style="color:var(--indigo-deep);">Total</div>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <div class="col-left">
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:var(--light-green);color:var(--peepal);">
                            <i data-lucide="shield-check" style="width:15px;height:15px"></i>
                        </div>
                        Administrative Capabilities
                    </div>
                    <span class="pill p-green">Root Verified</span>
                </div>
                <div class="card-body">
                    <div class="cap-grid">
                        <div class="cap-item">
                            <div class="cap-ic" style="background:#fffbe6;color:#996600;"><i data-lucide="database" style="width:16px;height:16px"></i></div>
                            <div><div class="cap-name">Full Data Access</div><div class="cap-desc">Read, modify and audit every database record across all modules.</div></div>
                        </div>
                        <div class="cap-item">
                            <div class="cap-ic" style="background:#fdf0ea;color:var(--terracotta);"><i data-lucide="users" style="width:16px;height:16px"></i></div>
                            <div><div class="cap-name">User Governance</div><div class="cap-desc">Manage farmer registrations, verify identities, reset credentials.</div></div>
                        </div>
                        <div class="cap-item">
                            <div class="cap-ic" style="background:var(--light-green);color:var(--peepal);"><i data-lucide="sprout" style="width:16px;height:16px"></i></div>
                            <div><div class="cap-name">Crop Management</div><div class="cap-desc">Approve listings, manage pricing tiers, update stock records.</div></div>
                        </div>
                        <div class="cap-item">
                            <div class="cap-ic" style="background:#e0e7ff;color:#3730a3;"><i data-lucide="activity" style="width:16px;height:16px"></i></div>
                            <div><div class="cap-name">Audit Intelligence</div><div class="cap-desc">Monitor real-time logs, session trails, and message traffic.</div></div>
                        </div>
                        <div class="cap-item">
                            <div class="cap-ic" style="background:#fce7f3;color:#9d174d;"><i data-lucide="lock" style="width:16px;height:16px"></i></div>
                            <div><div class="cap-name">Root Security</div><div class="cap-desc">Configure system-level access controls and session protocols.</div></div>
                        </div>
                        <div class="cap-item">
                            <div class="cap-ic" style="background:#ecfdf5;color:#065f46;"><i data-lucide="bar-chart-2" style="width:16px;height:16px"></i></div>
                            <div><div class="cap-name">Analytics Suite</div><div class="cap-desc">View portal-wide metrics, export reports, track growth trends.</div></div>
                        </div>
                    </div>
                    <div class="sig-block">
                        <div class="sig-ic"><i data-lucide="fingerprint" style="width:20px;height:20px"></i></div>
                        <div>
                            <strong>Root Verification Active — Session ADM-00<?php echo $para1; ?></strong>
                            <p>All actions by <b style="color:rgba(255,255,255,0.65)"><?php echo htmlspecialchars($para2); ?></b> are cryptographically signed and retained in the compliance audit log as per Indian IT Act regulations.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#fffbe6;color:#996600;"><i data-lucide="clock" style="width:15px;height:15px"></i></div>
                        Recent Activity
                    </div>
                    <a href="alogs.php" style="font-size:0.73rem;color:var(--field-green);font-weight:800;text-decoration:none;">View All →</a>
                </div>
                <div class="card-body" style="padding-top:0.3rem;padding-bottom:0.5rem;">
                    <div class="act-item"><div class="act-dot" style="background:var(--field-green)"></div><div><div class="act-text">Approved new farmer — <strong>Ramesh Kumar</strong>, Madhya Pradesh</div><div class="act-time">Today, 10:34 AM</div></div></div>
                    <div class="act-item"><div class="act-dot" style="background:var(--terracotta)"></div><div><div class="act-text">Updated crop listing — <strong>Wheat (Grade A), ₹2,400/qtl</strong></div><div class="act-time">Today, 09:12 AM</div></div></div>
                    <div class="act-item"><div class="act-dot" style="background:var(--haldi)"></div><div><div class="act-text">Payment dispute resolved — Order <strong>#ORD-8821</strong></div><div class="act-time">Yesterday, 4:55 PM</div></div></div>
                    <div class="act-item"><div class="act-dot" style="background:var(--indigo-deep)"></div><div><div class="act-text">Customer verified — <strong>Priya Sharma</strong>, Pune</div><div class="act-time">Yesterday, 2:08 PM</div></div></div>
                </div>
            </div>
        </div>

        <div class="col-right">
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:var(--light-green);color:var(--peepal);"><i data-lucide="id-card" style="width:15px;height:15px"></i></div>
                        Account Details
                    </div>
                </div>
                <div class="card-body" style="padding-top:0.4rem;padding-bottom:0.4rem;">
                    <div class="info-row"><div class="info-key">Username</div><div class="info-val"><?php echo htmlspecialchars($para2); ?></div></div>
                    <div class="info-row"><div class="info-key">Admin ID</div><div class="info-val">#<?php echo $para1; ?></div></div>
                    <div class="info-row"><div class="info-key">Role</div><div class="info-val">Super Administrator</div></div>
                    <div class="info-row"><div class="info-key">Access</div><div class="info-val"><span class="pill p-green">Root Level</span></div></div>
                    <div class="info-row"><div class="info-key">Email</div><div class="info-val"><?php echo htmlspecialchars($admin_email ?: 'Not set'); ?></div></div>
                    <div class="info-row"><div class="info-key">Phone</div><div class="info-val"><?php echo htmlspecialchars($phone ?: 'Not set'); ?></div></div>
                    <div class="info-row"><div class="info-key">Session</div><div class="info-val" style="font-family:monospace;font-size:0.7rem;color:var(--muted);">ADM-00<?php echo $para1; ?></div></div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#e6f4d7;color:var(--peepal);"><i data-lucide="server" style="width:15px;height:15px"></i></div>
                        System Status
                    </div>
                    <span class="pill p-green">All Clear</span>
                </div>
                <div class="card-body" style="padding-top:0.3rem;padding-bottom:0.3rem;">
                    <div class="sys-row"><div class="sys-label"><i data-lucide="database" style="width:12px;height:12px"></i> Database</div><span class="pill p-green">Connected</span></div>
                    <div class="sys-row"><div class="sys-label"><i data-lucide="wifi" style="width:12px;height:12px"></i> Network</div><span class="pill p-green">Secure</span></div>
                    <div class="sys-row"><div class="sys-label"><i data-lucide="scroll-text" style="width:12px;height:12px"></i> Audit Logs</div><span class="pill p-blue">Active</span></div>
                    <div class="sys-row"><div class="sys-label"><i data-lucide="mail" style="width:12px;height:12px"></i> Mail Server</div><span class="pill p-green">Online</span></div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#fffbe6;color:#996600;"><i data-lucide="trending-up" style="width:15px;height:15px"></i></div>
                        Portal Health
                    </div>
                </div>
                <div class="card-body">
                    <div class="prog-item"><div class="prog-head"><span class="prog-name">Farmer Onboarding</span><span class="prog-pct">78%</span></div><div class="prog-track"><div class="prog-fill" style="width:78%;background:var(--field-green);"></div></div></div>
                    <div class="prog-item"><div class="prog-head"><span class="prog-name">Payment Success Rate</span><span class="prog-pct">94%</span></div><div class="prog-track"><div class="prog-fill" style="width:94%;background:var(--terracotta);"></div></div></div>
                    <div class="prog-item"><div class="prog-head"><span class="prog-name">Crop Listing Activity</span><span class="prog-pct">61%</span></div><div class="prog-track"><div class="prog-fill" style="width:61%;background:var(--haldi);"></div></div></div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#fdf0ea;color:var(--terracotta);"><i data-lucide="zap" style="width:15px;height:15px"></i></div>
                        Quick Actions
                    </div>
                </div>
                <div class="card-body">
                    <a href="afarmers.php" class="qa-btn"><div class="qa-ic" style="background:var(--light-green);color:var(--peepal);"><i data-lucide="user-plus" style="width:14px;height:14px"></i></div>Add New Farmer<i data-lucide="chevron-right" class="qa-arrow" style="width:14px;height:14px"></i></a>
                    <a href="aproducedcrop.php" class="qa-btn"><div class="qa-ic" style="background:#fffbe6;color:#996600;"><i data-lucide="sprout" style="width:14px;height:14px"></i></div>Manage Crop Stock<i data-lucide="chevron-right" class="qa-arrow" style="width:14px;height:14px"></i></a>
                    <a href="apayments.php" class="qa-btn"><div class="qa-ic" style="background:#fdf0ea;color:var(--terracotta);"><i data-lucide="indian-rupee" style="width:14px;height:14px"></i></div>View Payments<i data-lucide="chevron-right" class="qa-arrow" style="width:14px;height:14px"></i></a>
                    <a href="aviewmsg.php" class="qa-btn"><div class="qa-ic" style="background:#ede9fe;color:#5b21b6;"><i data-lucide="mail-open" style="width:14px;height:14px"></i></div>Read Messages<i data-lucide="chevron-right" class="qa-arrow" style="width:14px;height:14px"></i></a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    lucide.createIcons();
    function openSidebar() { document.getElementById('sidebar').classList.add('open'); document.getElementById('overlay').classList.add('show'); }
    function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('overlay').classList.remove('show'); }
</script>
</body>
</html>
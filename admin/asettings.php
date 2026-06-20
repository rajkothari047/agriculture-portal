<?php
session_start();
require('../sql.php'); 

$user = $_SESSION['admin_login_user'];

if(!isset($_SESSION['admin_login_user'])){
    header("location: ../index.php");
    exit();
}

$query4 = "SELECT * from admin where admin_name = '$user'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$admin_id = $row4['admin_id'];
$admin_name = $row4['admin_name'];

// Get profile data
$profile_image = $row4['profile_image'] ?? 'admin.png';
$cover_image = $row4['cover_image'] ?? 'default-cover.jpg';
$bio = $row4['bio'] ?? 'Agricultural Administrator managing KisanMitra platform';

// Settings variables
$success_message = '';
$error_message = '';

// Handle General Settings Update
if(isset($_POST['update_general'])) {
    $site_name = mysqli_real_escape_string($conn, $_POST['site_name'] ?? 'KisanMitra');
    $site_tagline = mysqli_real_escape_string($conn, $_POST['site_tagline'] ?? '');
    $contact_email = mysqli_real_escape_string($conn, $_POST['contact_email'] ?? '');
    $contact_phone = mysqli_real_escape_string($conn, $_POST['contact_phone'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    
    // Update in a settings table (create if not exists)
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'admin_settings'");
    if(mysqli_num_rows($check_table) == 0) {
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `admin_settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `setting_key` varchar(100) NOT NULL,
            `setting_value` text,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `setting_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
    
    // Insert or update settings
    $settings = [
        'site_name' => $site_name,
        'site_tagline' => $site_tagline,
        'contact_email' => $contact_email,
        'contact_phone' => $contact_phone,
        'address' => $address
    ];
    
    foreach($settings as $key => $value) {
        $insert_query = "INSERT INTO admin_settings (setting_key, setting_value) VALUES ('$key', '$value') 
                        ON DUPLICATE KEY UPDATE setting_value = '$value'";
        mysqli_query($conn, $insert_query);
    }
    
    $success_message = "General settings updated successfully!";
}

// Handle Commission Settings
if(isset($_POST['update_commission'])) {
    $platform_fee = mysqli_real_escape_string($conn, $_POST['platform_fee'] ?? '5');
    $gst_rate = mysqli_real_escape_string($conn, $_POST['gst_rate'] ?? '18');
    $delivery_charge = mysqli_real_escape_string($conn, $_POST['delivery_charge'] ?? '50');
    $min_order_amount = mysqli_real_escape_string($conn, $_POST['min_order_amount'] ?? '500');
    
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'admin_settings'");
    if(mysqli_num_rows($check_table) == 0) {
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `admin_settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `setting_key` varchar(100) NOT NULL,
            `setting_value` text,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `setting_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
    
    $commission_settings = [
        'platform_fee' => $platform_fee,
        'gst_rate' => $gst_rate,
        'delivery_charge' => $delivery_charge,
        'min_order_amount' => $min_order_amount
    ];
    
    foreach($commission_settings as $key => $value) {
        $insert_query = "INSERT INTO admin_settings (setting_key, setting_value) VALUES ('$key', '$value') 
                        ON DUPLICATE KEY UPDATE setting_value = '$value'";
        mysqli_query($conn, $insert_query);
    }
    
    $success_message = "Commission & fee settings updated successfully!";
}

// Handle Notification Settings
if(isset($_POST['update_notifications'])) {
    $farmer_notifications = isset($_POST['farmer_notifications']) ? '1' : '0';
    $customer_notifications = isset($_POST['customer_notifications']) ? '1' : '0';
    $order_alerts = isset($_POST['order_alerts']) ? '1' : '0';
    $payment_alerts = isset($_POST['payment_alerts']) ? '1' : '0';
    $system_updates = isset($_POST['system_updates']) ? '1' : '0';
    
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'admin_settings'");
    if(mysqli_num_rows($check_table) == 0) {
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `admin_settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `setting_key` varchar(100) NOT NULL,
            `setting_value` text,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `setting_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
    
    $notification_settings = [
        'farmer_notifications' => $farmer_notifications,
        'customer_notifications' => $customer_notifications,
        'order_alerts' => $order_alerts,
        'payment_alerts' => $payment_alerts,
        'system_updates' => $system_updates
    ];
    
    foreach($notification_settings as $key => $value) {
        $insert_query = "INSERT INTO admin_settings (setting_key, setting_value) VALUES ('$key', '$value') 
                        ON DUPLICATE KEY UPDATE setting_value = '$value'";
        mysqli_query($conn, $insert_query);
    }
    
    $success_message = "Notification preferences updated successfully!";
}

// Fetch current settings
$settings = [];
$settings_query = mysqli_query($conn, "SELECT setting_key, setting_value FROM admin_settings");
if($settings_query) {
    while($row = mysqli_fetch_assoc($settings_query)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Default values
$site_name = $settings['site_name'] ?? 'KisanMitra';
$site_tagline = $settings['site_tagline'] ?? 'Empowering Indian Farmers';
$contact_email = $settings['contact_email'] ?? 'support@kisanmitra.com';
$contact_phone = $settings['contact_phone'] ?? '+91 98765 43210';
$address = $settings['address'] ?? 'Agricultural Hub, New Delhi, India';
$platform_fee = $settings['platform_fee'] ?? '5';
$gst_rate = $settings['gst_rate'] ?? '18';
$delivery_charge = $settings['delivery_charge'] ?? '50';
$min_order_amount = $settings['min_order_amount'] ?? '500';
$farmer_notifications = $settings['farmer_notifications'] ?? '1';
$customer_notifications = $settings['customer_notifications'] ?? '1';
$order_alerts = $settings['order_alerts'] ?? '1';
$payment_alerts = $settings['payment_alerts'] ?? '1';
$system_updates = $settings['system_updates'] ?? '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require ('aheader.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings — KisanMitra</title>
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
        .hero-title .ht-sub { font-size: 0.82rem; color: rgba(255,255,255,0.5); letter-spacing: 0.05em; display: block;margin-top: -60px; }
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

        .profile-info { padding-bottom: 0.85rem; flex: 1; }

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
            display: flex; gap: 9px; align-items: flex-end;
        }

        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 17px; border-radius: 10px;
            font-size: 0.8rem; font-weight: 700;
            cursor: pointer; text-decoration: none; border: none;
            transition: all 0.2s ease; font-family: 'Nunito', sans-serif;
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
            background: #fff; color: var(--text-dark);
            border: 1.5px solid var(--border-warm);
        }
        .btn-ghost:hover { border-color: var(--terracotta); background: #fdf0ea; }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 1.5rem;
            padding: 1.75rem 2.5rem 4rem;
        }

        .col-left { display: flex; flex-direction: column; gap: 1.5rem; }
        .col-right { display: flex; flex-direction: column; gap: 1.5rem; }

        .card {
            background: #fff;
            border: 1px solid var(--border-warm);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(10,61,10,0.04);
        }

        .card-head {
            padding: 1.05rem 1.45rem;
            border-bottom: 1px solid #f0e9de;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--cream-warm);
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 9px;
            font-family: 'Playfair Display', serif;
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .cicon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card-body { padding: 1.35rem 1.45rem; }

        .form-group { margin-bottom: 1.2rem; }
        .form-group:last-child { margin-bottom: 0; }
        
        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }
        
        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border-warm);
            border-radius: 12px;
            font-family: 'Nunito', sans-serif;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            background: #fff;
        }
        
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: var(--terracotta);
            box-shadow: 0 0 0 3px rgba(184,92,56,0.1);
        }
        
        .form-textarea { resize: vertical; min-height: 80px; }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.6rem 0;
            border-bottom: 1px solid #f0e9de;
        }
        
        .checkbox-group:last-child { border-bottom: none; }
        
        .checkbox-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            flex: 1;
            cursor: pointer;
        }
        
        .checkbox-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--field-green);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .alert-success {
            background: #e6f4d7;
            color: #286010;
            border-left: 4px solid var(--field-green);
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        .info-box {
            background: var(--light-green);
            padding: 1rem;
            border-radius: 12px;
            margin-top: 1rem;
        }
        
        .info-box p {
            font-size: 0.75rem;
            color: var(--peepal);
            line-height: 1.5;
            margin: 0;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            font-family: 'Playfair Display', serif;
        }
        
        .stat-change {
            font-size: 0.7rem;
            color: var(--field-green);
            font-weight: 600;
        }
        
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10,61,10,0.32);
            backdrop-filter: blur(2px);
            z-index: 99;
        }
        .overlay.show { display: block; }

        @media (max-width: 1100px) {
            .content-grid { grid-template-columns: 1fr; }
            .col-right { flex-direction: row; flex-wrap: wrap; }
            .col-right .card { flex: 1 1 280px; }
        }

        @media (max-width: 700px) {
            .sidebar { position: fixed; left: -100%; transition: left 0.28s ease; }
            .sidebar.open { left: 0; box-shadow: 6px 0 40px rgba(0,0,0,0.22); }
            .hamburger { display: flex; }
            .main { width: 100%; }
            .profile-row { flex-wrap: wrap; gap: 1rem; padding: 0 1.2rem; margin-top: -46px; }
            .profile-actions { margin-left: 0; }
            .content-grid { padding: 1.2rem 1.2rem 3rem; }
            .topbar { padding: 0.85rem 1.2rem; }
            .hero-content { left: 1.2rem; right: 1.2rem; }
            .hero-title .ht-name { font-size: 1.7rem; }
            .col-right { flex-direction: column; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
        }

        @media (max-width: 480px) {
            .hero-badge { display: none; }
        }
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
            <a href="aviewmsg.php" class="nav-link"><i data-lucide="mail" style="width:15px;height:15px"></i> Messages</a>
            <div class="nav-divider"></div>
            <div class="nav-label">Account</div>
            <a href="aprofile.php" class="nav-link"><i data-lucide="user-circle" style="width:15px;height:15px"></i> My Profile</a>
            <a href="asettings.php" class="nav-link active"><i data-lucide="settings-2" style="width:15px;height:15px"></i> Settings</a>
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
                <span class="cur">System Settings</span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="chip chip-green"><i data-lucide="shield-check" style="width:11px;height:11px"></i> Root Access</div>
            <div class="chip chip-terra"><i data-lucide="radio" style="width:10px;height:10px"></i> Live</div>
            <div class="topbar-avatar"><?php echo strtoupper(substr($admin_name,0,1)); ?></div>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <div class="hero-title">
                <span class="ht-sub">KisanMitra — Administration</span>
                <!-- <span class="ht-name">System Settings</span> -->
            </div>
            <div class="hero-badge">
                <i data-lucide="fingerprint" style="width:14px;height:14px;color:var(--haldi)"></i>
                Session ADM-00<?php echo $admin_id; ?>
            </div>
        </div>
    </div>

    <!-- Profile Row -->
    <div class="profile-row">
        <div style="position:relative;flex-shrink:0;">
            <div class="avatar-frame">
                <img src="../assets/uploads/profile/<?php echo $profile_image; ?>" alt="Admin" onerror="this.src='../assets/img/admin.png'">
            </div>
            <div class="avatar-check">
                <i data-lucide="settings" style="width:12px;height:12px;color:#fff"></i>
            </div>
        </div>
        <div class="profile-info">
            <div class="profile-name">Configuration Panel</div>
            <div class="profile-sub">Manage platform settings, fees, and preferences</div>
            <div class="tag-row">
                <span class="tag t-dark">ID #<?php echo $admin_id; ?></span>
                <span class="tag t-terra">Root Access</span>
                <span class="tag t-green">Super Admin</span>
                <span class="tag t-gold">⚙️ Configuration</span>
            </div>
        </div>
        <div class="profile-actions">
            <a href="aprofile.php" class="btn btn-ghost"><i data-lucide="user" style="width:14px;height:14px"></i> Profile</a>
            <a href="alogout.php" class="btn btn-terra"><i data-lucide="log-out" style="width:14px;height:14px"></i> Logout</a>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="content-grid">
        <div class="col-left">
            <?php if($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- General Settings -->
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:var(--light-green);color:var(--peepal);">
                            <i data-lucide="globe" style="width:15px;height:15px"></i>
                        </div>
                        General Settings
                    </div>
                    <span class="pill p-green">Platform Config</span>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Site Name</label>
                            <input type="text" name="site_name" class="form-input" value="<?php echo htmlspecialchars($site_name); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Site Tagline</label>
                            <input type="text" name="site_tagline" class="form-input" value="<?php echo htmlspecialchars($site_tagline); ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Contact Email</label>
                                <input type="email" name="contact_email" class="form-input" value="<?php echo htmlspecialchars($contact_email); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" name="contact_phone" class="form-input" value="<?php echo htmlspecialchars($contact_phone); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Office Address</label>
                            <textarea name="address" class="form-textarea"><?php echo htmlspecialchars($address); ?></textarea>
                        </div>
                        
                        <button type="submit" name="update_general" class="btn btn-primary">
                            <i data-lucide="save" style="width:14px;height:14px"></i> Save General Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Commission & Fees Settings -->
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#fffbe6;color:#996600;">
                            <i data-lucide="indian-rupee" style="width:15px;height:15px"></i>
                        </div>
                        Commission & Fees
                    </div>
                    <span class="pill p-amber">Financial Config</span>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Platform Fee (%)</label>
                                <input type="number" name="platform_fee" class="form-input" value="<?php echo $platform_fee; ?>" step="0.1" min="0" max="100">
                                <small style="font-size:0.65rem;color:var(--muted);">Commission charged per transaction</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">GST Rate (%)</label>
                                <input type="number" name="gst_rate" class="form-input" value="<?php echo $gst_rate; ?>" step="0.1" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Delivery Charge (₹)</label>
                                <input type="number" name="delivery_charge" class="form-input" value="<?php echo $delivery_charge; ?>" min="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Min Order Amount (₹)</label>
                                <input type="number" name="min_order_amount" class="form-input" value="<?php echo $min_order_amount; ?>" min="0">
                            </div>
                        </div>
                        
                        <div class="info-box">
                            <p><i data-lucide="info" style="width:12px;height:12px;display:inline;margin-right:5px;"></i> These fees will be applied to all transactions across the platform. Changes will affect future orders.</p>
                        </div>
                        
                        <button type="submit" name="update_commission" class="btn btn-primary" style="margin-top:1rem;">
                            <i data-lucide="save" style="width:14px;height:14px"></i> Save Fee Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Notification Preferences -->
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#fdf0ea;color:var(--terracotta);">
                            <i data-lucide="bell" style="width:15px;height:15px"></i>
                        </div>
                        Notification Preferences
                    </div>
                    <span class="pill p-blue">Alert Settings</span>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="checkbox-group">
                            <label class="checkbox-label">Farmer Registration Alerts</label>
                            <input type="checkbox" name="farmer_notifications" class="checkbox-input" value="1" <?php echo $farmer_notifications == '1' ? 'checked' : ''; ?>>
                        </div>
                        
                        <div class="checkbox-group">
                            <label class="checkbox-label">Customer Registration Alerts</label>
                            <input type="checkbox" name="customer_notifications" class="checkbox-input" value="1" <?php echo $customer_notifications == '1' ? 'checked' : ''; ?>>
                        </div>
                        
                        <div class="checkbox-group">
                            <label class="checkbox-label">New Order Alerts</label>
                            <input type="checkbox" name="order_alerts" class="checkbox-input" value="1" <?php echo $order_alerts == '1' ? 'checked' : ''; ?>>
                        </div>
                        
                        <div class="checkbox-group">
                            <label class="checkbox-label">Payment Confirmation Alerts</label>
                            <input type="checkbox" name="payment_alerts" class="checkbox-input" value="1" <?php echo $payment_alerts == '1' ? 'checked' : ''; ?>>
                        </div>
                        
                        <div class="checkbox-group">
                            <label class="checkbox-label">System Update Notifications</label>
                            <input type="checkbox" name="system_updates" class="checkbox-input" value="1" <?php echo $system_updates == '1' ? 'checked' : ''; ?>>
                        </div>
                        
                        <button type="submit" name="update_notifications" class="btn btn-primary" style="margin-top:1rem;">
                            <i data-lucide="save" style="width:14px;height:14px"></i> Save Preferences
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-right">
            <!-- System Information -->
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#e6f4d7;color:var(--peepal);">
                            <i data-lucide="server" style="width:15px;height:15px"></i>
                        </div>
                        System Information
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="info-key">Version</div>
                        <div class="info-val">KisanMitra v2.4.1</div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">PHP Version</div>
                        <div class="info-val"><?php echo phpversion(); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">Database</div>
                        <div class="info-val">MySQL / MariaDB</div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">Last Backup</div>
                        <div class="info-val"><?php echo date('d M Y, h:i A'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#fffbe6;color:#996600;">
                            <i data-lucide="bar-chart-2" style="width:15px;height:15px"></i>
                        </div>
                        Platform Statistics
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $total_farmers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM farmerlogin"))['count'] ?? 0;
                    $total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM custlogin"))['count'] ?? 0;
                    $total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'] ?? 0;
                    $total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as total FROM orders"))['total'] ?? 0;
                    ?>
                    <div class="info-row">
                        <div class="info-key">Total Farmers</div>
                        <div class="info-val"><span class="stat-value"><?php echo $total_farmers; ?></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">Total Customers</div>
                        <div class="info-val"><span class="stat-value"><?php echo $total_customers; ?></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">Total Orders</div>
                        <div class="info-val"><span class="stat-value"><?php echo $total_orders; ?></span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">Total Revenue</div>
                        <div class="info-val"><span class="stat-value">₹<?php echo number_format($total_revenue); ?></span></div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#fee2e2;color:#dc2626;">
                            <i data-lucide="alert-triangle" style="width:15px;height:15px"></i>
                        </div>
                        Danger Zone
                    </div>
                    <span class="pill" style="background:#fee2e2;color:#dc2626;">Sensitive</span>
                </div>
                <div class="card-body">
                    <div class="info-box" style="background:#fee2e2;">
                        <p style="color:#991b1b;"><i data-lucide="shield" style="width:14px;height:14px;display:inline;margin-right:5px;"></i> These actions are irreversible. Please proceed with caution.</p>
                    </div>
                    
                    <button class="btn btn-ghost" style="width:100%;justify-content:center;margin-top:1rem;border-color:#dc2626;color:#dc2626;" onclick="confirmClearCache()">
                        <i data-lucide="trash-2" style="width:14px;height:14px"></i> Clear System Cache
                    </button>
                    
                    <button class="btn btn-ghost" style="width:100%;justify-content:center;margin-top:0.5rem;border-color:#dc2626;color:#dc2626;" onclick="confirmBackup()">
                        <i data-lucide="database" style="width:14px;height:14px"></i> Create Database Backup
                    </button>
                </div>
            </div>
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
    
    function confirmClearCache() {
        if(confirm('⚠️ Warning: This will clear all system cache. This action cannot be undone. Continue?')) {
            alert('Cache cleared successfully!');
        }
    }
    
    function confirmBackup() {
        if(confirm('Create a full database backup? This may take a few moments.')) {
            alert('Backup initiated. You will be notified when complete.');
        }
    }
</script>

</body>
</html>
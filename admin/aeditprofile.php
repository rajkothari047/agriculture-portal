<?php
session_start();
require('../sql.php'); 

if(!isset($_SESSION['admin_login_user'])){
    header("location: ../index.php");
    exit();
}

$user = $_SESSION['admin_login_user'];

$query4 = "SELECT * from admin where admin_name = '$user'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$admin_id = $row4['admin_id'];
$admin_name = $row4['admin_name'];

$success_message = '';
$error_message = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = mysqli_real_escape_string($conn, $_POST['bio'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    
    // Handle password change if provided
    $password_update = '';
    if(!empty($_POST['new_password'])) {
        if($_POST['new_password'] === $_POST['confirm_password']) {
            $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $password_update = ", admin_password = '$hashed_password'";
        } else {
            $error_message = "Passwords do not match!";
        }
    }
    
    // Handle profile image upload
    $profile_image = $row4['profile_image'] ?? 'admin.png';
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $new_filename = 'admin_' . $admin_id . '_profile_' . time() . '.' . $ext;
            $upload_path = '../assets/uploads/profile/';
            
            if(!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            if($row4['profile_image'] != 'admin.png' && file_exists($upload_path . $row4['profile_image'])) {
                unlink($upload_path . $row4['profile_image']);
            }
            
            if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path . $new_filename)) {
                $profile_image = $new_filename;
            } else {
                $error_message = "Failed to upload profile image.";
            }
        } else {
            $error_message = "Invalid profile image format. Allowed: jpg, jpeg, png, gif, webp";
        }
    }
    
    // Handle cover image upload
    $cover_image = $row4['cover_image'] ?? 'default-cover.jpg';
    if(isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['cover_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $new_filename = 'admin_' . $admin_id . '_cover_' . time() . '.' . $ext;
            $upload_path = '../assets/uploads/cover/';
            
            if(!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            if($row4['cover_image'] != 'default-cover.jpg' && file_exists($upload_path . $row4['cover_image'])) {
                unlink($upload_path . $row4['cover_image']);
            }
            
            if(move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_path . $new_filename)) {
                $cover_image = $new_filename;
            } else {
                $error_message = "Failed to upload cover image.";
            }
        } else {
            $error_message = "Invalid cover image format. Allowed: jpg, jpeg, png, gif, webp";
        }
    }
    
    if(empty($error_message)) {
        // Update admin table with all fields
        $update_query = "UPDATE admin SET 
                         bio = '$bio',
                         phone = '$phone',
                         email = '$email',
                         address = '$address',
                         profile_image = '$profile_image',
                         cover_image = '$cover_image'
                         $password_update
                         WHERE admin_id = '$admin_id'";
        
        if(mysqli_query($conn, $update_query)) {
            $success_message = "Profile updated successfully!";
            // Refresh data
            $query4 = "SELECT * from admin where admin_id = '$admin_id'";
            $ses_sq4 = mysqli_query($conn, $query4);
            $row4 = mysqli_fetch_assoc($ses_sq4);
        } else {
            $error_message = "Database error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require ('aheader.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile — KisanMitra</title>
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
            background: linear-gradient(160deg, rgba(10,61,10,0.78) 0%, rgba(184,92,56,0.5) 100%), url('../assets/uploads/cover/<?php echo $row4['cover_image'] ?? 'default-cover.jpg'; ?>') center/cover no-repeat;
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
        .hero-title .ht-sub { font-size: 0.82rem; color: rgba(255,255,255,0.5); letter-spacing: 0.05em; display: block; margin-top: -60px;  }
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

        .btn-cancel {
            background: #fff; color: var(--text-dark);
            border: 1.5px solid var(--border-warm);
        }
        .btn-cancel:hover { border-color: var(--terracotta); background: #fdf0ea; }

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

        .info-row {
            display: flex; padding: 0.7rem 0;
            border-bottom: 1px solid #f0e9de; gap: 12px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-key { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.09em; color: var(--muted); width: 100px; flex-shrink: 0; padding-top: 1px; }
        .info-val { font-size: 0.81rem; color: var(--text-dark); font-weight: 600; }

        /* Form Styles */
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); margin-bottom: 0.5rem; }
        .form-input, .form-textarea { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid var(--border-warm); border-radius: 12px; font-family: 'Nunito', sans-serif; font-size: 0.85rem; transition: all 0.2s ease; background: #fff; }
        .form-input:focus, .form-textarea:focus { outline: none; border-color: var(--terracotta); box-shadow: 0 0 0 3px rgba(184,92,56,0.1); }
        .form-textarea { resize: vertical; min-height: 100px; }
        .image-preview { width: 120px; height: 120px; border-radius: 20px; object-fit: cover; border: 3px solid var(--border-warm); margin-bottom: 1rem; }
        .cover-preview { width: 100%; height: 150px; object-fit: cover; border-radius: 16px; border: 3px solid var(--border-warm); margin-bottom: 1rem; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .alert { padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.85rem; font-weight: 600; }
        .alert-success { background: #e6f4d7 !important; color: #000000 !important; /* Force text to Black */ border-left: 4px solid var(--field-green) !important; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.85rem; font-weight: 800; /* Extra bold to ensure it's visible */ }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }

        .overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(10,61,10,0.32); backdrop-filter: blur(2px); z-index: 99;
        }
        .overlay.show { display: block; }

        /* Responsive */
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

<!-- SIDEBAR - Exactly same as main profile -->
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
            <a href="aeditprofile.php" class="nav-link active"><i data-lucide="settings-2" style="width:15px;height:15px"></i> Edit Profile</a>
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
                <span>Profile</span>
                <i data-lucide="chevron-right" style="width:12px;height:12px;opacity:0.35"></i>
                <span class="cur">Edit Profile</span>
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
                <span class="ht-sub">KisanMitra — Edit Profile</span>
                <!-- <span class="ht-name"><?php echo htmlspecialchars($admin_name); ?></span> -->
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
                <img src="../assets/uploads/profile/<?php echo $row4['profile_image'] ?? 'admin.png'; ?>" alt="Admin" onerror="this.src='../assets/img/admin.png'">
            </div>
            <div class="avatar-check">
                <i data-lucide="edit-2" style="width:12px;height:12px;color:#fff"></i>
            </div>
        </div>
        <div class="profile-info">
            <div class="profile-name">Edit Profile</div>
            <div class="profile-sub">Update your personal information and account settings</div>
            <div class="tag-row">
                <span class="tag t-dark">ID #<?php echo $admin_id; ?></span>
                <span class="tag t-terra">Root Access</span>
                <span class="tag t-green">Verified Admin</span>
                <span class="tag t-gold">🌾 Agriculture</span>
            </div>
        </div>
        <div class="profile-actions">
            <a href="aprofile.php" class="btn btn-ghost">
                <i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back to Profile
            </a>
        </div>
    </div>

    <!-- Edit Form Grid -->
    <div class="content-grid">
        <div class="col-left">
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:var(--light-green);color:var(--peepal);">
                            <i data-lucide="user" style="width:15px;height:15px"></i>
                        </div>
                        Profile Information
                    </div>
                    <span class="pill p-green">Edit Mode</span>
                </div>
                <div class="card-body">
                    <?php if($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if($error_message): ?>
                        <div class="alert alert-error"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-input" value="<?php echo htmlspecialchars($admin_name); ?>" disabled>
                            <small style="font-size: 0.7rem; color: var(--muted);">Username cannot be changed</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Bio / Tagline</label>
                            <textarea name="bio" class="form-textarea" placeholder="Write a short bio..."><?php echo htmlspecialchars($row4['bio'] ?? 'Agricultural Administrator managing KisanMitra platform'); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($row4['email'] ?? ''); ?>" placeholder="admin@example.com">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars($row4['phone'] ?? ''); ?>" placeholder="+91 XXXXX XXXXX">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-textarea" rows="2" placeholder="Your address"><?php echo htmlspecialchars($row4['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Profile Image</label>
                            <div>
                                <img src="../assets/uploads/profile/<?php echo $row4['profile_image'] ?? 'admin.png'; ?>" class="image-preview" onerror="this.src='../assets/img/admin.png'" id="profilePreview">
                            </div>
                            <input type="file" name="profile_image" class="form-input" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImage(this, 'profilePreview')">
                            <small style="font-size: 0.7rem; color: var(--muted);">Recommended: Square image, at least 200x200px (JPG, PNG, GIF, WEBP)</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Cover Banner</label>
                            <div>
                                <img src="../assets/uploads/cover/<?php echo $row4['cover_image'] ?? 'default-cover.jpg'; ?>" class="cover-preview" id="coverPreview" onerror="this.src='https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=800&q=80'">
                            </div>
                            <input type="file" name="cover_image" class="form-input" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImage(this, 'coverPreview', true)">
                            <small style="font-size: 0.7rem; color: var(--muted);">Recommended: Wide image, at least 1200x300px (JPG, PNG, GIF, WEBP)</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Change Password (Optional)</label>
                            <input type="password" name="new_password" class="form-input" placeholder="Enter new password">
                            <input type="password" name="confirm_password" class="form-input" placeholder="Confirm new password" style="margin-top: 0.5rem;">
                            <small style="font-size: 0.7rem; color: var(--muted);">Leave blank to keep current password. Minimum 8 characters recommended.</small>
                        </div>
                        
                        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary">
                                <i data-lucide="save" style="width: 14px; height: 14px;"></i> Save Changes
                            </button>
                            <a href="aprofile.php" class="btn btn-cancel">
                                <i data-lucide="x" style="width: 14px; height: 14px;"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-right">
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#fffbe6;color:#996600;">
                            <i data-lucide="info" style="width:15px;height:15px"></i>
                        </div>
                        Account Information
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="info-key">Admin ID</div>
                        <div class="info-val">#<?php echo $admin_id; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">Username</div>
                        <div class="info-val"><?php echo htmlspecialchars($admin_name); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">Role</div>
                        <div class="info-val"><span class="pill p-green">Super Administrator</span></div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">Access Level</div>
                        <div class="info-val">Root / Full System Access</div>
                    </div>
                    <div class="info-row">
                        <div class="info-key">Last Updated</div>
                        <div class="info-val"><?php echo date('d M Y, h:i A', strtotime($row4['updated_at'] ?? 'now')); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#fdf0ea;color:var(--terracotta);">
                            <i data-lucide="help-circle" style="width:15px;height:15px"></i>
                        </div>
                        Guidelines & Tips
                    </div>
                </div>
                <div class="card-body">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 1rem; font-size: 0.8rem; display: flex; gap: 0.75rem;">
                            <i data-lucide="check-circle" style="width: 16px; height: 16px; color: var(--field-green); flex-shrink: 0;"></i>
                            <span>Use a clear professional photo for your profile</span>
                        </li>
                        <li style="margin-bottom: 1rem; font-size: 0.8rem; display: flex; gap: 0.75rem;">
                            <i data-lucide="check-circle" style="width: 16px; height: 16px; color: var(--field-green); flex-shrink: 0;"></i>
                            <span>Cover banner should represent agricultural theme</span>
                        </li>
                        <li style="margin-bottom: 1rem; font-size: 0.8rem; display: flex; gap: 0.75rem;">
                            <i data-lucide="check-circle" style="width: 16px; height: 16px; color: var(--field-green); flex-shrink: 0;"></i>
                            <span>Password must be at least 8 characters with mix of letters & numbers</span>
                        </li>
                        <li style="font-size: 0.8rem; display: flex; gap: 0.75rem;">
                            <i data-lucide="shield" style="width: 16px; height: 16px; color: var(--terracotta); flex-shrink: 0;"></i>
                            <span>All changes are logged for security audit purposes</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <div class="card-head">
                    <div class="card-title">
                        <div class="cicon" style="background:#e6f4d7;color:var(--peepal);">
                            <i data-lucide="shield-check" style="width:15px;height:15px"></i>
                        </div>
                        Security Note
                    </div>
                </div>
                <div class="card-body">
                    <div style="background: var(--light-green); padding: 1rem; border-radius: 12px;">
                        <p style="font-size: 0.75rem; color: var(--peepal); line-height: 1.5; margin: 0;">
                            <i data-lucide="lock" style="width: 14px; height: 14px; display: inline; margin-right: 5px;"></i>
                            Your account has root privileges. Any changes made here will affect your access across the entire KisanMitra platform. Please ensure your credentials are kept secure.
                        </p>
                    </div>
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
    
    function previewImage(input, previewId, isCover = false) {
        if(input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                if(isCover) {
                    preview.style.height = '150px';
                    preview.style.objectFit = 'cover';
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</body>
</html>
<?php
include ('csession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['customer_login_user'])){
    header("location: ../index.php");
} 

$query4 = "SELECT * from custlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);

$para1 = $row4['cust_id'];
$para2 = $row4['cust_name'];
$para3 = $row4['password'];
$para5 = $row4['email'];
$para6 = $row4['phone_no'];
$para7 = $row4['state'];
$para8 = $row4['city'];
$para9 = $row4['address'];
$para10 = $row4['pincode'];

// Get profile and banner pictures
$profile_pic = (!empty($row4['profile_pic'])) ? $row4['profile_pic'] : 'assets/img/default-profile.jpg';
$banner_pic = (!empty($row4['banner_pic'])) ? $row4['banner_pic'] : 'assets/img/v1.jpg';

// Get notification preferences
$email_notifications = isset($row4['email_notifications']) ? $row4['email_notifications'] : 1;
$sms_alerts = isset($row4['sms_alerts']) ? $row4['sms_alerts'] : 1;
$language_pref = isset($row4['language_pref']) ? $row4['language_pref'] : 'english';
$account_created = isset($row4['account_created']) ? $row4['account_created'] : date('Y-m-d');

if(isset($_POST['custupdate'])) {
    $id = ($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $pass = !empty($_POST['pass']) ? password_hash($_POST['pass'], PASSWORD_DEFAULT) : $para3;
    $email_notif = isset($_POST['email_notifications']) ? 1 : 0;
    $sms_alert = isset($_POST['sms_alerts']) ? 1 : 0;
    $language = mysqli_real_escape_string($conn, $_POST['language_pref']);

    $query5 = "SELECT StateName from state where StCode ='$state'";
    $ses_sq5 = mysqli_query($conn, $query5);
    $row5 = mysqli_fetch_assoc($ses_sq5);
    $statename = $row5['StateName'];
        
    $updatequery1 = "UPDATE custlogin SET 
        cust_name='$name', 
        email='$email', 
        phone_no='$mobile', 
        state='$statename', 
        city='$city', 
        address='$address', 
        pincode='$pincode', 
        password='$pass',
        email_notifications='$email_notif',
        sms_alerts='$sms_alert',
        language_pref='$language'
        WHERE cust_id='$id'";
    
    if(mysqli_query($conn, $updatequery1)) {
        $_SESSION['customer_login_user'] = $email;
        echo "<script>alert('Profile updated successfully!'); window.location='cprofile.php';</script>";
        exit();
    }
}

// Handle profile picture upload
if(isset($_POST['upload_profile_pic'])) {
    $target_dir = "assets/uploads/CProfile/";
    $full_target_dir = "../" . $target_dir;
    
    if (!file_exists($full_target_dir)) {
        mkdir($full_target_dir, 0777, true);
    }
    
    if(isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] == 0) {
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES["profile_pic"]["name"]);
        $target_file = $full_target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $allowed_types = array("jpg", "jpeg", "png", "gif", "webp");
        if(in_array($imageFileType, $allowed_types)) {
            if(move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $db_file_path = $target_dir . $file_name;
                $update_pic = "UPDATE custlogin SET profile_pic='$db_file_path' WHERE cust_id='$para1'";
                if(mysqli_query($conn, $update_pic)) {
                    echo "<script>alert('Profile picture updated successfully!'); window.location='cprofile.php';</script>";
                    exit();
                }
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG, GIF, WEBP files are allowed.'); window.location='cprofile.php';</script>";
        }
    }
}

// Handle banner picture upload
if(isset($_POST['upload_banner_pic'])) {
    $target_dir = "assets/uploads/CBanner/";
    $full_target_dir = "../" . $target_dir;
    
    if (!file_exists($full_target_dir)) {
        mkdir($full_target_dir, 0777, true);
    }
    
    if(isset($_FILES["banner_pic"]) && $_FILES["banner_pic"]["error"] == 0) {
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES["banner_pic"]["name"]);
        $target_file = $full_target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $allowed_types = array("jpg", "jpeg", "png", "gif", "webp");
        if(in_array($imageFileType, $allowed_types)) {
            if(move_uploaded_file($_FILES["banner_pic"]["tmp_name"], $target_file)) {
                $db_file_path = $target_dir . $file_name;
                $update_pic = "UPDATE custlogin SET banner_pic='$db_file_path' WHERE cust_id='$para1'";
                if(mysqli_query($conn, $update_pic)) {
                    echo "<script>alert('Banner picture updated successfully!'); window.location='cprofile.php';</script>";
                    exit();
                }
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG, GIF, WEBP files are allowed.'); window.location='cprofile.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>KisanMitra - Customer Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-primary-dark: #0A3D0A;
            --color-accent-terracotta: #B85C38;
            --color-secondary-green: #4F772D;
            --color-bg-light: #F9F7F3;
            --color-text-dark: #1E293B;
            --color-text-light: #64748B;
            --color-border: #E2E8F0;
            --color-white: #FFFFFF;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--color-bg-light) 0%, #FEFCF8 100%);
            color: var(--color-text-dark);
            overflow-x: hidden;
        }
        
        h1, h2, h3, .font-display {
            font-family: 'Playfair Display', serif;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--color-bg-light);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--color-accent-terracotta);
            border-radius: 4px;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .animate-slideInLeft {
            animation: slideInLeft 0.6s ease-out forwards;
        }
        
        .animate-slideInRight {
            animation: slideInRight 0.6s ease-out forwards;
        }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        
        /* Card Styles */
        .main-card {
            background: var(--color-white);
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(184, 92, 56, 0.1);
        }
        
        .main-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        }
        
        /* Stats Card */
        .stat-card {
            background: var(--color-white);
            border-radius: 20px;
            padding: 1.25rem 1rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(184, 92, 56, 0.1);
            text-align: center;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }
        
        /* Profile Image Container */
        .profile-img-container {
            position: relative;
            display: inline-block;
        }
        
        .profile-img-container::before {
            content: '';
            position: absolute;
            inset: -4px;
            background: linear-gradient(135deg, var(--color-accent-terracotta), var(--color-secondary-green));
            border-radius: 50%;
            z-index: 0;
            opacity: 0.6;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.6;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }
        
        .profile-img-placeholder {
            position: relative;
            z-index: 1;
            border: 4px solid var(--color-white);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        /* Banner - Minimal Blur */
        .image-banner {
            height: 380px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .banner-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: blur(1px);
            transform: scale(1.01);
        }
        
        .banner-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.4) 100%);
        }
        
        /* Info Row */
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--color-border);
            transition: all 0.3s ease;
        }
        
        .info-row:hover {
            background: linear-gradient(90deg, rgba(184, 92, 56, 0.05) 0%, transparent 100%);
            padding-left: 0.5rem;
        }
        
        /* Detail Item */
        .detail-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--color-border);
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        /* Modal Styles - Enhanced */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
        }
        
        .modal-content {
            background: linear-gradient(135deg, #FFFFFF 0%, #FEFCF8 100%);
            border-radius: 40px;
            border: 1px solid rgba(184, 92, 56, 0.2);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            margin: 2rem auto;
            max-width: 900px;
            width: calc(100% - 2rem);
            position: relative;
            overflow: hidden;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--color-primary-dark), var(--color-secondary-green));
            padding: 1.5rem;
            border-bottom: none;
            position: relative;
        }
        
        .modal-header h4 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
        }
        
        .modal-header p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0.25rem 0 0 0;
            font-size: 0.85rem;
        }
        
        .modal-header .close-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
            font-size: 1.2rem;
        }
        
        .modal-header .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
            padding: 2rem;
        }
        
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }
        
        .modal-body::-webkit-scrollbar-track {
            background: var(--color-border);
            border-radius: 3px;
        }
        
        .modal-body::-webkit-scrollbar-thumb {
            background: var(--color-accent-terracotta);
            border-radius: 3px;
        }
        
        /* Image Upload Section */
        .image-upload-card {
            background: linear-gradient(135deg, #FEF8F0, #FFF5E8);
            border-radius: 24px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(184, 92, 56, 0.2);
            transition: all 0.3s ease;
        }
        
        .image-upload-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        
        .upload-preview {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        
        .preview-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--color-accent-terracotta);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .preview-img-banner {
            width: 120px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid var(--color-accent-terracotta);
        }
        
        .change-photo-btn {
            background: var(--color-accent-terracotta);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            font-weight: 500;
        }
        
        .change-photo-btn:hover {
            background: #8e452a;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(184, 92, 56, 0.3);
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-text-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-label i {
            color: var(--color-accent-terracotta);
            margin-right: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--color-border);
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-input:focus {
            border-color: var(--color-accent-terracotta);
            box-shadow: 0 0 0 3px rgba(184, 92, 56, 0.1);
            outline: none;
        }
        
        .form-input:disabled, .form-input.readonly {
            background: #f5f5f5;
            cursor: not-allowed;
        }
        
        textarea.form-input {
            resize: vertical;
            min-height: 80px;
        }
        
        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.2rem;
        }
        
        /* Checkbox Styles */
        .checkbox-group {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .checkbox-item input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--color-accent-terracotta);
        }
        
        .checkbox-item label {
            font-size: 0.85rem;
            cursor: pointer;
            color: var(--color-text-dark);
        }
        
        /* Button Styles */
        .btn-terracotta {
            background: var(--color-accent-terracotta);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-terracotta:hover {
            background: #8e452a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(184, 92, 56, 0.3);
        }
        
        .btn-outline-terracotta {
            background: transparent;
            color: var(--color-accent-terracotta);
            border: 2px solid var(--color-accent-terracotta);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-outline-terracotta:hover {
            background: var(--color-accent-terracotta);
            color: white;
            transform: translateY(-2px);
        }
        
        /* ========== RESPONSIVE STYLES - MOBILE FIRST ========== */
        @media (max-width: 768px) {
            .image-banner {
                height: 280px;
            }
            
            .modal-content {
                margin: 1rem;
                border-radius: 28px;
            }
            
            .modal-body {
                padding: 1.25rem;
            }
            
            .upload-preview {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .checkbox-group {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .btn-terracotta, .btn-outline-terracotta {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
            }
            
            .modal-header .close-btn {
                top: 1rem;
                right: 1rem;
                width: 32px;
                height: 32px;
            }
            
            .modal-header {
                padding: 1.25rem;
            }
            
            .modal-header h4 {
                font-size: 1.25rem;
                padding-right: 2rem;
            }
            
            .modal-header p {
                font-size: 0.75rem;
            }
            
            /* Profile section responsive */
            .main-card {
                border-radius: 20px;
            }
            
            .profile-img-placeholder {
                width: 100px;
                height: 100px;
            }
            
            /* Stats cards responsive */
            .stats-grid {
                gap: 0.75rem;
            }
            
            .stat-card {
                padding: 0.875rem 0.5rem;
            }
            
            .stat-card p:first-of-type {
                font-size: 1.25rem;
            }
            
            /* Detail items responsive */
            .detail-item {
                padding: 0.5rem 0;
            }
            
            /* Form grid responsive */
            .form-grid {
                gap: 0.75rem;
            }
        }
        
        @media (max-width: 640px) {
            .image-banner {
                height: 240px;
            }
            
            .banner-overlay h1 {
                font-size: 1.75rem;
            }
            
            .banner-overlay h3 {
                font-size: 1rem;
            }
            
            /* Grid layouts */
            .grid.lg\:grid-cols-3 {
                gap: 1rem;
            }
            
            .grid-cols-2 {
                gap: 0.75rem;
            }
            
            .grid.md\:grid-cols-2 {
                gap: 0.75rem;
            }
            
            /* Form grid becomes single column */
            .form-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .md\:col-span-2 {
                grid-column: span 1;
            }
            
            /* Card padding */
            .main-card.p-5 {
                padding: 1rem;
            }
            
            .main-card.p-6 {
                padding: 1.25rem;
            }
            
            /* Upload preview images */
            .preview-img {
                width: 70px;
                height: 70px;
            }
            
            .preview-img-banner {
                width: 100px;
                height: 60px;
            }
            
            /* Button full width on mobile */
            .modal-body .flex.justify-center {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .modal-body .btn-terracotta,
            .modal-body .btn-outline-terracotta {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .image-banner {
                height: 220px;
            }
            
            .banner-overlay h1 {
                font-size: 1.5rem;
            }
            
            .profile-img-placeholder {
                width: 80px;
                height: 80px;
            }
            
            .stat-card p:first-of-type {
                font-size: 1rem;
            }
            
            .stat-card p:last-of-type {
                font-size: 0.7rem;
            }
            
            .detail-item .flex {
                gap: 0.75rem;
            }
            
            .detail-item .w-8 {
                width: 28px;
                height: 28px;
            }
            
            .detail-item p.text-sm {
                font-size: 0.8rem;
            }
        }
        
        /* Badge Styles */
        .badge-premium {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #000;
        }
        
        /* Grid Layout for form */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        
        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
        
        /* Pl spacing for address section */
        .pl-12 {
            padding-left: 3rem;
        }
        
        @media (max-width: 640px) {
            .pl-12 {
                padding-left: 2rem;
            }
        }
    </style>

    <?php include ('cheader.php'); ?> 
</head>

<body class="bg-bg-light" id="top">
    
<?php include ('cnav.php'); ?>

<!-- Hero Banner - Minimal Blur -->
<div class="image-banner flex items-center justify-center relative">
    <div class="absolute inset-0">
        <img src="<?php echo '../' . $banner_pic; ?>" alt="Banner" class="banner-image">
        <div class="banner-overlay"></div>
    </div>

    <div class="relative z-10 text-center px-4 animate-fadeInUp">
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 md:px-4 md:py-1.5 mb-3 md:mb-4">
            <i data-lucide="settings" class="w-3 h-3 md:w-4 md:h-4 text-white"></i>
            <span class="text-white text-xs md:text-sm font-medium">Manage Your Profile</span>
        </div>
        <h3 class="text-lg md:text-2xl font-light text-white mb-1 md:mb-2">Welcome back,</h3>
        <h1 class="text-3xl md:text-6xl font-bold text-white break-words" style="font-family: 'Playfair Display', serif;">
            <?php echo htmlspecialchars($para2) ?>
        </h1>
        <p class="text-white/90 text-xs md:text-base mt-2 md:mt-3 flex flex-wrap items-center justify-center gap-1 md:gap-2">
            <i data-lucide="map-pin" class="w-3 h-3 md:w-4 md:h-4"></i>
            <?php echo htmlspecialchars($para8) ?>, <?php echo htmlspecialchars($para7) ?> - <?php echo $para10 ?>
        </p>
        <p class="text-white/80 text-xs md:text-sm mt-1 md:mt-2 flex flex-wrap items-center justify-center gap-1 md:gap-2">
            <i data-lucide="phone" class="w-3 h-3"></i>
            <?php echo $para6 ?> | 
            <i data-lucide="mail" class="w-3 h-3"></i>
            <?php echo $para5 ?>
        </p>
    </div>
    
</div>

<section class="relative pb-12 md:pb-16 pt-6 md:pt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Profile Section -->
        <div class="grid lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8 mb-6 md:mb-8">
            
            <!-- Left Column - Profile Card -->
            <div class="lg:col-span-1 animate-slideInLeft">
                <div class="main-card p-4 md:p-6 text-center">
                    <div class="profile-img-container mb-3 md:mb-4">
                        <img src="<?php echo '../' . $profile_pic; ?>" alt="Customer Avatar" 
                             class="profile-img-placeholder rounded-full w-28 h-28 md:w-36 md:h-36 lg:w-44 lg:h-44 object-cover mx-auto">
                    </div>
                    
                    <h2 class="text-lg md:text-xl lg:text-2xl font-bold text-text-dark mb-1 break-words"><?php echo htmlspecialchars($para2) ?></h2>
                    
                    <div class="flex flex-wrap justify-center gap-1 md:gap-2 mb-3 md:mb-4">
                        <span class="badge-premium px-2 md:px-3 py-0.5 md:py-1 text-xs rounded-full font-semibold flex items-center gap-1">
                            <i data-lucide="star" class="w-2 h-2 md:w-3 md:h-3"></i>
                            Premium Member
                        </span>
                        <span class="px-2 md:px-3 py-0.5 md:py-1 bg-green-100 text-secondary-green text-xs rounded-full flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-2 h-2 md:w-3 md:h-3"></i>
                            Verified
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 md:gap-3 mb-4 md:mb-5 pt-1 md:pt-2">
                        <div class="bg-terracotta/5 rounded-xl p-1.5 md:p-2">
                            <p class="text-xs text-text-light">Member Since</p>
                            <p class="text-xs md:text-sm font-bold text-primary-dark"><?php echo date('M Y', strtotime($account_created)); ?></p>
                        </div>
                        <div class="bg-terracotta/5 rounded-xl p-1.5 md:p-2">
                            <p class="text-xs text-text-light">Account Status</p>
                            <p class="text-xs md:text-sm font-bold text-secondary-green">Active</p>
                        </div>
                    </div>
                    
                    <button id="editProfileBtn" 
                            class="btn-terracotta font-semibold py-2.5 md:py-3 px-4 md:px-6 rounded-full text-sm md:text-base inline-flex items-center gap-2 w-full justify-center">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                        Edit Profile
                    </button>
                    
                    <div class="mt-4 md:mt-5 pt-3 md:pt-4 border-t border-border">
                        <div class="flex items-center justify-center gap-2 text-text-light text-xs">
                            <i data-lucide="shield-check" class="w-3 h-3 md:w-4 md:h-4 text-secondary-green"></i>
                            <span>2-Factor Authentication Enabled</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Account Details -->
            <div class="lg:col-span-2 animate-slideInRight">
                <div class="main-card p-4 md:p-5 lg:p-6">
                    <div class="flex flex-wrap justify-between items-center mb-4 md:mb-5">
                        <div>
                            <h2 class="text-lg md:text-xl lg:text-2xl font-bold text-text-dark" style="font-family: 'Playfair Display', serif;">
                                Personal Information
                            </h2>
                            <p class="text-xs md:text-sm text-text-light mt-0.5 md:mt-1">Your account details and preferences</p>
                        </div>
                        <div class="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 rounded-full bg-primary-dark/5 flex items-center justify-center">
                            <i data-lucide="user-check" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-primary-dark"></i>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-x-4 md:gap-x-6 gap-y-0">
                        <?php
                        $profileDetails = [
                            'Customer ID' => ['icon' => 'fingerprint', 'value' => $para1],
                            'Full Name' => ['icon' => 'user', 'value' => $para2],
                            'Email Address' => ['icon' => 'mail', 'value' => $para5],
                            'Mobile Number' => ['icon' => 'phone', 'value' => $para6],
                            'State' => ['icon' => 'map', 'value' => $para7],
                            'City/District' => ['icon' => 'navigation', 'value' => $para8],
                            'Pincode' => ['icon' => 'hash', 'value' => $para10],
                            'Full Address' => ['icon' => 'home', 'value' => $para9, 'span' => true],
                        ];

                        foreach ($profileDetails as $label => $data) {
                            $spanClass = isset($data['span']) && $data['span'] ? 'md:col-span-2' : '';
                        ?>
                        <div class="detail-item <?php echo $spanClass; ?>">
                            <div class="flex items-start gap-2 md:gap-3">
                                <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-terracotta/5 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="<?php echo $data['icon']; ?>" class="w-3 h-3 md:w-4 md:h-4 text-terracotta"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-text-light mb-0.5 md:mb-1"><?php echo $label; ?></p>
                                    <p class="text-xs md:text-sm lg:text-base font-semibold text-text-dark break-words"><?php echo htmlspecialchars($data['value']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <div class="mt-4 md:mt-5 pt-3 md:pt-4 border-t border-border">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                            <div class="flex flex-wrap items-center justify-center gap-2 md:gap-3">
                                <div class="flex items-center gap-1 md:gap-2">
                                    <i data-lucide="bell" class="w-3 h-3 md:w-4 md:h-4 text-terracotta"></i>
                                    <span class="text-xs text-text-light">Email: <?php echo $email_notifications ? 'On' : 'Off'; ?></span>
                                </div>
                                <div class="w-px h-3 bg-border hidden sm:block"></div>
                                <div class="flex items-center gap-1 md:gap-2">
                                    <i data-lucide="smartphone" class="w-3 h-3 md:w-4 md:h-4 text-terracotta"></i>
                                    <span class="text-xs text-text-light">SMS: <?php echo $sms_alerts ? 'On' : 'Off'; ?></span>
                                </div>
                                <div class="w-px h-3 bg-border hidden sm:block"></div>
                                <div class="flex items-center gap-1 md:gap-2">
                                    <i data-lucide="globe" class="w-3 h-3 md:w-4 md:h-4 text-terracotta"></i>
                                    <span class="text-xs text-text-light">Language: <?php echo ucfirst($language_pref); ?></span>
                                </div>
                            </div>
                            <a href="../index.php" class="text-xs text-terracotta hover:underline flex items-center gap-1">
                                <i data-lucide="arrow-left" class="w-3 h-3"></i>
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 lg:gap-4 stats-grid animate-fadeInUp delay-200 mb-6 md:mb-8">
            <div class="stat-card">
                <div class="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 rounded-full bg-terracotta/10 flex items-center justify-center mx-auto mb-1 md:mb-2">
                    <i data-lucide="shopping-bag" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-terracotta"></i>
                </div>
                <p class="text-lg md:text-xl lg:text-2xl font-bold text-primary-dark">0</p>
                <p class="text-xs text-text-light">Total Orders</p>
            </div>
            <div class="stat-card">
                <div class="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 rounded-full bg-terracotta/10 flex items-center justify-center mx-auto mb-1 md:mb-2">
                    <i data-lucide="package" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-terracotta"></i>
                </div>
                <p class="text-lg md:text-xl lg:text-2xl font-bold text-primary-dark">0</p>
                <p class="text-xs text-text-light">Items Purchased</p>
            </div>
            <div class="stat-card">
                <div class="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 rounded-full bg-terracotta/10 flex items-center justify-center mx-auto mb-1 md:mb-2">
                    <i data-lucide="truck" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-terracotta"></i>
                </div>
                <p class="text-lg md:text-xl lg:text-2xl font-bold text-primary-dark">0</p>
                <p class="text-xs text-text-light">Orders Delivered</p>
            </div>
            <div class="stat-card">
                <div class="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 rounded-full bg-terracotta/10 flex items-center justify-center mx-auto mb-1 md:mb-2">
                    <i data-lucide="heart" class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 text-terracotta"></i>
                </div>
                <p class="text-lg md:text-xl lg:text-2xl font-bold text-primary-dark">0</p>
                <p class="text-xs text-text-light">Wishlist Items</p>
            </div>
        </div>
        
        <!-- Additional Info Cards -->
        <div class="grid md:grid-cols-2 gap-4 md:gap-6 animate-fadeInUp delay-300">
            <div class="main-card p-4 md:p-5">
                <div class="flex flex-wrap items-center justify-between mb-3 md:mb-4">
                    <div class="flex items-center gap-2 md:gap-3">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-terracotta/10 flex items-center justify-center">
                            <i data-lucide="map-pin" class="w-4 h-4 md:w-5 md:h-5 text-terracotta"></i>
                        </div>
                        <div>
                            <h3 class="text-sm md:text-base lg:text-lg font-bold text-text-dark">Shipping Address</h3>
                            <p class="text-xs text-text-light">Primary delivery location</p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('editProfileBtn').click()" class="text-terracotta text-xs hover:underline">
                        Edit
                    </button>
                </div>
                <div class="pl-8 md:pl-12">
                    <p class="text-xs md:text-sm text-text-dark leading-relaxed break-words"><?php echo htmlspecialchars($para9); ?></p>
                    <p class="text-xs md:text-sm text-text-dark mt-1 break-words"><?php echo htmlspecialchars($para8); ?>, <?php echo htmlspecialchars($para7); ?> - <?php echo $para10; ?></p>
                    <div class="flex items-center gap-2 mt-2 md:mt-3">
                        <i data-lucide="phone" class="w-3 h-3 text-text-light"></i>
                        <span class="text-xs text-text-light"><?php echo $para6; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="main-card p-4 md:p-5">
                <div class="flex items-center gap-2 md:gap-3 mb-3 md:mb-4">
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-secondary-green/10 flex items-center justify-center">
                        <i data-lucide="settings" class="w-4 h-4 md:w-5 md:h-5 text-secondary-green"></i>
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base lg:text-lg font-bold text-text-dark">Account Preferences</h3>
                        <p class="text-xs text-text-light">Manage your preferences</p>
                    </div>
                </div>
                <div class="space-y-2 md:space-y-3 pl-8 md:pl-12">
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <div class="flex items-center gap-1 md:gap-2">
                            <i data-lucide="bell" class="w-3 h-3 md:w-4 md:h-4 text-text-light"></i>
                            <span class="text-xs md:text-sm text-text-dark">Email Notifications</span>
                        </div>
                        <span class="text-xs <?php echo $email_notifications ? 'text-secondary-green' : 'text-text-light'; ?> font-medium">
                            <?php echo $email_notifications ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <div class="flex items-center gap-1 md:gap-2">
                            <i data-lucide="smartphone" class="w-3 h-3 md:w-4 md:h-4 text-text-light"></i>
                            <span class="text-xs md:text-sm text-text-dark">SMS Alerts</span>
                        </div>
                        <span class="text-xs <?php echo $sms_alerts ? 'text-secondary-green' : 'text-text-light'; ?> font-medium">
                            <?php echo $sms_alerts ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <div class="flex items-center gap-1 md:gap-2">
                            <i data-lucide="shield" class="w-3 h-3 md:w-4 md:h-4 text-text-light"></i>
                            <span class="text-xs md:text-sm text-text-dark">Two-Factor Auth</span>
                        </div>
                        <span class="text-xs text-secondary-green font-medium">Active</span>
                    </div>
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <div class="flex items-center gap-1 md:gap-2">
                            <i data-lucide="globe" class="w-3 h-3 md:w-4 md:h-4 text-text-light"></i>
                            <span class="text-xs md:text-sm text-text-dark">Language Preference</span>
                        </div>
                        <span class="text-xs text-text-dark"><?php echo ucfirst($language_pref); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="mt-5 md:mt-6 animate-fadeInUp delay-300">
            <div class="main-card p-4 md:p-5">
                <div class="flex items-center gap-2 md:gap-3 mb-3 md:mb-4">
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-terracotta/10 flex items-center justify-center">
                        <i data-lucide="activity" class="w-4 h-4 md:w-5 md:h-5 text-terracotta"></i>
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base lg:text-lg font-bold text-text-dark">Recent Activity</h3>
                        <p class="text-xs text-text-light">Your latest account activity</p>
                    </div>
                </div>
                <div class="space-y-2 md:space-y-3 pl-8 md:pl-12">
                    <div class="flex flex-wrap items-center gap-2 md:gap-3 text-xs md:text-sm text-text-dark">
                        <i data-lucide="check-circle" class="w-3 h-3 md:w-4 md:h-4 text-secondary-green flex-shrink-0"></i>
                        <span>Account verified successfully</span>
                        <span class="text-xs text-text-light ml-auto">Today</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 md:gap-3 text-xs md:text-sm text-text-dark">
                        <i data-lucide="log-in" class="w-3 h-3 md:w-4 md:h-4 text-terracotta flex-shrink-0"></i>
                        <span class="break-words">Last login from <?php echo $_SERVER['REMOTE_ADDR']; ?></span>
                        <span class="text-xs text-text-light ml-auto">Just now</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 md:gap-3 text-xs md:text-sm text-text-dark">
                        <i data-lucide="edit" class="w-3 h-3 md:w-4 md:h-4 text-text-light flex-shrink-0"></i>
                        <span>Profile last updated</span>
                        <span class="text-xs text-text-light ml-auto">Recently</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Edit Profile Modal - Enhanced with State/District Retention -->
<div id="edit" class="modal" role="dialog">
    <div class="flex justify-center items-center min-h-screen p-3 md:p-4">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="text-lg md:text-xl lg:text-2xl font-bold">✨ Edit Your Profile</h4>
                    <p class="text-xs md:text-sm opacity-80">Update your personal information and preferences</p>
                </div>
                <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <!-- Profile Picture Upload Section -->
                <div class="image-upload-card">
                    <div class="flex items-center gap-2 mb-2 md:mb-3">
                        <i data-lucide="camera" class="w-4 h-4 md:w-5 md:h-5 text-terracotta"></i>
                        <span class="font-semibold text-text-dark text-sm md:text-base">Profile Picture</span>
                    </div>
                    <div class="upload-preview">
                        <img src="<?php echo '../' . $profile_pic; ?>" alt="Current Profile" class="preview-img" id="profilePreview">
                        <form method="POST" enctype="multipart/form-data" class="inline">
                            <button type="button" class="change-photo-btn text-xs md:text-sm" onclick="document.getElementById('profile_file_input').click()">
                                <i class="fas fa-camera"></i>
                                Change Photo
                            </button>
                            <input type="file" name="profile_pic" id="profile_file_input" accept="image/*" class="hidden" onchange="this.form.submit()">
                            <input type="hidden" name="upload_profile_pic" value="1">
                        </form>
                        <p class="text-xs text-text-light ml-auto">Recommended: Square image, max 2MB</p>
                    </div>
                </div>

                <!-- Banner Picture Upload Section -->
                <div class="image-upload-card">
                    <div class="flex items-center gap-2 mb-2 md:mb-3">
                        <i data-lucide="image" class="w-4 h-4 md:w-5 md:h-5 text-terracotta"></i>
                        <span class="font-semibold text-text-dark text-sm md:text-base">Cover Banner</span>
                    </div>
                    <div class="upload-preview">
                        <img src="<?php echo '../' . $banner_pic; ?>" alt="Current Banner" class="preview-img-banner" id="bannerPreview">
                        <form method="POST" enctype="multipart/form-data" class="inline">
                            <button type="button" class="change-photo-btn text-xs md:text-sm" onclick="document.getElementById('banner_file_input').click()">
                                <i class="fas fa-camera"></i>
                                Change Banner
                            </button>
                            <input type="file" name="banner_pic" id="banner_file_input" accept="image/*" class="hidden" onchange="this.form.submit()">
                            <input type="hidden" name="upload_banner_pic" value="1">
                        </form>
                        <p class="text-xs text-text-light ml-auto">Recommended: 1200x400px, max 2MB</p>
                    </div>
                </div>

                <form method="POST" autocomplete="off">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-id-card"></i> Customer ID
                            </label>
                            <input type="text" name="id" value="<?php echo $para1 ?>" readonly class="form-input text-sm">
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-user"></i> Full Name *
                            </label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($para2) ?>" required class="form-input text-sm">
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-envelope"></i> Email Address *
                            </label>
                            <input type="email" name="email" value="<?php echo $para5 ?>" required class="form-input text-sm">
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-phone"></i> Mobile Number *
                            </label>
                            <input type="tel" name="mobile" value="<?php echo $para6 ?>" required class="form-input text-sm">
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-map-marker-alt"></i> State *
                            </label>
                            <select onChange="getdistrict(this.value);" name="state" id="state" required class="form-input text-sm">
                                <option value="">Select State</option>
                                <?php 
                                $state_query = mysqli_query($conn,"SELECT StCode, StateName FROM state");
                                while($state_row = mysqli_fetch_array($state_query)) { 
                                    $selected = ($state_row['StateName'] == $para7) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $state_row['StCode'];?>" <?php echo $selected; ?>>
                                        <?php echo $state_row['StateName'];?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-city"></i> City/District *
                            </label>
                            <select name="city" id="district-list" required class="form-input text-sm">
                                <option value="<?php echo htmlspecialchars($para8); ?>"><?php echo htmlspecialchars($para8); ?></option>
                            </select>
                        </div>

                        <div class="form-group md:col-span-2">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-home"></i> Shipping Address *
                            </label>
                            <textarea name="address" required class="form-input text-sm"><?php echo htmlspecialchars($para9) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-map-pin"></i> Pincode *
                            </label>
                            <input type="number" name="pincode" value="<?php echo $para10 ?>" required class="form-input text-sm">
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-lock"></i> New Password
                            </label>
                            <div class="relative">
                                <input type="password" name="pass" id="new_password" class="form-input text-sm pr-10" placeholder="Leave blank to keep current">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer" onclick="toggleNewPassword()">
                                    <i class="fas fa-eye text-text-light hover:text-terracotta"></i>
                                </span>
                            </div>
                            <p class="text-xs text-text-light mt-1">Minimum 6 characters</p>
                        </div>

                        <div class="form-group md:col-span-2">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-bell"></i> Notification Preferences
                            </label>
                            <div class="checkbox-group">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="email_notifications" value="1" <?php echo $email_notifications ? 'checked' : ''; ?>>
                                    <span class="text-xs md:text-sm">Email Notifications</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="sms_alerts" value="1" <?php echo $sms_alerts ? 'checked' : ''; ?>>
                                    <span class="text-xs md:text-sm">SMS Alerts</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs md:text-sm">
                                <i class="fas fa-language"></i> Language Preference
                            </label>
                            <select name="language_pref" class="form-input text-sm">
                                <option value="english" <?php echo $language_pref == 'english' ? 'selected' : ''; ?>>English</option>
                                <option value="hindi" <?php echo $language_pref == 'hindi' ? 'selected' : ''; ?>>हिंदी (Hindi)</option>
                                <option value="marathi" <?php echo $language_pref == 'marathi' ? 'selected' : ''; ?>>मराठी (Marathi)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-center gap-2 md:gap-3 mt-6 md:mt-8 pt-4 border-t border-border">
                        <button type="button" class="btn-outline-terracotta text-sm md:text-base w-full sm:w-auto justify-center" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button name="custupdate" class="btn-terracotta text-sm md:text-base w-full sm:w-auto justify-center">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include ('footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    lucide.createIcons();

    var editBtn = document.getElementById("editProfileBtn");
    var modal = document.getElementById("edit");
    var closeBtns = document.querySelectorAll(".close-btn, [data-dismiss='modal']");

    if(editBtn){
        editBtn.addEventListener("click", function () {
            modal.style.display = "block";
            document.body.style.overflow = "hidden";
        });
    }

    closeBtns.forEach(function(btn) {
        btn.addEventListener("click", function () {
            modal.style.display = "none";
            document.body.style.overflow = "";
        });
    });

    window.addEventListener("click", function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
            document.body.style.overflow = "";
        }
    });
    
    // On page load, populate districts for the current state
    var currentStateCode = $("#state option:selected").val();
    if(currentStateCode && currentStateCode !== '') {
        getdistrict(currentStateCode);
    }
});

function toggleNewPassword() {
    var x = document.getElementById("new_password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}

function getdistrict(val) {
    $.ajax({
        type: "POST",
        url: "cget_district.php",
        data: 'state_id=' + val,
        success: function(data){
            $("#district-list").html(data);
            // After loading districts, try to select the current city if it exists
            var currentCity = "<?php echo htmlspecialchars($para8); ?>";
            if(currentCity && currentCity !== '') {
                $("#district-list option").each(function() {
                    if($(this).val() == currentCity || $(this).text() == currentCity) {
                        $(this).prop('selected', true);
                    }
                });
            }
        }
    });
}
</script> 
</body>
</html>
<?php
include('fregisterScript.php');
require_once("../sql.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Farmer Registration - KisanMitra Portal</title>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<style>
:root {
    --primary-dark: #1e5128;
    --primary-medium: #4e9f3d;
    --light-bg: #f0f0f0;
    --form-bg: rgba(255,255,255,0.15);
    --brand-color: #B85C38;

    --color-primary-dark: #1e5128;
    --color-accent-terracotta: #B85C38;
    --color-secondary-green: #4e9f3d;
    --color-light-bg: #f8f9fa;
    --color-text-dark: #374151;
    --color-text-light: #6b7280;
}

body, html {
    height: 100%;
    margin: 0;
    font-family: 'Open Sans', sans-serif;
    background-color: var(--light-bg);
}

.page-container { display: flex; min-height: 100vh; }
.info-panel {
    width: 35%; min-height: 100vh; background-color: white;
    padding: 3rem; display: flex; flex-direction: column; justify-content: center; align-items: flex-start;
    box-shadow: 10px 0 30px rgba(0,0,0,0.1); position: relative; z-index: 2;
}
.form-panel {
    width: 65%; min-height: 100vh;
    background-image: url('../assets/img/leaf8.jpg');
    background-size: cover; background-position: center;
    display: flex; align-items: flex-start; justify-content: center;
    padding: 8rem 2rem 2rem; position: relative;
}
.text-accent-terracotta { color: var(--color-accent-terracotta) !important; }
.register-card-new {
    background-color: var(--form-bg);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
    padding: 2.5rem; max-width: 600px; width: 100%; color: white;
}
.brand-logo { display: flex; align-items: center; margin-bottom: 2rem; font-size: 1.5rem; font-weight: 700; color: var(--brand-color);}
.brand-logo i { font-size: 1.8rem; margin-right: 0.5rem; color: var(--primary-medium); }

.info-heading { font-size: 2.5rem; font-weight: 700; color: #333; margin-bottom: 1rem; }
.info-text { color: #666; line-height: 1.6; margin-bottom: 2rem; }
.login-link-box { margin-top: 1.5rem; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; background-color: var(--light-bg); text-align: center; width: 100%; }

.form-panel h2 { font-weight: 600; margin-bottom: 2rem; text-align: center; font-size: 1.8rem; }
.input-group { position: relative; }
.form-control {
    background-color: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 8px;
    color: white; height: 48px; padding-left: 45px; transition: all 0.3s;
}
.form-control::placeholder { color: rgba(255,255,255,0.7); }
.form-control:focus { background-color: rgba(255,255,255,0.3); border-color: var(--primary-medium); box-shadow: 0 0 10px rgba(78,159,61,0.3); color: white; }
.input-group-text { background: transparent; border: none; position: absolute; left:0; top:50%; transform: translateY(-50%); z-index:10; color:white; font-size:1.1rem; }

.toggle-password { position:absolute; right:12px; top:50%; transform: translateY(-50%); cursor:pointer; color:white; z-index:10; }
.toggle-password:hover { color: #ddd; }

label { font-weight:600; color:white; margin-bottom:0.25rem; font-size:0.9rem; }
.password-hint { color: rgba(255,255,255,0.8); font-size:0.8rem; }

.btn-register-new { background: var(--primary-medium); color:#fff; border:none; height:48px; font-weight:700; font-size:1.1rem; border-radius:8px; transition:all 0.3s; width:100%; }
.btn-register-new:hover { background: var(--primary-dark); transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,0.3); }

select.form-control { -webkit-appearance:none; -moz-appearance:none; appearance:none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23fff' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 0.75rem center; background-size:8px 10px; color:white; }
select.form-control option { color:black; background-color:white; }

header { position: fixed; top:0; left:0; width:100%; z-index:1000; transition: all 0.3s ease-in-out; background-color:transparent; }
header a { text-decoration:none !important; color:inherit !important; }
.nav-center-btn { position: relative; color:white !important; font-weight:600; padding:0.5rem 0.75rem; transition: all 0.3s; text-decoration:none !important; background:none; border:none; font-size:1rem; cursor:pointer; }
.nav-center-btn::after { content:''; position:absolute; height:2px; width:0; bottom:0; left:50%; background-color: var(--color-accent-terracotta); transition: all 0.3s ease-in-out; }
.nav-center-btn:hover::after { width:100%; left:0; }
.nav-center-btn:hover { color:white !important; }
.dropdown { position:relative; }
.dropdown-content { position:absolute; left:0; top:100%; background:white; border-radius:0.5rem; border:1px solid #d1d5db; opacity:0; visibility:hidden; transition:all 0.3s; z-index:100; min-width:200px; margin-top:0.5rem; }
.dropdown:hover .dropdown-content { opacity:1; visibility:visible; transform:translateY(0); }
.dropdown-content a { display:flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; font-size:0.875rem; color:#374151; text-decoration:none !important; transition: background 0.2s; }
.dropdown-content a:hover { background:#f3f4f6; }
.dropdown-content i { width:14px; height:14px; }
.btn-home { background-color:var(--color-accent-terracotta); color:white !important; font-weight:600; padding:0.75rem 1.5rem; border-radius:9999px; transition:all 0.2s ease-in-out; text-decoration:none !important; display:inline-block; }
.btn-home:hover { background-color:#a3502c; transform:scale(1.05); color:white !important; }

.navbar-scrolled { background-color:var(--color-primary-dark) !important; box-shadow:0 4px 12px rgba(0,0,0,0.3); }
.navbar-scrolled .nav-center-btn { color:white !important; }
.navbar-scrolled .hamburger-line { background-color: white !important; }

footer { background-color: var(--color-primary-dark); color:white; padding-top:4rem; padding-bottom:2rem; margin-top:auto; }
footer a { color: #f1f5f9; transition: color 0.2s; text-decoration:none; }
footer a:hover { color: var(--color-accent-terracotta); }
footer .border-accent { border-color:var(--color-accent-terracotta)/50; }

/* Left-side signup buttons hover */
.btn-outline-secondary:hover {
    background-color: var(--color-accent-terracotta) !important;
    border-color: var(--color-accent-terracotta) !important;
    color: white !important;
}

/* ========== HAMBURGER MENU STYLES (Mobile Only) ========== */
.hamburger-btn {
    display: none;
    flex-direction: column;
    justify-content: space-between;
    width: 30px;
    height: 24px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    z-index: 1100;
    position: relative;
}
.hamburger-line {
    width: 100%;
    height: 3px;
    background-color: white;
    border-radius: 4px;
    transition: all 0.3s ease;
}
.navbar-scrolled .hamburger-line {
    background-color: white !important;
}

/* Mobile Menu Overlay */
.mobile-menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1001;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}
.mobile-menu-overlay.active {
    opacity: 1;
    visibility: visible;
}
.mobile-menu-container {
    position: fixed;
    top: 0;
    right: -300px;
    width: 280px;
    height: 100%;
    background-color: white;
    z-index: 1002;
    box-shadow: -2px 0 15px rgba(0,0,0,0.2);
    transition: right 0.3s ease;
    overflow-y: auto;
    padding: 80px 20px 30px;
}
.mobile-menu-container.active {
    right: 0;
}
.mobile-menu-link {
    display: block;
    padding: 15px 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    text-decoration: none;
    border-bottom: 1px solid #e5e7eb;
    transition: color 0.2s;
}
.mobile-menu-link:hover {
    color: var(--color-accent-terracotta);
}
.mobile-dropdown-title {
    padding: 15px 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}
.mobile-dropdown-title i {
    transition: transform 0.3s;
}
.mobile-dropdown-title.open i {
    transform: rotate(180deg);
}
.mobile-submenu {
    padding-left: 15px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}
.mobile-submenu.open {
    max-height: 400px;
}
.mobile-submenu a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 0;
    font-size: 0.95rem;
    color: #4b5563;
    text-decoration: none;
    border-bottom: 1px solid #f3f4f6;
}
.mobile-submenu a:hover {
    color: var(--color-accent-terracotta);
}
.mobile-home-btn {
    display: block;
    margin-top: 25px;
    text-align: center;
    background-color: var(--color-accent-terracotta);
    color: white;
    padding: 12px;
    border-radius: 50px;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.2s;
}
.mobile-home-btn:hover {
    background-color: #a3502c;
}
.close-menu-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #374151;
}

/* Popup Modal Styles */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}
.popup-overlay.active {
    opacity: 1;
    visibility: visible;
}
.popup-container {
    background: linear-gradient(135deg, #F9F7F3 0%, #ECF5E2 100%);
    border-radius: 32px;
    max-width: 450px;
    width: 90%;
    margin: 20px;
    position: relative;
    transform: scale(0.9);
    transition: transform 0.3s ease;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border: 2px solid #B85C38;
}
.popup-overlay.active .popup-container {
    transform: scale(1);
}
.popup-content {
    padding: 40px 32px;
    text-align: center;
}
.popup-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #B85C38 0%, #8e452a 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    animation: bounce 0.5s ease;
}
@keyframes bounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
.popup-title {
    font-size: 28px;
    font-weight: 800;
    color: #B85C38;
    margin-bottom: 12px;
    font-family: 'Playfair Display', serif;
}
.popup-message {
    color: #374151;
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 28px;
}
.popup-button {
    background: linear-gradient(135deg, #B85C38 0%, #8e452a 100%);
    color: white;
    border: none;
    padding: 12px 32px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.popup-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(184,92,56,0.4);
}
.popup-close {
    position: absolute;
    top: 16px;
    right: 20px;
    font-size: 28px;
    cursor: pointer;
    color: #6B7280;
    transition: color 0.2s;
    background: none;
    border: none;
}
.popup-close:hover {
    color: #B85C38;
}
@media (max-width: 480px) {
    .popup-content {
        padding: 32px 24px;
    }
    .popup-title {
        font-size: 24px;
    }
    .popup-icon {
        width: 70px;
        height: 70px;
    }
}

@media (max-width: 768px) {
    .desktop-nav-links {
        display: none !important;
    }
    .desktop-home-btn {
        display: none !important;
    }
    .hamburger-btn {
        display: flex;
    }
    header nav {
        justify-content: space-between;
    }
    .logo-area {
        display: flex;
        align-items: center;
    }
    /* Make Kisan text white on mobile */
    .navbar-kisan-text {
        color: white !important;
    }
    .page-container { flex-direction: column; }
    .info-panel, .form-panel { width:100%; min-height:auto; padding:2rem 1.5rem; }
    .info-panel { order:2; box-shadow:none; }
    .form-panel { order:1; padding-top:6rem; }
    .register-card-new { max-width:100%; padding:1.5rem; }
    .info-heading { font-size:1.8rem; }
    .form-panel h2 { font-size:1.5rem; margin-bottom:1.5rem; }
    .row { flex-direction:column; }
    .col-md-6 { width:100%; margin-bottom:1rem; }
}

@media (min-width: 769px) {
    .mobile-menu-overlay, .mobile-menu-container {
        display: none;
    }
    .hamburger-btn {
        display: none;
    }
}
</style>
</head>
<body>

<!-- Navbar -->
<header id="main-header">
<nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
    <div class="flex items-center logo-area">
        <span class="text-2xl text-white mr-1">🌱</span>
        <span class="text-xl font-bold"><span class="navbar-kisan-text" style="color: var(--color-primary-black);">Kisan</span><span style="color: var(--color-accent-terracotta);">Mitra</span></span>
    </div>
    
    <!-- Desktop Navigation Links -->
    <div class="flex items-center space-x-4 desktop-nav-links">
        <a href="../contact.php" class="nav-center-btn">Contact</a>
        <div class="relative dropdown">
            <button class="nav-center-btn">Register</button>
            <div class="dropdown-content">
               <a href="../customer/cregister.php"><i data-lucide="shopping-basket"></i> Customer Account</a>
            </div>
        </div>
        <div class="relative dropdown">
            <button class="nav-center-btn">Login</button>
            <div class="dropdown-content">
                <a href="../farmer/flogin.php"><i data-lucide="log-in"></i> Farmer Login</a>
                <a href="../customer/clogin.php"><i data-lucide="log-in"></i> Customer Login</a>
                <a href="../admin/alogin.php"><i data-lucide="log-in"></i> Admin Login</a>
            </div>
        </div>
    </div>
    
    <!-- Desktop Home Button -->
    <div class="desktop-home-btn">
        <a href="../index.php" class="btn-home">Home</a>
    </div>
    
    <!-- Hamburger Button (Mobile) -->
    <button class="hamburger-btn" id="hamburgerBtn" aria-label="Menu">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
    </button>
</nav>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

<!-- Mobile Menu Container -->
<div class="mobile-menu-container" id="mobileMenuContainer">
    <button class="close-menu-btn" id="closeMenuBtn">&times;</button>
    
    <a href="../contact.php" class="mobile-menu-link">Contact</a>
    
    <!-- Register Dropdown Mobile -->
    <div class="mobile-dropdown-item">
        <div class="mobile-dropdown-title" data-dropdown="register">
            Register <i data-lucide="chevron-down" class="w-4 h-4"></i>
        </div>
        <div class="mobile-submenu" id="register-submenu">
            <a href="../customer/cregister.php"><i data-lucide="shopping-basket" class="w-4 h-4"></i> Customer Account</a>
        </div>
    </div>
    
    <!-- Login Dropdown Mobile -->
    <div class="mobile-dropdown-item">
        <div class="mobile-dropdown-title" data-dropdown="login">
            Login <i data-lucide="chevron-down" class="w-4 h-4"></i>
        </div>
        <div class="mobile-submenu" id="login-submenu">
            <a href="../farmer/flogin.php"><i data-lucide="log-in" class="w-4 h-4"></i> Farmer Login</a>
            <a href="../customer/clogin.php"><i data-lucide="log-in" class="w-4 h-4"></i> Customer Login</a>
            <a href="../admin/alogin.php"><i data-lucide="log-in" class="w-4 h-4"></i> Admin Login</a>
        </div>
    </div>
    
    <a href="../index.php" class="mobile-home-btn">🏠 Home</a>
</div>

<!-- Email Exists Popup Modal -->
<div id="emailExistsPopup" class="popup-overlay">
    <div class="popup-container">
        <button class="popup-close" onclick="closeEmailPopup()">&times;</button>
        <div class="popup-content">
            <div class="popup-icon">
                <i data-lucide="mail-x" class="w-10 h-10 text-white"></i>
            </div>
            <h3 class="popup-title">Email Already Exists!</h3>
            <p class="popup-message">
                🌾 This email address is already registered with KisanMitra.<br><br>
                Please use a different email address or <a href="../farmer/flogin.php" style="color: #B85C38; font-weight: bold;">login to your existing account</a>.
            </p>
            <button class="popup-button" onclick="closeEmailPopup()">
                <i data-lucide="try-again" class="w-5 h-5"></i> Try Another Email
            </button>
        </div>
    </div>
</div>

<div class="page-container">
    <div class="info-panel">
        <div class="brand-logo text-xl font-bold flex items-center">
            <span class="text-2xl text-white mr-1">🌱</span>
            <span style="color: var(--color-primary-dark) !important;">Kisan</span>
            <span style="color: var(--color-accent-terracotta) !important;">Mitra</span>
        </div>
        <h1 class="info-heading" style="color: var(--color-accent-terracotta) !important;">Empowering Farmers!</h1>
        <p class="info-text">Join our farmer community and showcase your produce to customers nationwide. Trusted, secure, and simple to use.</p>
        <div class="d-flex flex-column w-100 mb-4">
            <a href="#" class="btn btn-outline-secondary mb-2" style="background-color:#f0f0f0; border-color:#ddd;">
                <i class="fab fa-google mr-2"></i> Sign Up with Google
            </a>
            <a href="#" class="btn btn-outline-secondary" style="background-color:#f0f0f0; border-color:#ddd;">
                <i class="fas fa-envelope mr-2"></i> Sign Up with Email
            </a>
        </div>
        <div class="login-link-box">
            <p class="mb-0 text-dark">Already have an account? <a href="../farmer/flogin.php" class="text-success font-weight-bold">Log In here</a></p>
        </div>
    </div>

    <div class="form-panel">
        <div class="register-card-new">
            <h2>Farmer Registration</h2>
            
            <!-- Success/Error Messages -->
            <?php if(isset($error) && $error != ''): ?>
                <div class="alert alert-danger alert-dismissible fade show text-center" role="alert" style="background-color: rgba(220,53,69,0.9); border: none;">
                    <?php echo strip_tags($error); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if(isset($success) && $success != ''): ?>
                <div class="alert alert-success alert-dismissible fade show text-center" role="alert" style="background-color: rgba(40,167,69,0.9); border: none;">
                    <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registrationForm">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Farmer Name *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required/>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="email">Email Address *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="mobile">Mobile Number *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                            <input type="tel" class="form-control" id="mobile" name="mobile" pattern="[6789][0-9]{9}" placeholder="10-digit Mobile" required/>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="gender">Gender *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="dob">Date of Birth *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="date" class="form-control" id="dob" name="dob" required/>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="state">State *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                            <select class="form-control" id="state" name="state" onChange="getdistrict(this.value);" required>
                                <option value="">Select State</option>
                                <?php
                                $query = mysqli_query($conn,"SELECT * FROM state");
                                while($row = mysqli_fetch_array($query)) {
                                    echo "<option value='".$row['StCode']."'>".$row['StateName']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="district">District *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                            <select class="form-control" id="district-list" name="district" required>
                                <option value="">Select District</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="city">City / Village *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                            <input type="text" class="form-control" id="city" name="city" placeholder="City or Village Name" required/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required/>
                        <span class="toggle-password" onclick="togglePassword()"><i class="fas fa-eye" id="eye-icon"></i></span>
                    </div>
                    <small class="password-hint">Min. 8 chars, 1 number, 1 capital, 1 special char.</small>
                </div>

                <div class="form-group">
                    <label for="confirmpassword">Confirm Password *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password" required/>
                        <span class="toggle-password" onclick="toggleConfirmPassword()"><i class="fas fa-eye" id="eye-icon-confirm"></i></span>
                    </div>
                </div>

                <button type="submit" name="farmerregister" class="btn btn-register-new mt-4"><i class="fas fa-user-plus"></i> Create Account</button>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<footer id="contact">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="col-span-2 lg:col-span-1 scroll-reveal">
                <a href="#home" class="flex items-center space-x-2 mb-4">
                    <span class="text-3xl text-secondary">🌱</span>
                    <span class="text-2xl font-extrabold text-white tracking-tight">Kisan<span class="text-accent-terracotta">Mitra</span></span>
                </a>
                <p class="text-sm text-white/70 pr-8">
                    Dedicated to the prosperity of the Indian farmer and the health of the Indian consumer.
                </p>
            </div>
            <div class="scroll-reveal" style="transition-delay: 0.1s;">
                <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">Quick Links</h5>
                <ul class="space-y-3 text-sm">
                    <li><a href="../index.php#vision" class="hover:text-accent transition duration-200">Our Story</a></li>
                    <li><a href="../index.php#offerings" class="hover:text-accent transition duration-200">Platform Services</a></li>
                    <li><a href="../index.php#impact" class="hover:text-accent transition duration-200">Impact</a></li>
                    <li><a href="../index.php#benefits" class="hover:text-accent transition duration-200">Features</a></li>
                    <li><a href="../index.php#contact" class="hover:text-accent transition duration-200">Help & Support</a></li>
                </ul>
            </div>
            <div class="scroll-reveal" style="transition-delay: 0.2s;">
                <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">User Access</h5>
                <ul class="space-y-3 text-sm">
                  <li><a href="../customer/cregister.php" class="hover:text-accent transition duration-200">Customer Account</a></li>
                    <li><a href="../customer/clogin.php" class="hover:text-accent transition duration-200">Customer Login</a></li>
                    <li><a href="../admin/alogin.php" class="hover:text-accent transition duration-200">Admin Panel</a></li>
                </ul>
            </div>
            <div class="scroll-reveal" style="transition-delay: 0.3s;">
                <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">Get in Touch</h5>
                <p class="text-sm text-white/80 mb-4">Pune, Maharashtra, India.</p>
                <div class="flex space-x-4">
                    <a href="#"><i data-lucide="twitter" class="w-5 h-5"></i></a>
                    <a href="#"><i data-lucide="linkedin" class="w-5 h-5"></i></a>
                    <a href="#"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-white/20 mt-12 pt-8 text-center">
            <p class="text-sm text-white/50">&copy; <span id="current-year"></span> KisanMitra. All rights reserved. Crafted with care in India.</p>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
lucide.createIcons();

function togglePassword() {
    const pass = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if(pass.type === 'password'){ pass.type='text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
    else{ pass.type='password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
}

function toggleConfirmPassword() {
    const pass = document.getElementById('confirmpassword');
    const icon = document.getElementById('eye-icon-confirm');
    if(pass.type === 'password'){ pass.type='text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
    else{ pass.type='password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
}

function getdistrict(val){
    $.ajax({
        type: "POST",
        url: "fget_district.php",
        data: 'state_id=' + val,
        success: function(data){ $("#district-list").html(data); }
    });
}

document.getElementById('current-year').textContent = new Date().getFullYear();

const header = document.getElementById('main-header');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) header.classList.add('navbar-scrolled');
    else header.classList.remove('navbar-scrolled');
});

// Popup Functions
function showEmailPopup() {
    const popup = document.getElementById('emailExistsPopup');
    popup.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEmailPopup() {
    const popup = document.getElementById('emailExistsPopup');
    popup.classList.remove('active');
    document.body.style.overflow = '';
    lucide.createIcons();
}

// Close popup when clicking outside
const emailPopup = document.getElementById('emailExistsPopup');
if(emailPopup) {
    emailPopup.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEmailPopup();
        }
    });
}

// Check for email error from PHP
<?php if(isset($error) && (strpos($error, 'email already exists') !== false || strpos($error, 'Email already exists') !== false)): ?>
    setTimeout(function() {
        showEmailPopup();
    }, 100);
<?php endif; ?>

// Hamburger Menu Functionality
const hamburgerBtn = document.getElementById('hamburgerBtn');
const mobileMenuContainer = document.getElementById('mobileMenuContainer');
const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
const closeMenuBtn = document.getElementById('closeMenuBtn');

function openMobileMenu() {
    mobileMenuContainer.classList.add('active');
    mobileMenuOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    mobileMenuContainer.classList.remove('active');
    mobileMenuOverlay.classList.remove('active');
    document.body.style.overflow = '';
}

if(hamburgerBtn) hamburgerBtn.addEventListener('click', openMobileMenu);
if(closeMenuBtn) closeMenuBtn.addEventListener('click', closeMobileMenu);
if(mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', closeMobileMenu);

// Mobile dropdown toggles
const dropdownTitles = document.querySelectorAll('.mobile-dropdown-title');
dropdownTitles.forEach(title => {
    title.addEventListener('click', () => {
        const dropdownType = title.getAttribute('data-dropdown');
        const submenu = document.getElementById(`${dropdownType}-submenu`);
        if (submenu) {
            submenu.classList.toggle('open');
            title.classList.toggle('open');
            const chevronIcon = title.querySelector('i');
            if (chevronIcon && submenu.classList.contains('open')) {
                chevronIcon.style.transform = 'rotate(180deg)';
            } else if (chevronIcon) {
                chevronIcon.style.transform = 'rotate(0deg)';
            }
        }
    });
});

// Close mobile menu when clicking on any link
const mobileLinks = document.querySelectorAll('.mobile-menu-link, .mobile-submenu a, .mobile-home-btn');
mobileLinks.forEach(link => {
    link.addEventListener('click', closeMobileMenu);
});
</script>

</body>
</html>
<?php
include('floginScript.php'); // Includes Login Script
// Remove any echo or print statements here
?> 
<!DOCTYPE html>
<html lang="en">
<!-- rest of your HTML code -->
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Farmer Login - Agriculture Portal</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<style>
:root {
    --color-primary-dark: #1e5128;
    --color-accent-terracotta: #B85C38;
    --color-secondary-green: #4e9f3d;
    --color-light-bg: #f8f9fa;
    --color-text-dark: #374151;
    --color-text-light: #6b7280;
}

/* Body */
body { 
    font-family: 'Inter', ui-sans-serif, system-ui; 
    margin:0; 
    min-height:100vh; 
    display:flex; 
    flex-direction:column; 
    background-color:var(--color-light-bg); 
}

/* Main Content Wrapper */
.main-content { 
    flex:1; 
    display:flex; 
}

/* Left Welcome Panel */
.welcome-panel { 
    width:65%; 
    background-image: url('../assets/img/log4.jpg'); 
    background-size: cover; 
    background-position: center; 
    color:white; 
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    padding:2rem; 
    position:relative; 
}
.welcome-panel::before {
    content:''; 
    position:absolute; 
    top:0; 
    left:0; 
    width:100%; 
    height:100%; 
    background: rgba(0,0,0,0.4); 
    z-index:0; 
}
.welcome-panel > * { position:relative; z-index:1; }

/* Right Login Form Panel */
.login-form-panel { 
    width:35%; 
    min-height:100vh; 
    background:white; 
    padding:3rem; 
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    box-shadow:-8px 0 20px rgba(0,0,0,0.1); 
    position:relative; 
}

@media (max-width:768px) { 
    .welcome-panel { display:none; } 
    .login-form-panel { width:100%; box-shadow:none; padding:2rem 1.5rem; min-height:auto; } 
    .main-content { min-height:100vh; }
}

/* Logo */
.logo-kisanmitra { 
    display:flex; 
    align-items:center; 
    gap:0.5rem; 
    color:var(--color-primary-dark); 
    font-size:1.6rem; 
    font-weight:800; 
    letter-spacing:-0.05em; 
}
.logo-kisanmitra .accent-text { color:var(--color-accent-terracotta); }

/* Form Styling */
.form-label { display:block; font-weight:600; color:var(--color-text-dark); margin-bottom:0.5rem; font-size:0.95rem; }
.form-input-field { width:100%; padding:0.75rem 1rem; border:1px solid #d1d5db; border-radius:8px; font-size:1rem; color:var(--color-text-dark); transition: all 0.2s ease-in-out; }
.form-input-field:focus { outline:none; border-color:var(--color-secondary-green); box-shadow:0 0 0 3px rgba(78,159,61,0.2); }

.input-group-icon { position:relative; }
.input-group-icon .lucide { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--color-text-light); font-size:1.1rem; pointer-events:none; }
.input-group-icon .form-input-field { padding-left:40px; }

.password-toggle-icon {
    position:absolute;
    right:12px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    z-index:10;
    width:20px;
    height:20px;
    display:flex;
    align-items:center;
    justify-content:center;
}
.password-toggle-icon svg {
    width:100%;
    height:100%;
    stroke: #374151; 
    stroke-width: 2;
    fill: none;
}

/* Buttons */
.btn-primary-action { 
    width:100%; 
    padding:0.75rem 1.5rem; 
    background-color:var(--color-accent-terracotta); 
    color:white; 
    font-weight:700; 
    border-radius:8px; 
    transition: all 0.2s ease-in-out; 
    box-shadow:0 4px 10px rgba(184,92,56,0.3); 
}
.btn-primary-action:hover { 
    background-color:#a3502c; 
    box-shadow:0 6px 15px rgba(184,92,56,0.4); 
    transform: translateY(-1px); 
}
.btn-secondary-action { 
    width:100%; 
    padding:0.75rem 1.5rem; 
    background-color:#f1f5f9; 
    color:var(--color-text-dark); 
    font-weight:600; 
    border:1px solid #d1d5db; 
    border-radius:8px; 
    transition: all 0.2s ease-in-out; 
}
.btn-secondary-action:hover { 
    background-color:#e2e8f0; 
    border-color:#9ca3af; 
}

/* Navbar - Desktop Styles */
header { 
    position: fixed; 
    top:0; 
    left:0; 
    width:100%; 
    z-index:1000; 
    transition: all 0.3s ease-in-out; 
    background-color:transparent; 
}

.nav-center-btn {
    position: relative;
    color: white;
    font-weight: 600;
    padding: 0.5rem 0.75rem;
    transition: all 0.3s;
    cursor: pointer;
    background: none;
    border: none;
    font-size: 1rem;
}
.nav-center-btn::after {
    content: '';
    position: absolute;
    height: 2px;
    width: 0;
    bottom: 0;
    left: 50%;
    background-color: var(--color-accent-terracotta);
    transition: all 0.3s ease-in-out;
}
.nav-center-btn:hover::after {
    width: 100%;
    left: 0;
}

/* Dropdowns */
.dropdown { position: relative; }
.dropdown-content { 
    position: absolute; 
    left: 0; top:100%; 
    background: white; 
    border-radius: 0.5rem; 
    border:1px solid #d1d5db; 
    opacity:0; visibility:hidden; 
    transition:all 0.3s; 
    z-index:100; 
    min-width:200px;
    margin-top: 0.5rem;
}
.dropdown:hover .dropdown-content { 
    opacity:1; visibility:visible; transform:translateY(0); 
}
.dropdown-content a { 
    display:flex; align-items:center; gap:0.5rem; 
    padding:0.5rem 1rem; 
    font-size:0.875rem; color:#374151; text-decoration:none; 
    transition: background 0.2s; 
}
.dropdown-content a:hover { background:#f3f4f6; }

/* Right Home Button */
.btn-home { 
    background-color:var(--color-accent-terracotta); 
    color:white; 
    font-weight:600; 
    padding:0.75rem 1.5rem; 
    border-radius:9999px; 
    transition: all 0.2s ease-in-out; 
    text-decoration: none;
    display: inline-block;
}
.btn-home:hover { 
    background-color:#a3502c; 
    transform:scale(1.05); 
}

/* Navbar scroll class */
.navbar-scrolled { 
    background-color:var(--color-primary-dark) !important; 
    box-shadow:0 4px 12px rgba(0,0,0,0.3); 
}
.navbar-scrolled .nav-center-btn { color:white !important; }
.navbar-scrolled .hamburger-line { background-color: white !important; }

/* ========== HAMBURGER MENU STYLES (Mobile) ========== */
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
    background-color: black;
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
    right: -280px;
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
    max-height: 300px;
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
}

@media (min-width: 769px) {
    .mobile-menu-overlay, .mobile-menu-container {
        display: none;
    }
    .hamburger-btn {
        display: none;
    }
}

/* Footer */
footer { background-color: var(--color-primary-dark); color:white; padding-top:4rem; padding-bottom:2rem; margin-top: auto; }
footer a { color: #f1f5f9; transition: color 0.2s; text-decoration: none; }
footer a:hover { color: var(--color-accent-terracotta); }
footer .border-accent { border-color:var(--color-accent-terracotta)/50; }
</style>
</head>

<body class="antialiased">

<!-- Navbar -->
<header id="main-header">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
        <!-- Left Logo Area -->
        <div class="flex items-center space-x-2 logo-area">
            <span class="text-2xl text-white">🌱</span>
            <span class="text-xl font-bold text-white">Kisan<span class="text-accent-terracotta font-bold">Mitra</span></span>
        </div>

        <!-- Desktop Navigation Links -->
        <div class="flex items-center space-x-4 desktop-nav-links">
            <a href="../contact.php" class="nav-center-btn">Contact</a>

            <div class="relative dropdown">
                <button class="nav-center-btn">Register</button>
                <div class="dropdown-content">
                    <a href="../farmer/fregister.php"><i data-lucide="tractor" class="w-4 h-4"></i> Farmer Account</a>
                    <a href="../customer/cregister.php"><i data-lucide="shopping-basket" class="w-4 h-4"></i> Customer Account</a>
                </div>
            </div>

            <div class="relative dropdown">
                <button class="nav-center-btn">Login</button>
                <div class="dropdown-content">
                    <a href="../customer/clogin.php"><i data-lucide="log-in" class="w-4 h-4"></i> Customer Login</a>
                    <a href="../admin/alogin.php"><i data-lucide="log-in" class="w-4 h-4"></i> Admin Login</a>
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
            <a href="../farmer/fregister.php"><i data-lucide="tractor" class="w-4 h-4"></i> Farmer Account</a>
            <a href="../customer/cregister.php"><i data-lucide="shopping-basket" class="w-4 h-4"></i> Customer Account</a>
        </div>
    </div>
    
    <!-- Login Dropdown Mobile -->
    <div class="mobile-dropdown-item">
        <div class="mobile-dropdown-title" data-dropdown="login">
            Login <i data-lucide="chevron-down" class="w-4 h-4"></i>
        </div>
        <div class="mobile-submenu" id="login-submenu">
            <a href="../customer/clogin.php"><i data-lucide="log-in" class="w-4 h-4"></i> Customer Login</a>
            <a href="../admin/alogin.php"><i data-lucide="log-in" class="w-4 h-4"></i> Admin Login</a>
        </div>
    </div>
    
    <a href="../index.php" class="mobile-home-btn">🏠 Home</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="welcome-panel">
        <div class="text-center max-w-lg px-4 relative">
            <h1 class="text-5xl font-extrabold mb-4 leading-tight">Welcome back!</h1>
            <p class="text-lg mb-8 text-white/90">Sign in to your farmer account and explore Agriculture Portal services.</p>
            <p class="mt-12 text-sm text-white/70">"Empowering farmers, connecting consumers."</p>
        </div>
    </div>

    <div class="login-form-panel">
        <a href="../index.php" class="logo-kisanmitra">
            <span class="icon">🌱</span>
            <span>Kisan<span class="accent-text">Mitra</span></span>
        </a>

        <div class="w-full max-w-sm mx-auto mt-16">
            <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Farmer Sign In</h2>
            <p class="text-gray-500 mb-8">Enter your login credentials below.</p>

            <?php if (isset($error) && !empty($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 shadow-sm" role="alert">
                <p class="font-medium text-sm"><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-5">
                    <label for="farmerEmail" class="form-label">Email ID</label>
                    <div class="input-group-icon">
                        <i data-lucide="mail" class="lucide w-5 h-5"></i>
                        <input type="email" class="form-input-field" id="farmerEmail" name="farmer_email" placeholder="Your Email" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group-icon">
                        <i data-lucide="lock" class="lucide w-5 h-5"></i>
                        <input type="password" class="form-input-field" id="password" name="farmer_password" placeholder="••••••••" required>
                        <span class="password-toggle-icon" onclick="togglePassword()" id="eye-button">
                            <svg id="eye-icon" viewBox="0 0 24 24" width="20" height="20">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" stroke="currentColor" fill="none"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" fill="none"/>
                            </svg>
                        </span>
                    </div>
                </div>

                <button type="submit" name="farmerlogin" class="btn-primary-action mb-4">
                    <i data-lucide="log-in" class="w-5 h-5 inline-block mr-2"></i>
                    Sign In
                </button>

                <a href="../index.php" class="btn-secondary-action flex items-center justify-center">
                    <i data-lucide="home" class="w-4 h-4 mr-2 text-gray-500"></i> Back to Homepage
                </a>
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
                    Dedicated to empowering Indian farmers and connecting consumers. Cultivating trust through technology.
                </p>
            </div>

            <div class="scroll-reveal" style="transition-delay: 0.1s;">
                <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">Quick Links</h5>
                <ul class="space-y-3 text-sm">
                    <li><a href="../index.php#vision" class="hover:text-accent transition duration-200">Our Story</a></li>
                    <li><a href="../index.php#offerings" class="hover:text-accent transition duration-200">Platform Services</a></li>
                    <li><a href="../index.php#impact" class="hover:text-accent transition duration-200">Impact</a></li>
                    <li><a href="../index.php#benefits" class="hover:text-accent transition duration-200">Features</a></li>
                    <li><a href="../index.php#contact.php" class="hover:text-accent transition duration-200">Help & Support</a></li>
                </ul>
            </div>

            <div class="scroll-reveal" style="transition-delay: 0.2s;">
                <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">User Access</h5>
                <ul class="space-y-3 text-sm">
                    <li><a href="../farmer/fregister.php" class="hover:text-accent transition duration-200">Farmer Registration</a></li>
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
            <p class="text-sm text-white/50">&copy; <span id="current-year"></span> Agriculture Portal. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
// Initialize Lucide icons
lucide.createIcons();

// Password toggle function
function togglePassword() {
    const passwordField = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    if(passwordField.type === 'password'){
        passwordField.type = 'text';
        eyeIcon.innerHTML = '<path d="M1 1l22 22" stroke="currentColor" fill="none"/><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" stroke="currentColor" fill="none"/><circle cx="12" cy="12" r="3" stroke="currentColor" fill="none"/>';
    } else {
        passwordField.type = 'password';
        eyeIcon.innerHTML = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" stroke="currentColor" fill="none"/><circle cx="12" cy="12" r="3" stroke="currentColor" fill="none"/>';
    }
}

// Footer year
document.getElementById('current-year').textContent = new Date().getFullYear();

// Navbar scroll effect
const header = document.getElementById('main-header');
window.addEventListener('scroll', () => {
    if (window.scrollY > 20) {
        header.classList.add('navbar-scrolled');
    } else {
        header.classList.remove('navbar-scrolled');
    }
});

// ========== HAMBURGER MENU FUNCTIONALITY ==========
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

hamburgerBtn.addEventListener('click', openMobileMenu);
closeMenuBtn.addEventListener('click', closeMobileMenu);
mobileMenuOverlay.addEventListener('click', closeMobileMenu);

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

// Re-initialize Lucide icons inside mobile menu after dynamic changes
function refreshMobileIcons() {
    const mobileIcons = document.querySelectorAll('.mobile-menu-container [data-lucide]');
    if (mobileIcons.length) {
        lucide.createIcons({ icons: mobileIcons });
    }
}
setTimeout(refreshMobileIcons, 100);
</script>

</body>
</html>
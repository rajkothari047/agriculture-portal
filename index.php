<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KisanMitra - Nurturing Prosperity (Image Background Hero)</title>
    <!-- Load Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Lucide Icons for modern, clean icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Custom Tailwind Configuration (Richer, Darker Nature Palette) */
        :root {
            --color-primary-dark: #0A3D0A; /* Deep Neem Green */
            --color-accent-terracotta: #B85C38; /* Rustic Terracotta */
            --color-secondary-green: #4F772D; /* Lush Field Green */
            --color-bg-light: #F9F7F3; /* Warm Cream Background */
            --color-text-dark: #1E293B; /* Slate Dark */
        }
        
        /* Apply Custom Fonts and Background */
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-bg-light);
            color: var(--color-text-dark);
            overflow-x: hidden;
        }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }

        /* Custom Utilities */
        .text-primary { color: var(--color-primary-dark); }
        .bg-primary { background-color: var(--color-primary-dark); }
        .text-accent { color: var(--color-accent-terracotta); }
        .bg-accent { background-color: var(--color-accent-terracotta); }
        .text-secondary { color: var(--color-secondary-green); }

        /* Scroll Animation Setup */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .scroll-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Nav & Button Overrides */
        .nav-link {
            position: relative;
            padding-bottom: 4px;
            transition: color 0.3s;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--color-accent-terracotta);
            transition: width 0.3s ease-in-out, left 0.3s ease-in-out;
        }
        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        /* Navbar State for Hero Section (Light text over dark background) */
        .navbar-hero .nav-link,
        .navbar-hero .brand-logo-text {
            color: white !important;
        }
        .navbar-hero .brand-logo-accent {
            color: var(--color-accent-terracotta) !important;
        }

        /* Navbar State when Scrolled (Dark text over solid background) */
        .navbar-scrolled {
            background-color: var(--color-primary-dark) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .navbar-scrolled .nav-link,
        .navbar-scrolled .brand-logo-text {
            color: white !important;
        }
        .navbar-scrolled .nav-link:hover {
            color: var(--color-accent-terracotta) !important;
        }
        
        /* Hero Section Base Styling */
        .hero-section {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Overlay for readability */
        .hero-overlay {
            position: absolute;
            inset: 0;
            z-index: 10;
            background: linear-gradient(135deg, rgba(10, 61, 10, 0.6) 0%, rgba(184, 92, 56, 0.4) 100%);
        }

        /* Hero Background Slides */
        .hero-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transform: scale(1);
            transition: opacity 2s ease-in-out, transform 5s ease-in-out;
            z-index: 1;
        }

        /* Active slide */
        .hero-bg.opacity-100 {
            opacity: 1;
            transform: scale(1.05);
        }

        /* --- Parallax Section Styling --- */
        .parallax-section {
            background-image: url('assets/img/h1.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100%;
            position: relative;
            overflow: hidden;
            padding-top: 8rem;
            padding-bottom: 8rem;
        }
        .parallax-overlay {
            background-color: rgba(0, 0, 0, 0.5); 
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        /* Card Hover Effects */
        .card-modern {
            transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.4, 1), box-shadow 0.4s;
            border-bottom: 5px solid transparent;
        }
        .card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(10, 61, 10, 0.2);
            border-color: var(--color-accent-terracotta);
        }
        
        /* Feature Icon Circle */
        .feature-icon-circle {
            background-color: rgba(79, 119, 45, 0.1);
            color: var(--color-secondary-green);
            transition: all 0.3s ease;
        }
        .feature-item:hover .feature-icon-circle {
            background-color: var(--color-secondary-green);
            color: var(--color-bg-light);
        }

        /* Image Masking for unique visual */
        .image-mask-unique {
            position: relative;
        }
        .image-mask-unique::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            width: 80px;
            height: 80px;
            background-color: var(--color-accent-terracotta);
            border-radius: 12px;
            z-index: -1;
            opacity: 0.7;
        }

        /* Mobile Menu Styles */
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 98;
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
            z-index: 99;
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
            font-size: 1rem;
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
            font-size: 1rem;
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
            font-size: 0.9rem;
            color: #4b5563;
            text-decoration: none;
            border-bottom: 1px solid #f3f4f6;
        }
        .mobile-submenu a:hover {
            color: var(--color-accent-terracotta);
        }
        .mobile-home-btn {
            display: block;
            margin-top: 20px;
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

        @media (min-width: 768px) {
            /* Center Nav Links on Desktop */
            .nav-links-center {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                width: max-content;
            }
            .mobile-menu-overlay, .mobile-menu-container {
                display: none;
            }
        }
        
        @media (max-width: 767px) {
            .desktop-only {
                display: none !important;
            }
        }
        
        /* Hamburger button styles */
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
            z-index: 100;
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
        
        @media (max-width: 767px) {
            .hamburger-btn {
                display: flex;
            }
            .nav-links-center, .desktop-access-btn {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <header id="main-header" class="fixed top-0 left-0 w-full z-50 bg-transparent transition-colors duration-500 navbar-hero">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center relative">
            
            <!-- Left: Brand Logo -->
            <a href="#home" class="flex items-center space-x-2 z-10">
                <span class="text-3xl text-secondary" style="font-family: 'Playfair Display', serif;">🌱</span>
                <span class="text-2xl font-extrabold tracking-tight brand-logo-text transition-colors duration-500">Kisan<span class="brand-logo-accent text-accent transition-colors duration-500">Mitra</span></span>
            </a>
            
            <!-- Center: Navigation Links (Desktop Only) -->
            <div class="nav-links-center hidden md:flex items-center space-x-8 text-sm font-medium z-10">
                <a href="#vision" class="nav-link font-medium hover:text-accent transition duration-300">Our Vision</a>
                <a href="#offerings" class="nav-link font-medium hover:text-accent transition duration-300">Core Offerings</a>
                <a href="#impact" class="nav-link font-medium hover:text-accent transition duration-300">Impact</a>
                <a href="#benefits" class="nav-link font-medium hover:text-accent transition duration-300">Features</a>
                <a href="#contact" class="nav-link font-medium hover:text-accent transition duration-300">Contact</a>
            </div>

            <!-- Right: Unified Access Button (Desktop Only) -->
            <div class="relative group z-10 hidden md:block desktop-access-btn">
                <button class="flex items-center bg-accent text-white px-5 py-2.5 rounded-full font-semibold text-sm shadow-lg hover:bg-[#8e452a] transition duration-300 transform hover:scale-[1.02]">
                    Access Portal <i data-lucide="chevron-down" class="w-4 h-4 ml-1.5"></i>
                </button>
                <div class="absolute right-0 mt-3 w-56 bg-white border border-gray-100 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-y-2 group-hover:translate-y-0 overflow-hidden">
                    <div class="py-2">
                        <div class="px-4 py-2 text-xs font-bold uppercase text-accent border-b border-gray-100">Register</div>
                        <a href="farmer/fregister.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center"><i data-lucide="tractor" class="w-4 h-4 mr-2 text-secondary"></i> Farmer Account</a>
                        <a href="customer/cregister.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center"><i data-lucide="shopping-basket" class="w-4 h-4 mr-2 text-secondary"></i> Customer Account</a>
                        <div class="border-t border-gray-100 my-2"></div>
                        <div class="px-4 py-2 text-xs font-bold uppercase text-primary border-b border-gray-100">Login</div>
                        <a href="farmer/flogin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center"><i data-lucide="log-in" class="w-4 h-4 mr-2 text-accent"></i> Farmer Login</a>
                        <a href="customer/clogin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center"><i data-lucide="log-in" class="w-4 h-4 mr-2 text-accent"></i> Customer Login</a>
                        <a href="admin/alogin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center"><i data-lucide="lock" class="w-4 h-4 mr-2 text-accent"></i> Admin Login</a>
                    </div>
                </div>
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
        
        <a href="#vision" class="mobile-menu-link">Our Vision</a>
        <a href="#offerings" class="mobile-menu-link">Core Offerings</a>
        <a href="#impact" class="mobile-menu-link">Impact</a>
        <a href="#benefits" class="mobile-menu-link">Features</a>
        <a href="#contact" class="mobile-menu-link">Contact</a>
        
        <!-- Register Dropdown Mobile -->
        <div class="mobile-dropdown-item">
            <div class="mobile-dropdown-title" data-dropdown="register">
                Register <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </div>
            <div class="mobile-submenu" id="register-submenu">
                <a href="farmer/fregister.php"><i data-lucide="tractor" class="w-4 h-4"></i> Farmer Account</a>
                <a href="customer/cregister.php"><i data-lucide="shopping-basket" class="w-4 h-4"></i> Customer Account</a>
            </div>
        </div>
        
        <!-- Login Dropdown Mobile -->
        <div class="mobile-dropdown-item">
            <div class="mobile-dropdown-title" data-dropdown="login">
                Login <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </div>
            <div class="mobile-submenu" id="login-submenu">
                <a href="farmer/flogin.php"><i data-lucide="log-in" class="w-4 h-4"></i> Farmer Login</a>
                <a href="customer/clogin.php"><i data-lucide="log-in" class="w-4 h-4"></i> Customer Login</a>
                <a href="admin/alogin.php"><i data-lucide="lock" class="w-4 h-4"></i> Admin Login</a>
            </div>
        </div>
        
        <a href="farmer/fregister.php" class="mobile-home-btn" style="background: var(--color-secondary-green);">🌾 Join as Farmer</a>
        <a href="customer/clogin.php" class="mobile-home-btn" style="margin-top: 10px;">🛒 Customer Login</a>
        <a href="admin/alogin.php" class="mobile-home-btn" style="margin-top: 10px; background: #1e40af;">🔐 Admin Login</a>
    </div>

    <!-- Hero Section -->
    <section id="home" class="hero-section flex items-center justify-center">
        <div class="hero-bg opacity-100" style="background-image: url('assets/img/main.jpg');"></div>
        <div class="hero-bg" style="background-image: url('assets/img/main5.jpg');"></div>
        <div class="hero-bg" style="background-image: url('assets/img/main3.jpg');"></div>
        <div class="hero-bg" style="background-image: url('assets/img/main6.jpg');"></div>
        <div class="hero-overlay"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 text-center pt-20 sm:pt-0">
            <span class="inline-block px-5 py-2 mb-4 text-sm font-semibold tracking-wider uppercase bg-accent text-white rounded-full shadow-lg">
                India's Digital Agriculture Platform
            </span>
            <!-- Mobile: text-3xl, Tablet: text-5xl, Desktop: text-6xl/lg:text-8xl -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl xl:text-7xl 2xl:text-8xl font-black text-white leading-tight mb-6 drop-shadow-lg">
                Nurturing <span class="text-accent">Growth</span>, <br class="hidden sm:block"> Harvesting <span class="text-accent">Prosperity</span>.
            </h1>
            <!-- Mobile: text-base, Tablet/Desktop: text-xl -->
            <p class="text-base sm:text-lg md:text-xl text-white/90 max-w-3xl mx-auto mb-10 font-medium">
                KisanMitra empowers millions of Indian farmers with AI-driven insights, connecting them directly to consumers and fostering a sustainable, fair ecosystem.
            </p>
            <!-- Mobile: flex-col with gap-3, Desktop: flex-row -->
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                <a href="farmer/fregister.php" class="group bg-accent text-white font-bold py-3 px-6 sm:px-8 rounded-full text-base sm:text-lg shadow-xl hover:bg-[#8e452a] transition duration-300 transform hover:scale-[1.03] flex items-center justify-center">
                    Join as a Farmer <i data-lucide="arrow-right" class="w-4 h-4 sm:w-5 sm:h-5 ml-2 transition-transform group-hover:translate-x-1"></i>
                </a>
                <a href="#offerings" class="border-2 border-white text-white font-semibold py-3 px-6 sm:px-8 rounded-full text-base sm:text-lg hover:bg-white hover:text-black transition duration-300 transform hover:scale-[1.03] text-center">
                    Explore Services
                </a>
            </div>
        </div>
    </section>

    <!-- Rest of your content remains exactly the same -->
    <section id="vision" class="py-24 md:py-32 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="scroll-reveal">
                    <span class="inline-block text-sm font-semibold tracking-wider uppercase text-secondary mb-3">Our Mission</span>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-primary leading-snug mb-6">Bridging the Gap Between <span class="text-accent">Field & Fork</span></h2>
                    <p class="text-lg text-gray-600 mb-8">We aim to revolutionize the Indian agricultural supply chain. By eliminating intermediaries, we ensure farmers receive fair compensation for their hard work, and consumers get fresh, traceable, and quality produce delivered to their doorsteps.</p>
                    <ul class="space-y-4 text-gray-700 font-medium">
                        <li class="flex items-center"><i data-lucide="leaf" class="w-6 h-6 mr-3 text-secondary flex-shrink-0"></i> Sustainable & Eco-friendly Practices</li>
                        <li class="flex items-center"><i data-lucide="zap" class="w-6 h-6 mr-3 text-secondary flex-shrink-0"></i> Real-time Market and Weather Data</li>
                        <li class="flex items-center"><i data-lucide="badge-check" class="w-6 h-6 mr-3 text-secondary flex-shrink-0"></i> Guaranteed Quality and Traceability</li>
                    </ul>
                    <a href="#" class="mt-8 inline-flex items-center text-primary font-semibold hover:text-accent transition duration-300">Read Our Full Story <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i></a>
                </div>
                <div class="flex justify-center lg:justify-end relative scroll-reveal">
                    <div class="relative image-mask-unique p-6 bg-white rounded-2xl shadow-2xl">
                        <img src="assets/img/f1.jpg" alt="Smiling Indian Farmer" class="w-full h-auto object-cover rounded-xl shadow-xl transform rotate-1 transition duration-500 hover:rotate-0 hover:scale-[1.02]">
                        <div class="absolute bottom-[-40px] left-[-40px] w-48 h-32 bg-accent/90 p-4 rounded-xl shadow-2xl flex items-center justify-center transform -rotate-3 transition duration-500 hover:rotate-0 hover:scale-105">
                            <p class="text-sm font-bold text-black text-center">Trusted by <br><span class="text-4xl font-black">10K+</span> Farmers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="offerings" class="py-24 md:py-32 bg-primary/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 scroll-reveal">
                <span class="inline-block text-sm font-semibold tracking-wider uppercase text-accent mb-3">Platform Pillars</span>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-primary">Our <span class="text-accent">Core Offerings</span></h2>
                <div class="h-1 w-20 bg-accent mx-auto mt-4 rounded-full"></div>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-modern bg-white p-8 rounded-2xl shadow-xl text-center scroll-reveal">
                    <div class="p-4 inline-block rounded-full bg-primary/10 text-primary mb-6"><i data-lucide="tractor" class="w-8 h-8"></i></div>
                    <h3 class="text-2xl font-bold mb-3">For Farmers</h3>
                    <p class="text-gray-600 mb-6">Maximize yield with predictive analytics, crop guidance, and access to premium tools and fertilizers.</p>
                    <a href="farmer/fregister.php" class="inline-flex items-center text-accent font-semibold hover:text-[#8e452a] transition duration-300">Start Growing <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i></a>
                </div>
                <div class="card-modern bg-white p-8 rounded-2xl shadow-xl text-center scroll-reveal">
                    <div class="p-4 inline-block rounded-full bg-accent/10 text-accent mb-6"><i data-lucide="shopping-basket" class="w-8 h-8"></i></div>
                    <h3 class="text-2xl font-bold mb-3">For Customers</h3>
                    <p class="text-gray-600 mb-6">Shop for the freshest, ethically-sourced produce directly from local farms. Transparency and fair prices, supporting our farmers.</p>
                    <a href="customer/cregister.php" class="inline-flex items-center text-primary font-semibold hover:text-secondary transition duration-300">Shop Fresh <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i></a>
                </div>
                <div class="card-modern bg-white p-8 rounded-2xl shadow-xl text-center scroll-reveal">
                    <div class="p-4 inline-block rounded-full bg-secondary/10 text-secondary mb-6"><i data-lucide="bar-chart-3" class="w-8 h-8"></i></div>
                    <h3 class="text-2xl font-bold mb-3">Data & Management</h3>
                    <p class="text-gray-600 mb-6">Robust dashboards for administrators and bulk buyers to manage logistics, inventory, and compliance.</p>
                    <a href="admin/alogin.php" class="inline-flex items-center text-accent font-semibold hover:text-[#8e452a] transition duration-300">Access Platform <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i></a>
                </div>
            </div>
        </div>
    </section>
    
    <section id="impact" class="parallax-section">
        <div class="parallax-overlay"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 scroll-reveal">
                <span class="inline-block text-sm font-semibold tracking-wider uppercase text-white/70 mb-3">Measurable Difference</span>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-white">Our <span class="text-accent">Impact</span> on the Ecosystem</h2>
                <div class="h-1 w-20 bg-accent mx-auto mt-4 rounded-full"></div>
            </div>
            <div class="grid md:grid-cols-3 gap-12">
                <div class="bg-white/95 backdrop-blur-sm p-8 rounded-xl shadow-2xl text-center scroll-reveal">
                    <div class="text-6xl font-black text-primary mb-3">+35%</div>
                    <h4 class="text-xl font-bold text-primary mb-3">Farmer Income Boost</h4>
                    <p class="text-gray-600">Direct sales and transparent pricing models have significantly increased the take-home income for our registered farmers in key regions.</p>
                </div>
                <div class="bg-white/95 backdrop-blur-sm p-8 rounded-xl shadow-2xl text-center scroll-reveal">
                    <div class="text-6xl font-black text-primary mb-3">98%</div>
                    <h4 class="text-xl font-bold text-primary mb-3">Reduction in Food Waste</h4>
                    <p class="text-gray-600">Efficient logistics and demand forecasting reduce inventory mismatch, leading to near-zero spoilage across the supply chain.</p>
                </div>
                <div class="bg-white/95 backdrop-blur-sm p-8 rounded-xl shadow-2xl text-center scroll-reveal">
                    <div class="text-6xl font-black text-primary mb-3">6L+</div>
                    <h4 class="text-xl font-bold text-primary mb-3">Happy Customers Served</h4>
                    <p class="text-gray-600">Connecting over six lakh families to fresh, high-quality, and traceable produce that they can trust, fostering better health.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" class="py-24 md:py-32 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 scroll-reveal">
                <span class="inline-block text-sm font-semibold tracking-wider uppercase text-secondary mb-3">Why KisanMitra?</span>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-primary">Key <span class="text-accent">Differentiators</span></h2>
                <div class="h-1 w-20 bg-secondary mx-auto mt-4 rounded-full"></div>
            </div>
            <div class="grid lg:grid-cols-5 gap-12 items-center">
                <div class="lg:col-span-2 space-y-10 order-2 lg:order-1">
                    <div class="flex space-x-4 feature-item scroll-reveal">
                        <div class="feature-icon-circle p-3 rounded-full flex-shrink-0 w-12 h-12 flex items-center justify-center"><i data-lucide="cpu" class="w-6 h-6"></i></div>
                        <div><h4 class="text-xl font-bold text-primary mb-1">AI-Powered Forecasting</h4><p class="text-gray-600">Get precise, predictive analytics for pest outbreaks, soil health, and market trends right on your mobile.</p></div>
                    </div>
                    <div class="flex space-x-4 feature-item scroll-reveal">
                        <div class="feature-icon-circle p-3 rounded-full flex-shrink-0 w-12 h-12 flex items-center justify-center"><i data-lucide="wallet" class="w-6 h-6"></i></div>
                        <div><h4 class="text-xl font-bold text-primary mb-1">Zero Commission Marketplace</h4><p class="text-gray-600">Keep 100% of your earnings. Our platform is built on a subscription model, not on your hard work.</p></div>
                    </div>
                    <div class="flex space-x-4 feature-item scroll-reveal">
                        <div class="feature-icon-circle p-3 rounded-full flex-shrink-0 w-12 h-12 flex items-center justify-center"><i data-lucide="map-pin" class="w-6 h-6"></i></div>
                        <div><h4 class="text-xl font-bold text-primary mb-1">Hyper-Local Connectivity</h4><p class="text-gray-600">Connect with nearby consumers and businesses to reduce transport costs and ensure maximum freshness.</p></div>
                    </div>
                </div>
                <div class="lg:col-span-3 order-1 lg:order-2 flex justify-center lg:justify-start">
                     <div class="w-full lg:w-[90%] h-96 relative rounded-2xl overflow-hidden shadow-2xl group scroll-reveal">
                        <img id="parallax-img" src="https://placehold.co/800x600/B85C38/FFFFFF?text=Modern+Agricultural+Technology" alt="Modern Farming Tech" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.05]">
                        <div class="absolute inset-0 bg-primary-dark/20 group-hover:bg-primary-dark/10 transition-colors duration-500"></div>
                        <div class="absolute bottom-6 right-6 bg-white/95 backdrop-blur-sm p-4 rounded-xl shadow-xl border-t-4 border-accent transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500 ease-out">
                            <i data-lucide="quote" class="w-6 h-6 text-accent mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">"Technology has truly become the Kisan's Mitra."</p>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-primary/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center scroll-reveal">
            <span class="inline-block text-sm font-semibold tracking-wider uppercase text-accent mb-3">Start Your Journey</span>
            <h2 class="text-4xl lg:text-5xl font-extrabold text-primary leading-snug mb-6">Don't Wait For The Next <span class="text-accent">Harvest</span>. Start Today.</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-8">Whether you're looking to scale your farm or just buy healthier food, KisanMitra is the right step forward.</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="farmer/flogin.php" class="bg-accent text-white font-bold py-3 px-8 rounded-full text-lg shadow-2xl hover:bg-[#8e452a] transition duration-300 transform hover:scale-[1.05]">Farmer Login</a>
                <a href="contact.php" class="border-2 border-accent text-accent font-semibold py-3 px-8 rounded-full text-lg hover:bg-accent hover:text-black transition duration-300 transform hover:scale-[1.05]">Talk to an Expert</a>
            </div>
        </div>
    </section>

    <footer id="contact" class="bg-primary pt-16 pb-8 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="col-span-2 lg:col-span-1 scroll-reveal">
                    <a href="#home" class="flex items-center space-x-2 mb-4">
                        <span class="text-3xl text-secondary">🌱</span>
                        <span class="text-2xl font-extrabold text-white tracking-tight">Kisan<span class="text-accent">Mitra</span></span>
                    </a>
                    <p class="text-sm text-white/70 pr-8">Dedicated to the prosperity of the Indian farmer and the health of the Indian consumer. Cultivating trust through technology.</p>
                </div>
                <div class="scroll-reveal">
                    <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">Quick Links</h5>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#vision" class="text-white/80 hover:text-accent transition duration-200">Our Story</a></li>
                        <li><a href="#offerings" class="text-white/80 hover:text-accent transition duration-200">Platform Services</a></li>
                        <li><a href="#impact" class="text-white/80 hover:text-accent transition duration-200">Impact</a></li>
                        <li><a href="#benefits" class="text-white/80 hover:text-accent transition duration-200">Features</a></li>
                        <li><a href="contact.php" class="text-white/80 hover:text-accent transition duration-200">Help & Support</a></li>
                    </ul>
                </div>
                <div class="scroll-reveal">
                    <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">User Access</h5>
                    <ul class="space-y-3 text-sm">
                        <li><a href="farmer/fregister.php" class="text-white/80 hover:text-accent transition duration-200">Farmer Registration</a></li>
                        <li><a href="customer/clogin.php" class="text-white/80 hover:text-accent transition duration-200">Customer Login</a></li>
                        <li><a href="admin/alogin.php" class="text-white/80 hover:text-accent transition duration-200">Admin Panel</a></li>
                    </ul>
                </div>
                <div class="scroll-reveal">
                    <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">Get in Touch</h5>
                    <p class="text-sm text-white/80 mb-4">Pune, Maharashtra, India.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-white/80 hover:text-accent transition duration-200"><i data-lucide="twitter" class="w-5 h-5"></i></a>
                        <a href="#" class="text-white/80 hover:text-accent transition duration-200"><i data-lucide="linkedin" class="w-5 h-5"></i></a>
                        <a href="#" class="text-white/80 hover:text-accent transition duration-200"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/20 mt-12 pt-8 text-center">
                <p class="text-sm text-white/50">&copy; <span id="current-year"></span> KisanMitra. All rights reserved. Crafted with care in India.</p>
            </div>
        </div>
    </footer>

    <script>
    lucide.createIcons();
    document.getElementById('current-year').textContent = new Date().getFullYear();

    const header = document.getElementById('main-header');  
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.remove('navbar-hero');
            header.classList.add('navbar-scrolled');
        } else {
            header.classList.remove('navbar-scrolled');
            header.classList.add('navbar-hero');
        }
    });

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

    // Scroll Reveal Effect
    const observerOptions = { root: null, rootMargin: '0px', threshold: 0.1 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    document.querySelectorAll('.scroll-reveal').forEach(element => {
        observer.observe(element);
    });

    // Hero Section Slider
    const heroSlides = document.querySelectorAll('#home .hero-bg');
    let currentHero = 0;
    setInterval(() => {
        heroSlides[currentHero].classList.remove('opacity-100');
        heroSlides[currentHero].classList.add('opacity-0');
        currentHero = (currentHero + 1) % heroSlides.length;
        heroSlides[currentHero].classList.remove('opacity-0');
        heroSlides[currentHero].classList.add('opacity-100');
    }, 5000);

    // Parallax Effect
    window.addEventListener('scroll', function() {
        const parallaxImg = document.getElementById('parallax-img');
        if (parallaxImg) {
            const scrollPosition = window.scrollY;
            const elementTop = parallaxImg.getBoundingClientRect().top + window.scrollY;
            const offset = scrollPosition - elementTop;
            if (offset > -window.innerHeight && offset < parallaxImg.offsetHeight) {
                parallaxImg.style.transform = `translateY(${offset * 0.05}px) scale(1.02)`;
            }
        }
    });
    </script>
</body>
</html>
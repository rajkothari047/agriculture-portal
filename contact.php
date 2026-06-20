<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="assets/img/logo.png" />
    <title>Contact Us - KisanMitra</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': { DEFAULT: '#0A3D0A', 'dark': '#0A3D0A' },
                        'secondary': '#4F772D',
                        'accent': '#B85C38',
                        'bg-light': '#F9F7F3',
                        'text-dark': '#1E293B',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                }
            }
        }
    </script>

    <style>
        /* --- Navbar Styles --- */
        #main-header { transition: background-color 0.4s ease, box-shadow 0.4s ease, padding 0.3s ease; padding: 0.75rem 0; }
        #main-header.bg-scrolled { background-color: #0A3D0A; box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
        .navbar-hero { background-color: transparent; }

        /* Dropdown */
        .dropdown-hover-group { position: relative; }
        .dropdown-menu-kisan { 
            opacity: 0; 
            visibility: hidden; 
            transform: translateY(-10px); 
            transition: opacity 0.35s ease, transform 0.35s ease, visibility 0.35s; 
            z-index: 50; 
        }
        .dropdown-hover-group:hover .dropdown-menu-kisan,
        .dropdown-hover-group:focus-within .dropdown-menu-kisan { 
            opacity: 1; 
            visibility: visible; 
            transform: translateY(0); 
        }

        /* Scroll Reveal */
        .scroll-reveal { opacity: 0; transform: translateY(20px); transition: opacity 0.6s ease-out, transform 0.6s ease-out; }
        .scroll-reveal.visible { opacity: 1; transform: translateY(0); }

        /* Contact Section */
        .card-contact { box-shadow: 0 15px 40px rgba(10, 61, 10, 0.15); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-contact:hover { transform: translateY(-3px); box-shadow: 0 20px 50px rgba(10, 61, 10, 0.2); }
        .form-control-kisan { transition: border-color 0.3s ease, box-shadow 0.3s ease; border-color: #D1D5DB; }
        .form-control-kisan:focus { outline: none; border-color: #B85C38; box-shadow: 0 0 0 3px rgba(184,92,56,0.3); }
        .contact-main-section { background: linear-gradient(135deg, #F9F7F3 0%, #E0EEDC 100%); }
        
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
            background: linear-gradient(135deg, #4F772D 0%, #0A3D0A 100%);
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
            color: #0A3D0A;
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
        
        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .btn-loading {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="bg-bg-light">

    <!-- HEADER -->
    <header id="main-header" class="fixed top-0 left-0 right-0 z-50 navbar-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-2 text-white">
                        <span class="text-3xl text-secondary">🌱</span>
                        <span class="text-2xl font-extrabold tracking-tight">Kisan<span class="text-accent">Mitra</span></span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex lg:space-x-10 items-center font-semibold">
                    <a href="index.php#benefits" class="text-white hover:text-accent transition duration-300">
                        <i data-lucide="mail" class="w-5 h-5 inline-block mr-1"></i> Services
                    </a>

                    <!-- Register Dropdown -->
                    <div class="relative dropdown-hover-group group">
                        <button class="text-white hover:text-accent flex items-center whitespace-nowrap gap-1 transition duration-300">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                            Register
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                        <div class="absolute mt-3 w-48 rounded-lg shadow-xl bg-white dropdown-menu-kisan">
                            <a href="farmer/fregister.php" class="block px-4 py-3 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent">
                                <i data-lucide="user" class="w-4 h-4 inline-block mr-2"></i> Farmer Register
                            </a>
                            <a href="customer/cregister.php" class="block px-4 py-3 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent">
                                <i data-lucide="users" class="w-4 h-4 inline-block mr-2"></i> Customer Register
                            </a>
                        </div>
                    </div>

                    <!-- Login Dropdown -->
                    <div class="relative dropdown-hover-group group">
                        <button class="text-white hover:text-accent flex items-center transition duration-300">
                            <i data-lucide="log-in" class="w-5 h-5 mr-1"></i> Login
                            <i data-lucide="chevron-down" class="w-4 h-4 ml-1"></i>
                        </button>
                        <div class="absolute mt-3 w-48 rounded-lg shadow-xl bg-white dropdown-menu-kisan">
                            <a href="farmer/flogin.php" class="block px-4 py-3 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent">
                                <i data-lucide="user" class="w-4 h-4 inline-block mr-2"></i> Farmer Login
                            </a>
                            <a href="customer/clogin.php" class="block px-4 py-3 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent">
                                <i data-lucide="users" class="w-4 h-4 inline-block mr-2"></i> Customer Login
                            </a>
                            <a href="admin/alogin.php" class="block px-4 py-3 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent">
                                <i data-lucide="shield" class="w-4 h-4 inline-block mr-2"></i> Admin Panel
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Desktop Home Button -->
                <div class="hidden lg:flex items-center">
                    <a href="index.php" class="bg-accent text-white font-bold py-2 px-6 rounded-full shadow-lg shadow-accent/50 hover:bg-[#8e452a] transition duration-300 transform hover:scale-[1.05]">
                        <i data-lucide="home" class="w-5 h-5 mr-1 inline-block"></i> Home
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-white focus:outline-none">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div id="mobile-menu-drawer" class="fixed inset-0 bg-primary/95 backdrop-blur-sm z-50 transform -translate-y-full transition-transform duration-500 ease-in-out lg:hidden">
            <div class="flex justify-end p-6">
                <button id="close-mobile-menu-btn" class="text-white focus:outline-none">
                    <i data-lucide="x" class="w-8 h-8"></i>
                </button>
            </div>
            <nav class="flex flex-col items-center space-y-8 text-2xl font-bold pt-10">
                <a href="index.php" class="text-accent hover:text-white transition duration-300">Home</a>
                <a href="contact.php" class="text-white hover:text-accent transition duration-300">Contact</a>
                <a href="farmer/fregister.php" class="text-white hover:text-accent transition duration-300">Farmer Register</a>
                <a href="customer/cregister.php" class="text-white hover:text-accent transition duration-300">Customer Register</a>
                <a href="farmer/flogin.php" class="text-accent hover:text-white transition duration-300">Farmer Login</a>
                <a href="customer/clogin.php" class="text-accent hover:text-white transition duration-300">Customer Login</a>
                <a href="admin/alogin.php" class="text-accent hover:text-white transition duration-300">Admin Login</a>
            </nav>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="pt-24 pb-20 relative text-text-dark" 
      style="background-image: url('assets/img/log5.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
    
    <div class="absolute inset-0 bg-black/30 z-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 scroll-reveal">
            <p class="text-[#B85C38] text-lg font-semibold uppercase tracking-wider">Need Assistance? Your KisanMitra is Here.</p>
            <h1 class="text-5xl font-serif font-extrabold text-white mt-2">Get Timely Support from Our Team</h1>
            <p class="mt-4 text-lg text-gray-200 max-w-3xl mx-auto">
                We value every query. Use the form below for fast, structured support regarding your account, orders, or technology.
            </p>
        </div>

        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Left Info Cards -->
            <div class="lg:col-span-1 space-y-8 scroll-reveal">
                <div class="bg-white/90 p-8 rounded-2xl card-contact border-t-4 border-accent">
                    <i data-lucide="map-pin" class="w-8 h-8 text-accent mb-4"></i>
                    <h3 class="text-2xl font-bold text-primary mb-2">Our Head Office</h3>
                    <p class="text-gray-700">
                        KisanMitra Technology Pvt. Ltd.<br>
                        Krishi Bhavan, Near IT Park,<br>
                        Pune, Maharashtra - 411045, India.
                    </p>
                    <a href="#" class="mt-4 inline-block text-secondary hover:text-accent font-semibold">Get Directions</a>
                </div>

                <div class="bg-white/90 p-8 rounded-2xl card-contact border-t-4 border-secondary">
                    <i data-lucide="phone-call" class="w-8 h-8 text-secondary mb-4"></i>
                    <h3 class="text-2xl font-bold text-primary mb-2">Farmer Helpline</h3>
                    <p class="text-gray-700">Speak to a support agent for urgent crop or platform issues.</p>
                    <p class="text-xl font-bold text-accent mt-2">+91 8000 567 890</p>
                    <small class="text-gray-500">Mon - Sat, 9 AM - 6 PM IST</small>
                </div>

                <div class="bg-white/90 p-8 rounded-2xl card-contact border-t-4 border-primary">
                    <i data-lucide="inbox" class="w-8 h-8 text-primary mb-4"></i>
                    <h3 class="text-2xl font-bold text-primary mb-2">General Inquiries</h3>
                    <p class="text-gray-700">For partnerships, media, or other general questions.</p>
                    <p class="text-lg font-semibold mt-2 text-primary">support@kisanmitra.in</p>
                </div>
            </div>

<!-- Right Contact Form -->
<div class="lg:col-span-2 bg-white/90 p-8 lg:p-12 rounded-2xl card-contact scroll-reveal" style="transition-delay: 0.1s;">
    <h2 class="text-3xl font-bold text-primary mb-8 border-b pb-4 border-gray-100">Contact Inquiry Form</h2>
    <form id="contactForm" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-text-dark mb-2">Full Name</label>
                <input class="form-control-kisan w-full p-3 border-2 rounded-lg bg-bg-light" type="text" name="user_name" id="user_name" placeholder="Your Full Name" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-text-dark mb-2">Email Id</label>
                <input class="form-control-kisan w-full p-3 border-2 rounded-lg bg-bg-light" type="email" name="user_email" id="user_email" placeholder="your.email@example.com" required>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-text-dark mb-2">Mobile Number</label>
                <input class="form-control-kisan w-full p-3 border-2 rounded-lg bg-bg-light" type="tel" name="user_mobile" id="user_mobile" placeholder="9876543210" pattern="[6-9]{1}[0-9]{9}" maxlength="10" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-text-dark mb-2">Inquiry Subject</label>
                <input class="form-control-kisan w-full p-3 border-2 rounded-lg bg-bg-light" type="text" name="user_subject" id="user_subject" placeholder="e.g., Technical Support" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-text-dark mb-2">Address / City</label>
            <input class="form-control-kisan w-full p-3 border-2 rounded-lg bg-bg-light" type="text" name="user_address" id="user_address" placeholder="Your City, State, Pincode" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-text-dark mb-2">Detailed Message</label>
            <textarea class="form-control-kisan w-full p-3 border-2 rounded-lg bg-bg-light resize-y min-h-[140px]" name="user_message" id="user_message" placeholder="Describe your issue..." required></textarea>
        </div>

        <button type="button" id="submitBtn" class="w-full py-4 bg-accent text-white font-bold rounded-xl text-lg shadow-xl shadow-accent/40 hover:bg-[#8e452a] transition duration-300 transform hover:scale-[1.005] flex items-center justify-center">
            <i data-lucide="send" class="w-5 h-5 inline-block mr-2"></i> Submit Inquiry
        </button>
    </form>
</div>
        </div>
    </div>
</main>

    <!-- FOOTER -->
    <footer class="bg-primary pt-16 pb-8 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="col-span-2 lg:col-span-1 scroll-reveal">
                    <a href="#home" class="flex items-center space-x-2 mb-4">
                        <span class="text-3xl text-secondary">🌱</span>
                        <span class="text-2xl font-extrabold text-white tracking-tight">Kisan<span class="text-accent">Mitra</span></span>
                    </a>
                    <p class="text-sm text-white/70 pr-8">
                        Dedicated to the prosperity of the Indian farmer and the health of the Indian consumer. Cultivating trust through technology.
                    </p>
                </div>
                <div class="scroll-reveal">
                    <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">Quick Links</h5>
                    <ul class="space-y-3 text-sm">
                        <li><a href="index.php#vision" class="text-white/80 hover:text-accent transition duration-300">Our Story</a></li>
                        <li><a href="index.php#offerings" class="text-white/80 hover:text-accent transition duration-300">Platform Services</a></li>
                        <li><a href="index.php#impact" class="text-white/80 hover:text-accent transition duration-300">Impact</a></li>
                        <li><a href="index.php#benefits" class="text-white/80 hover:text-accent transition duration-300">Features</a></li>
                        <li><a href="contact.php" class="text-white/80 hover:text-accent transition duration-300">Help & Support</a></li>
                    </ul>
                </div>
                <div class="scroll-reveal">
                    <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">User Access</h5>
                    <ul class="space-y-3 text-sm">
                        <li><a href="farmer/fregister.php" class="text-white/80 hover:text-accent transition duration-300">Farmer Registration</a></li>
                        <li><a href="customer/clogin.php" class="text-white/80 hover:text-accent transition duration-300">Customer Login</a></li>
                        <li><a href="admin/alogin.php" class="text-white/80 hover:text-accent transition duration-300">Admin Panel</a></li>
                    </ul>
                </div>
                <div class="scroll-reveal">
                    <h5 class="text-lg font-bold mb-4 border-b border-accent/50 pb-2">Get in Touch</h5>
                    <p class="text-sm text-white/80 mb-4">Pune, Maharashtra, India</p>
                    <p class="text-sm text-white/80 mb-4">support@kisanmitra.in</p>
                    <p class="text-sm text-white/80">+91 8000 567 890</p>
                </div>
            </div>
            <div class="text-center text-sm text-white/60 mt-8">&copy; 2025 KisanMitra. All Rights Reserved.</div>
        </div>
    </footer>

    <!-- Success Popup Modal -->
    <div id="successPopup" class="popup-overlay">
        <div class="popup-container">
            <button class="popup-close" onclick="closePopup()">&times;</button>
            <div class="popup-content">
                <div class="popup-icon">
                    <i data-lucide="check" class="w-10 h-10 text-white"></i>
                </div>
                <h3 class="popup-title">Thank You!</h3>
                <p class="popup-message">
                    🌾 Your inquiry has been successfully submitted!<br>
                    Our KisanMitra team will get back to you within 24 hours.<br>
                    <span style="font-size: 14px; display: block; margin-top: 12px;">📞 Need urgent help? Call: +91 8000 567 890</span>
                </p>
                <button class="popup-button" onclick="closePopup()">
                    <i data-lucide="thumbs-up" class="w-5 h-5"></i> Great, Thanks!
                </button>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        lucide.createIcons();

        // Navbar scroll color change
        window.addEventListener('scroll', () => {
            const header = document.getElementById('main-header');
            if(window.scrollY > 60) header.classList.add('bg-scrolled'); 
            else header.classList.remove('bg-scrolled');
        });

        // Mobile Menu toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-mobile-menu-btn');
        const drawer = document.getElementById('mobile-menu-drawer');

        if(menuBtn) menuBtn.addEventListener('click', () => drawer.classList.remove('-translate-y-full'));
        if(closeBtn) closeBtn.addEventListener('click', () => drawer.classList.add('-translate-y-full'));

        // Scroll Reveal
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) { 
                    entry.target.classList.add('visible'); 
                    obs.unobserve(entry.target); 
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));

        // Popup Functions
        function showPopup() {
            const popup = document.getElementById('successPopup');
            popup.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closePopup() {
            const popup = document.getElementById('successPopup');
            popup.classList.remove('active');
            document.body.style.overflow = '';
            // Reinitialize Lucide icons in popup after close (for next time)
            lucide.createIcons();
        }

        // Close popup when clicking outside
        const popupElement = document.getElementById('successPopup');
        if(popupElement) {
            popupElement.addEventListener('click', function(e) {
                if (e.target === this) {
                    closePopup();
                }
            });
        }

        // Form submission handler
const submitBtn = document.getElementById('submitBtn');
const contactForm = document.getElementById('contactForm');

if (submitBtn) {
    submitBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        // Get all form values
        const formData = new FormData();
        formData.append('user_name', document.getElementById('user_name').value);
        formData.append('user_email', document.getElementById('user_email').value);
        formData.append('user_mobile', document.getElementById('user_mobile').value);
        formData.append('user_subject', document.getElementById('user_subject').value);
        formData.append('user_address', document.getElementById('user_address').value);
        formData.append('user_message', document.getElementById('user_message').value);
        
        // Validate form fields
        let isValid = true;
        const requiredFields = ['user_name', 'user_email', 'user_mobile', 'user_subject', 'user_address', 'user_message'];
        
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                element.classList.add('border-red-500');
                isValid = false;
            } else {
                element.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            alert('Please fill all required fields.');
            return;
        }
        
        // Validate email
        const email = document.getElementById('user_email').value;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert('Please enter a valid email address.');
            document.getElementById('user_email').classList.add('border-red-500');
            return;
        }
        
        // Validate mobile (10 digits)
        const mobile = document.getElementById('user_mobile').value;
        const mobilePattern = /^[6-9][0-9]{9}$/;
        if (!mobilePattern.test(mobile)) {
            alert('Please enter a valid 10-digit mobile number starting with 6,7,8, or 9.');
            document.getElementById('user_mobile').classList.add('border-red-500');
            return;
        }
        
        // Show loading state
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Submitting...';
        submitBtn.disabled = true;
        submitBtn.classList.add('btn-loading');
        
        try {
            // Send AJAX request
            const response = await fetch('contact-script.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show success popup
                showPopup();
                // Reset form
                contactForm.reset();
            } else {
                // Show error message
                alert(data.message || 'Something went wrong. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error. Please check your connection and try again.');
        } finally {
            // Reset button state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-loading');
            // Reinitialize Lucide icons in button
            lucide.createIcons();
        }
    });
}
    </script>
</body>
</html>
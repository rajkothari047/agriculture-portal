<?php 
session_start(); 
require('../sql.php');

date_default_timezone_set('Asia/Kolkata');

if(!isset($_SESSION['customer_login_user'])){
    header("location: ../customer/clogin.php");
    exit();
}
$user = $_SESSION['customer_login_user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Secure Verification | KisanMitra Customer</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-primary-dark: #B85C38;
            --color-accent-terracotta: #D47B4A;
            --color-secondary-green: #4F772D;
            --color-bg-light: #FEFCF8;
            --color-text-dark: #2C3E2F;
            --color-text-light: #6B7A6D;
            --color-shadow: rgba(0, 0, 0, 0.08);
            --color-border: #E8EDE4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #FEFCF8 0%, #FDF9F2 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Market Scene Background */
        .market-scene {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 280px;
            z-index: 0;
            overflow: hidden;
        }

        /* Rolling Hills */
        .hill {
            position: absolute;
            bottom: 0;
            background: linear-gradient(135deg, #E8F0E5 0%, #D4E2C9 100%);
            border-radius: 50% 50% 0 0;
            box-shadow: inset 0 8px 12px rgba(0,0,0,0.05);
        }

        .hill-1 {
            width: 100%;
            height: 180px;
            left: -20%;
            bottom: 0;
            border-radius: 50% 50% 0 0;
        }

        .hill-2 {
            width: 85%;
            height: 140px;
            right: -15%;
            bottom: 0;
            background: linear-gradient(135deg, #DDEBD2 0%, #C9DDBD 100%);
        }

        .hill-3 {
            width: 70%;
            height: 110px;
            left: 40%;
            bottom: 0;
            background: linear-gradient(135deg, #E2EDD7 0%, #CFE2C3 100%);
        }

        /* Animated Clouds */
        .cloud {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 100px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            animation: floatCloud 40s linear infinite;
        }

        .cloud::before,
        .cloud::after {
            content: '';
            position: absolute;
            background: inherit;
            border-radius: 50%;
        }

        .cloud::before {
            width: 70px;
            height: 70px;
            top: -35px;
            left: 15px;
        }

        .cloud::after {
            width: 55px;
            height: 55px;
            top: -25px;
            left: 65px;
        }

        .cloud-1 {
            width: 120px;
            height: 55px;
            top: 15%;
            left: -150px;
            animation-duration: 55s;
            animation-delay: 0s;
        }

        .cloud-2 {
            width: 100px;
            height: 48px;
            top: 25%;
            left: -120px;
            animation-duration: 48s;
            animation-delay: 5s;
        }

        .cloud-3 {
            width: 140px;
            height: 62px;
            top: 8%;
            right: -170px;
            left: auto;
            animation-duration: 62s;
            animation-delay: 2s;
        }

        @keyframes floatCloud {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(100vw);
            }
        }

        /* Floating Shopping Icons */
        .floating-icon {
            position: absolute;
            opacity: 0.15;
            pointer-events: none;
            animation: floatUp 25s infinite linear;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.15;
            }
            90% {
                opacity: 0.15;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Main Container */
        .verification-container {
            width: 100%;
            max-width: 550px;
            margin: 2rem;
            position: relative;
            z-index: 10;
        }

        /* Elevated Card */
        .verification-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(0px);
            border-radius: 48px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(184, 92, 56, 0.1);
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            position: relative;
            overflow: hidden;
        }

        .verification-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-accent-terracotta), var(--color-secondary-green), var(--color-primary-dark));
        }

        .verification-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.2);
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .logo-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #FEF3E2, #FCEAD8);
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px -5px rgba(184, 92, 56, 0.2);
        }

        .logo-icon svg {
            width: 32px;
            height: 32px;
            stroke: var(--color-accent-terracotta);
            stroke-width: 1.5;
        }

        .logo-text {
            text-align: left;
        }

        .logo-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--color-primary-dark), var(--color-secondary-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .logo-text p {
            font-size: 0.7rem;
            color: var(--color-text-light);
            letter-spacing: 1px;
        }

        /* Header */
        .verification-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .verification-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-text-dark);
            margin-bottom: 0.75rem;
        }

        .verification-header p {
            color: var(--color-text-light);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .customer-greeting {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #F5F0E8;
            padding: 0.5rem 1rem;
            border-radius: 100px;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: var(--color-accent-terracotta);
            font-weight: 500;
        }

        .customer-greeting svg {
            width: 18px;
            height: 18px;
        }

        /* OTP Input Fields - Elevated Design */
        .otp-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2.5rem 0;
        }

        .otp-input {
            width: 75px;
            height: 85px;
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            font-family: 'Inter', monospace;
            border: 2px solid var(--color-border);
            border-radius: 24px;
            background: white;
            color: var(--color-text-dark);
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        .otp-input:focus {
            outline: none;
            border-color: var(--color-accent-terracotta);
            box-shadow: 0 8px 25px -8px rgba(184, 92, 56, 0.3);
            transform: translateY(-3px);
        }

        .otp-input:disabled {
            background: #F9F9F9;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Timer Card */
        .timer-card {
            background: #F8F6F2;
            border-radius: 20px;
            padding: 1rem;
            margin: 1.5rem 0;
            text-align: center;
            border: 1px solid var(--color-border);
        }

        .timer-display {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--color-text-dark);
        }

        .timer-display svg {
            width: 20px;
            height: 20px;
            stroke: var(--color-accent-terracotta);
        }

        .timer-value {
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--color-accent-terracotta);
            font-family: monospace;
            letter-spacing: 1px;
        }

        /* Buttons */
        .btn-verify {
            width: 100%;
            background: linear-gradient(135deg, var(--color-primary-dark), var(--color-accent-terracotta));
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 60px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px -5px rgba(184, 92, 56, 0.3);
        }

        .btn-verify::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }

        .btn-verify:hover::after {
            width: 200px;
            height: 200px;
        }

        .btn-verify:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -8px rgba(184, 92, 56, 0.4);
        }

        .btn-verify:disabled {
            opacity: 0.6;
            transform: none;
        }

        .btn-resend {
            width: 100%;
            background: transparent;
            color: var(--color-accent-terracotta);
            border: 2px solid var(--color-accent-terracotta);
            padding: 0.85rem;
            border-radius: 60px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-resend:hover {
            background: var(--color-accent-terracotta);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -5px rgba(184, 92, 56, 0.3);
        }

        .btn-resend:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Message Boxes */
        .message-box {
            padding: 1rem 1.2rem;
            border-radius: 20px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-success {
            background: linear-gradient(135deg, #E8F5E8, #DCF0DC);
            color: #2C5E2A;
            border-left: 4px solid #4CAF50;
        }

        .message-error {
            background: linear-gradient(135deg, #FEF0F0, #FDE8E8);
            color: #C44536;
            border-left: 4px solid #EF4444;
        }

        .message-warning {
            background: linear-gradient(135deg, #FFF8E7, #FFF3E0);
            color: #B85C38;
            border-left: 4px solid #F59E0B;
        }

        .message-box svg {
            flex-shrink: 0;
        }

        .cooldown-timer {
            text-align: center;
            font-size: 0.75rem;
            color: var(--color-text-light);
            margin-top: 1rem;
            padding: 0.5rem;
        }

        .help-section {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
        }

        .help-text {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            color: var(--color-text-light);
        }

        .help-text a {
            color: var(--color-accent-terracotta);
            text-decoration: none;
            font-weight: 500;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .verification-card {
                padding: 2rem 1.5rem;
            }
            
            .otp-input {
                width: 55px;
                height: 65px;
                font-size: 1.6rem;
            }
            
            .otp-container {
                gap: 0.6rem;
            }
            
            .verification-header h1 {
                font-size: 1.6rem;
            }
            
            .logo-text h2 {
                font-size: 1.4rem;
            }
            
            .logo-icon {
                width: 48px;
                height: 48px;
            }
            
            .logo-icon svg {
                width: 28px;
                height: 28px;
            }
        }

        @media (max-width: 480px) {
            .otp-input {
                width: 48px;
                height: 58px;
                font-size: 1.4rem;
            }
            
            .verification-card {
                padding: 1.75rem 1.25rem;
            }
        }
    </style>
</head>
<body>

<!-- Animated Market Scene Background -->
<div class="market-scene">
    <div class="hill hill-1"></div>
    <div class="hill hill-2"></div>
    <div class="hill hill-3"></div>
    
    <div class="cloud cloud-1"></div>
    <div class="cloud cloud-2"></div>
    <div class="cloud cloud-3"></div>
</div>

<!-- Floating Shopping Icons -->
<div class="floating-icon" style="left: 5%; animation-duration: 32s; animation-delay: 0s;">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#B85C38" stroke-width="1.2">
        <circle cx="9" cy="21" r="1"/>
        <circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
    </svg>
</div>
<div class="floating-icon" style="right: 10%; animation-duration: 28s; animation-delay: 5s;">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#4F772D" stroke-width="1.2">
        <path d="M20 7h-4.5L15 4H9L8.5 7H4v2h16V7z"/>
        <rect x="4" y="9" width="16" height="10" rx="1"/>
        <circle cx="8" cy="15" r="1"/>
        <circle cx="16" cy="15" r="1"/>
    </svg>
</div>
<div class="floating-icon" style="left: 20%; animation-duration: 35s; animation-delay: 12s;">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0A3D0A" stroke-width="1.2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
        <path d="M12 3v2"/>
        <path d="M12 9v2"/>
    </svg>
</div>
<div class="floating-icon" style="right: 25%; animation-duration: 40s; animation-delay: 8s;">
    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#B85C38" stroke-width="1.2">
        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
    </svg>
</div>

<div class="verification-container">
    <div class="verification-card">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo-wrapper">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                </div>
                <div class="logo-text">
                    <h2>KisanMitra</h2>
                    <p>Fresh from Farm to Fork</p>
                </div>
            </div>
        </div>

        <!-- Verification Header -->
        <div class="verification-header">
            <h1>Secure Checkout</h1>
            <p>Enter the 5-digit verification code sent to your registered email address to complete your login</p>
            <div class="customer-greeting">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>Customer Account Verification</span>
            </div>
        </div>

        <!-- Message Boxes -->
        <div id="popup" class="message-box message-success hidden">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>Verification code sent to your email! Check your inbox or spam folder.</span>
        </div>

        <div id="invalid" class="message-box message-error hidden">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>Invalid verification code</span>
        </div>

        <div id="expired" class="message-box message-warning hidden">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>Code has expired. Click "Get New Code" to receive a fresh one.</span>
        </div>

        <div id="locked" class="message-box message-error hidden">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span>Too many attempts. Account locked for 15 minutes.</span>
        </div>

        <form onsubmit="return false;">
            <input type="hidden" id="otp" name="customer_otp" value="">
            
            <!-- OTP Input Fields -->
            <div class="otp-container">
                <input type="text" class="otp-input" id="otp-input-0" maxlength="1" pattern="\d*" inputmode="numeric" required autofocus>
                <input type="text" class="otp-input" id="otp-input-1" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" class="otp-input" id="otp-input-2" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" class="otp-input" id="otp-input-3" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" class="otp-input" id="otp-input-4" maxlength="1" pattern="\d*" inputmode="numeric" required>
            </div>

            <!-- Timer Card -->
            <div class="timer-card">
                <div class="timer-display">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Code expires in</span>
                    <span class="timer-value">25:00</span>
                </div>
            </div>

            <!-- Verify Button -->
            <button onclick="submit_otp()" id="verifyBtn" class="btn-verify">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 7l6 5-6 5M21 12H9"/>
                    <path d="M3 5h9M3 12h3M3 19h9"/>
                </svg>
                Verify & Access Account
            </button>

            <!-- Resend Button -->
            <button onclick="send_otp()" id="resendBtn" class="btn-resend">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                Request New Code
            </button>
            
            <div id="cooldown" class="cooldown-timer hidden"></div>
        </form>

        <!-- Help Section -->
        <div class="help-section">
            <div class="help-text">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 16v-4M12 8h.01"></path>
                </svg>
                Didn't receive the code? 
                <a href="#" onclick="send_otp(); return false;">Resend</a>
                &nbsp;•&nbsp;
                <a href="../contact.php">Contact Support</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script>
    lucide.createIcons();
    
    let lastOTPSent = 0;
    let cooldownInterval = null;
    let isSubmitting = false;
    let timerInterval = null;
    
    $(document).ready(function() { 
        setTimeout(function() {
            send_otp();
        }, 500);
        startTimer(25);
    });

    function startTimer(minutes) {
        let timeLeft = minutes * 60;
        if(timerInterval) clearInterval(timerInterval);
        
        timerInterval = setInterval(() => {
            timeLeft--;
            if(timeLeft <= 0) {
                clearInterval(timerInterval);
                $('.timer-value').text('00:00');
                showMessage('expired', 'Code has expired. Please request a new one.');
            } else {
                const mins = Math.floor(timeLeft / 60);
                const secs = timeLeft % 60;
                $('.timer-value').text(`${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`);
            }
        }, 1000);
    }

    function send_otp() {
        if($('#resendBtn').prop('disabled')) return;
        
        const now = Date.now();
        if (lastOTPSent && (now - lastOTPSent) < 30000) {
            const remaining = Math.ceil((30000 - (now - lastOTPSent)) / 1000);
            showMessage('warning', `Please wait ${remaining} seconds before requesting a new code`);
            return;
        }
        
        hideAllMessages();
        $('#resendBtn').prop('disabled', true);
        $('#resendBtn').html('<span class="loading"></span> Sending Code...');
        
        $.ajax({
            url: "csend_otp.php",
            type: "POST",
            success: function(response) {
                if (response.trim() == 'success') {
                    lastOTPSent = Date.now();
                    showMessage('success', '✅ Verification code sent! Check your email inbox or spam folder.');
                    startCooldownTimer();
                    clearOTPInputs();
                    startTimer(25);
                } else {
                    showMessage('error', '❌ Failed to send code. Please try again.');
                }
            },
            error: function() {
                showMessage('error', '❌ Network error. Please check your connection.');
            },
            complete: function() {
                $('#resendBtn').prop('disabled', false);
                $('#resendBtn').html('<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Request New Code');
            }
        });
    }

    function submit_otp() {
        if(isSubmitting) return;
        
        const inputs = document.querySelectorAll('.otp-input');
        const otpValue = Array.from(inputs).map(input => input.value).join('');
        
        if(otpValue.length !== 5) {
            showMessage('error', 'Please enter complete 5-digit verification code');
            return;
        }
        
        $('#otp').val(otpValue);
        hideAllMessages();
        $('#verifyBtn').prop('disabled', true);
        isSubmitting = true;
        
        const verifyText = $('#verifyBtn').html();
        $('#verifyBtn').html('<span class="loading"></span> Verifying...');
        
        $.ajax({
            url: 'ccheck_otp.php',
            type: 'post',
            data: 'otp=' + otpValue,
            success: function(result) {
                if (result.trim() == 'yes') {
                    showMessage('success', '✅ Verification successful! Redirecting to your dashboard...');
                    setTimeout(function() {
                        window.location = 'cprofile.php';
                    }, 1000);
                } else if (result.trim() == 'expired') {
                    showMessage('expired', '⚠️ Code expired. Click "Get New Code" to receive a fresh code.');
                    clearOTPInputs();
                } else if (result.trim() == 'locked') {
                    showMessage('locked', '🔒 Too many attempts. Account temporarily locked for 15 minutes.');
                    disableOTPInputs(true);
                } else {
                    $.ajax({
                        url: 'cget_attempts.php',
                        type: 'get',
                        async: false,
                        success: function(attempts) {
                            let remaining = parseInt(attempts);
                            if(remaining > 0) {
                                showMessage('error', `❌ Invalid verification code! (${remaining} attempt${remaining !== 1 ? 's' : ''} remaining)`);
                            }
                        }
                    });
                    clearOTPInputs();
                }
            },
            error: function() {
                showMessage('error', '❌ Network error. Please try again.');
            },
            complete: function() {
                $('#verifyBtn').prop('disabled', false);
                $('#verifyBtn').html(verifyText);
                isSubmitting = false;
            }
        });
    }
    
    function showMessage(type, message) {
        hideAllMessages();
        const elements = {
            success: '#popup',
            error: '#invalid',
            expired: '#expired',
            locked: '#locked',
            warning: '#invalid'
        };
        const element = elements[type];
        if(element) {
            $(element).find('span').html(message);
            $(element).removeClass('hidden').show();
            setTimeout(() => $(element).fadeOut(), 5000);
        }
    }
    
    function hideAllMessages() {
        $('#popup, #invalid, #expired, #locked').addClass('hidden').hide();
    }
    
    function clearOTPInputs() {
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach(i => i.value = '');
        if(inputs[0] && !inputs[0].disabled) inputs[0].focus();
    }
    
    function disableOTPInputs(disable) {
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach(i => i.disabled = disable);
        if(disable) {
            $('#verifyBtn, #resendBtn').prop('disabled', true);
            setTimeout(() => {
                disableOTPInputs(false);
                $('#verifyBtn, #resendBtn').prop('disabled', false);
                hideAllMessages();
                send_otp();
            }, 900000);
        }
    }
    
    function startCooldownTimer() {
        let timeLeft = 30;
        $('#cooldown').removeClass('hidden').show();
        if(cooldownInterval) clearInterval(cooldownInterval);
        
        cooldownInterval = setInterval(() => {
            timeLeft--;
            if(timeLeft <= 0) {
                clearInterval(cooldownInterval);
                $('#cooldown').addClass('hidden').hide();
            } else {
                $('#cooldown').text(`⏱️ You can request a new code in ${timeLeft} seconds`);
            }
        }, 1000);
    }
    
    // OTP input navigation
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            const allFilled = Array.from(inputs).every(inp => inp.value.length === 1);
            if (allFilled) submit_otp();
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
            if (e.key === 'Enter') submit_otp();
        });
        input.addEventListener('keypress', (e) => {
            if (!/[0-9]/.test(e.key)) e.preventDefault();
        });
    });
</script>
</body>
</html>
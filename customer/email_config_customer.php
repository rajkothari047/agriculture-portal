<?php
// email_config_customer.php - Professional email template for customers
require __DIR__ . '/../src/PHPMailer.php';
require __DIR__ . '/../src/SMTP.php';
require __DIR__ . '/../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendOTPToCustomerEmail($recipient_email, $recipient_name, $otp_code) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kisanmitra.noreply@gmail.com';
        $mail->Password   = 'jsvp jszp kspq chrj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Disable SSL verification for testing
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Recipients
        $mail->setFrom('kisanmitra.noreply@gmail.com', 'KisanMitra - Customer Support');
        $mail->addAddress($recipient_email, $recipient_name);
        
        // Professional Email Content
        $mail->isHTML(true);
        $mail->Subject = '🔐 Your KisanMitra Customer Verification Code';
        $mail->Body    = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>KisanMitra OTP Verification</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                    background-color: #f4f7f3;
                }
                .email-container {
                    max-width: 550px;
                    margin: 30px auto;
                    background: #ffffff;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                    border: 1px solid #e0e6df;
                }
                .email-header {
                    background: linear-gradient(135deg, #B85C38 0%, #D47B4A 100%);
                    padding: 25px 30px;
                    text-align: center;
                }
                .email-header h1 {
                    margin: 0;
                    font-size: 28px;
                    color: #ffffff;
                    letter-spacing: -0.5px;
                }
                .email-header p {
                    margin: 8px 0 0;
                    color: #FFE5D9;
                    font-size: 14px;
                }
                .email-body {
                    padding: 35px 30px;
                    background: #ffffff;
                }
                .greeting {
                    font-size: 18px;
                    color: #2d3748;
                    margin-bottom: 20px;
                }
                .greeting strong {
                    color: #B85C38;
                }
                .otp-box {
                    background: #FFF8F0;
                    border: 2px dashed #B85C38;
                    border-radius: 12px;
                    padding: 25px;
                    text-align: center;
                    margin: 25px 0;
                }
                .otp-code {
                    font-size: 42px;
                    font-weight: bold;
                    letter-spacing: 8px;
                    color: #B85C38;
                    font-family: 'Courier New', monospace;
                    background: #ffffff;
                    display: inline-block;
                    padding: 10px 25px;
                    border-radius: 10px;
                    border: 1px solid #F0E2D4;
                }
                .expiry-note {
                    background: #FFF8E1;
                    border-left: 4px solid #B85C38;
                    padding: 12px 18px;
                    margin: 20px 0;
                    border-radius: 8px;
                    font-size: 14px;
                    color: #856404;
                }
                .message-text {
                    color: #4a5568;
                    margin: 20px 0;
                    font-size: 15px;
                }
                .security-tips {
                    background: #f8f9fa;
                    padding: 15px 20px;
                    border-radius: 10px;
                    margin: 20px 0;
                    font-size: 13px;
                    color: #6c757d;
                }
                .security-tips ul {
                    margin: 8px 0 0 20px;
                    padding: 0;
                }
                .security-tips li {
                    margin: 5px 0;
                }
                .email-footer {
                    background: #f8faf7;
                    padding: 20px 30px;
                    text-align: center;
                    border-top: 1px solid #e2e8e0;
                    font-size: 12px;
                    color: #8ba888;
                }
                .footer-links {
                    margin-top: 15px;
                }
                .footer-links a {
                    color: #6c9e6b;
                    text-decoration: none;
                    margin: 0 8px;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <h1>🛍️ KisanMitra</h1>
                    <p>Fresh from Farm to Fork</p>
                </div>
                
                <div class='email-body'>
                    <div class='greeting'>
                        <strong>Hello " . htmlspecialchars($recipient_name) . "!</strong>
                    </div>
                    
                    <div class='greeting' style='font-size: 16px;'>
                        Welcome back to KisanMitra Customer Portal!
                    </div>
                    
                    <div class='message-text'>
                        We received a request to verify your identity for accessing your customer account. 
                        Please use the verification code below to complete your login:
                    </div>
                    
                    <div class='otp-box'>
                        <div style='font-size: 14px; color: #B85C38; margin-bottom: 12px;'>Your One-Time Password (OTP)</div>
                        <div class='otp-code'>$otp_code</div>
                    </div>
                    
                    <div class='expiry-note'>
                        ⏰ <strong>Time Remaining:</strong> This verification code will expire in <strong>25 minutes</strong> from the time of this email.
                    </div>
                    
                    <div class='message-text' style='font-size: 14px;'>
                        <strong>How to verify:</strong>
                        <ol style='margin-top: 8px; padding-left: 20px;'>
                            <li>Enter this 5-digit code on the verification page</li>
                            <li>Click 'Verify & Access' button</li>
                            <li>You will be redirected to your customer dashboard</li>
                        </ol>
                    </div>
                    
                    <div class='security-tips'>
                        🔒 <strong>Security Notice:</strong>
                        <ul>
                            <li>Never share this OTP with anyone, including KisanMitra support staff</li>
                            <li>If you didn't request this code, please ignore this email</li>
                            <li>For security reasons, do not forward this email to anyone</li>
                        </ul>
                    </div>
                    
                    <div class='message-text' style='text-align: center; margin-top: 25px;'>
                        <strong>Need help?</strong><br>
                        Contact our customer support at <span style='color: #B85C38;'>support@kisanmitra.com</span>
                    </div>
                </div>
                
                <div class='email-footer'>
                    <p>© 2024 KisanMitra - Connecting Farmers to Consumers</p>
                    <p>This is an automated message, please do not reply to this email.</p>
                    <div class='footer-links'>
                        <a href='#'>Help Center</a> • 
                        <a href='#'>Privacy Policy</a> • 
                        <a href='#'>Contact Support</a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Dear " . $recipient_name . ",\n\n"
                        . "Your KisanMitra customer login verification code is: $otp_code\n\n"
                        . "This code will expire in 25 minutes.\n\n"
                        . "How to verify:\n"
                        . "1. Enter this 5-digit code on the verification page\n"
                        . "2. Click 'Verify & Access' button\n"
                        . "3. You will be redirected to your customer dashboard\n\n"
                        . "Security Notice:\n"
                        . "- Never share this OTP with anyone\n"
                        . "- If you didn't request this code, please ignore this email\n\n"
                        . "Thank you for shopping with KisanMitra!\n\n"
                        . "KisanMitra Team";
        
        $mail->send();
        return ['success' => true, 'message' => 'OTP sent to your email'];
        
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return ['success' => false, 'message' => "Failed to send OTP: {$mail->ErrorInfo}"];
    }
}
?>
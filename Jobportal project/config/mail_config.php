<?php
/**
 * PHPMailer SMTP Configuration
 * 
 * IMPORTANT: Use Gmail App Password (NOT your normal password)
 * 1. Go to Google Account -> Security -> 2-Step Verification (must be ON)
 * 2. Search for "App Passwords" at the bottom
 * 3. Select "Mail" and "Windows Computer"
 * 4. Copy the 16-character code
 */

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'miriyaladeepika646@gmail.com'); // Replace with your Gmail
define('SMTP_PASS', 'bbqqdnbiurptcyys');    // Replace with your 16-character App Password
define('SMTP_FROM', 'miriyaladeepika646@gmail.com');
define('SMTP_FROM_NAME', 'Job Portal OTP Verification');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal Management System | Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: radial-gradient(circle at top right, #1e1b4b, #0f172a);">

<div class="login-card" style="width: 100%; max-width: 480px;">
    <div class="header" style="text-align: center; margin-bottom: 45px;">
        <div style="width: 90px; height: 90px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 15px 35px rgba(99, 102, 241, 0.5); transform: rotate(-5deg);">
            <i class="fas fa-rocket" style="font-size: 40px; color: white; transform: rotate(5deg);"></i>
        </div>
        <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -2px; margin-bottom: 5px; background: linear-gradient(to right, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">JOB PORTAL</h1>
        <p style="color: var(--text-muted); font-size: 0.85rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 600;">Management System</p>
    </div>

    <!-- Step 1: Credentials -->
    <form id="loginForm">
        <div class="form-group">
            <label><i class="fas fa-envelope" style="margin-right: 8px;"></i> Email Address</label>
            <input type="email" id="email" required placeholder="name@career.com" style="padding-left: 45px;">
        </div>
        <div class="form-group" style="position: relative;">
            <label><i class="fas fa-lock" style="margin-right: 8px;"></i> Password</label>
            <input type="password" id="password" required placeholder="••••••••" style="padding-left: 45px;">
            <div style="text-align: right; margin-top: 8px;">
                <a href="forgot_password.php" style="color: var(--primary-light); text-decoration: none; font-size: 0.8rem; font-weight: 600;">Forgot Password?</a>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" id="loginBtn" style="width: 100%; height: 55px; font-size: 1.1rem; margin-top: 10px;">
            <span>Secure Verification</span>
            <div class="spinner" id="loginSpinner" style="display:none; margin: 0 auto;"></div>
        </button>
    </form>

    <!-- Step 2: OTP Verification (Hidden initially) -->
    <div class="otp-section" id="otpSection" style="display:none;">
        <div class="header" style="text-align: center;">
            <h2 style="font-size: 1.8rem;">OTP Verification</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Code sent to <span id="displayEmail" style="color: var(--primary-light); font-weight: 700;"></span></p>
        </div>
        <form id="otpForm">
            <div class="form-group">
                <input type="text" id="otp" maxlength="6" pattern="\d{6}" required placeholder="000000" style="text-align:center; letter-spacing: 12px; font-size: 28px; height: 70px; background: rgba(255,255,255,0.05);">
            </div>
            <button type="submit" class="btn btn-primary" id="verifyBtn" style="width: 100%; height: 55px;">
                <span>Confirm & Access</span>
                <div class="spinner" id="verifySpinner" style="display:none; margin: 0 auto;"></div>
            </button>
        </form>
        <div class="resend-container" style="text-align: center; margin-top: 20px; font-size: 14px;">
            Didn't receive the code? <button id="resendBtn" style="background: none; border: none; color: var(--primary-light); cursor: pointer; font-weight: 700; padding: 0;">Resend OTP</button>
            <span id="timer" style="color: var(--text-muted); font-weight: 600;"></span>
        </div>
    </div>

    <div id="msg" class="msg"></div>

    <p style="text-align:center; margin-top: 30px; font-size: 14px; color: var(--text-muted);">
        Don't have an account? <a href="register.php" style="color: var(--primary-light); text-decoration:none; font-weight:700;">Create Global Profile</a>
    </p>
</div>

<script>
const loginForm = document.getElementById('loginForm');
const otpSection = document.getElementById('otpSection');
const otpForm = document.getElementById('otpForm');
const msg = document.getElementById('msg');
const resendBtn = document.getElementById('resendBtn');
const timerSpan = document.getElementById('timer');

let userEmail = '';
let cooldown = 0;

function showMsg(text, type) {
    msg.textContent = text;
    msg.className = `msg msg-${type}`;
    msg.style.display = 'block';
}

function startTimer() {
    cooldown = 30;
    resendBtn.disabled = true;
    const interval = setInterval(() => {
        if (cooldown <= 0) {
            clearInterval(interval);
            resendBtn.disabled = false;
            timerSpan.textContent = '';
        } else {
            timerSpan.textContent = `(${cooldown}s)`;
            cooldown--;
        }
    }, 1000);
}

// Step 1: Login Submission
loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    document.getElementById('loginSpinner').style.display = 'block';
    document.getElementById('loginBtn').disabled = true;
    msg.style.display = 'none';

    try {
        const response = await fetch('api/login_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        });
        const data = await response.json();

        if (data.status === 'success') {
            userEmail = data.email;
            document.getElementById('displayEmail').textContent = userEmail;
            
            // Trigger OTP Sending
            sendOTP();
        } else {
            showMsg(data.message, 'error');
            document.getElementById('loginSpinner').style.display = 'none';
            document.getElementById('loginBtn').disabled = false;
        }
    } catch (err) {
        showMsg('An error occurred. Please try again.', 'error');
        document.getElementById('loginSpinner').style.display = 'none';
        document.getElementById('loginBtn').disabled = false;
    }
});

async function sendOTP() {
    try {
        const response = await fetch('api/send_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(userEmail)}`
        });
        const data = await response.json();

        if (data.status === 'success') {
            loginForm.style.display = 'none';
            otpSection.style.display = 'block';
            showMsg(data.message, 'success');
            startTimer();
            
            // For debug only if PHPMailer missing
            if (data.debug_otp) console.log("Debug OTP:", data.debug_otp);
        } else {
            showMsg(data.message, 'error');
        }
    } catch (err) {
        showMsg('Failed to send OTP. Please try again.', 'error');
    } finally {
        document.getElementById('loginSpinner').style.display = 'none';
        document.getElementById('loginBtn').disabled = false;
    }
}

// Step 2: OTP Verification
otpForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const otp = document.getElementById('otp').value;
    
    document.getElementById('verifySpinner').style.display = 'block';
    document.getElementById('verifyBtn').disabled = true;

    try {
        const response = await fetch('api/verify_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(userEmail)}&otp=${encodeURIComponent(otp)}`
        });
        const data = await response.json();

        if (data.status === 'success') {
            showMsg('Verified! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            showMsg(data.message, 'error');
            document.getElementById('verifySpinner').style.display = 'none';
            document.getElementById('verifyBtn').disabled = false;
        }
    } catch (err) {
        showMsg('Verification failed. Please try again.', 'error');
        document.getElementById('verifySpinner').style.display = 'none';
        document.getElementById('verifyBtn').disabled = false;
    }
});

resendBtn.addEventListener('click', sendOTP);
</script>

</body>
</html>

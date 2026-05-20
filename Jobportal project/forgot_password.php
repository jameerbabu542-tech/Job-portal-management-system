<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Job Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: radial-gradient(circle at top right, #1e1b4b, #0f172a);">

<div class="login-card" style="width: 100%; max-width: 480px;">
    <div class="header" style="text-align: center; margin-bottom: 45px;">
        <div style="width: 80px; height: 80px; background: rgba(239, 68, 68, 0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
            <i class="fas fa-key" style="font-size: 32px; color: #ef4444;"></i>
        </div>
        <h1 style="font-size: 2rem; font-weight: 800;">Reset Access</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Securely recover your account</p>
    </div>

    <div style="display: flex; justify-content: flex-end; margin-bottom: 30px;">
        <a href="login.php" class="btn btn-primary" style="text-decoration: none; font-size: 0.9rem; padding: 10px 25px; display: flex; align-items: center; gap: 8px; box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);">
            <i class="fas fa-arrow-left"></i> <strong>Back</strong>
        </a>
    </div>

    <!-- Phase 1: Request OTP -->
    <form id="requestForm">
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Enter Registered Email</label>
            <input type="email" id="email" required placeholder="name@example.com">
        </div>
        <button type="submit" class="btn btn-primary" id="requestBtn" style="width: 100%;">
            <span>Send Reset Code</span>
            <div class="spinner" id="requestSpinner" style="display:none; margin: 0 auto;"></div>
        </button>
    </form>

    <!-- Phase 2: Verify OTP -->
    <div id="otpSection" style="display:none;">
        <form id="verifyForm">
            <div class="form-group">
                <label>Verification Code</label>
                <input type="text" id="otp" maxlength="6" required placeholder="000000" style="text-align:center; letter-spacing: 10px; font-size: 24px;">
            </div>
            <button type="submit" class="btn btn-primary" id="verifyBtn" style="width: 100%;">Verify & Continue</button>
        </form>
    </div>

    <!-- Phase 3: New Password -->
    <div id="resetSection" style="display:none;">
        <form id="resetForm">
            <div class="form-group">
                <label>New Secure Password</label>
                <input type="password" id="newPassword" required placeholder="••••••••">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" id="confirmPassword" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" id="resetBtn" style="width: 100%; background: #10b981;">Change Password</button>
        </form>
    </div>

    <div id="msg" class="msg" style="margin-top: 20px;"></div>

    <p style="text-align:center; margin-top: 30px; font-size: 14px;">
        Remembered password? <a href="login.php" style="color: var(--primary-light); text-decoration:none; font-weight:700;">Back to Login</a>
    </p>
</div>

<script>
let userEmail = '';
let verifiedOtp = '';

function showMsg(text, type) {
    const msg = document.getElementById('msg');
    msg.textContent = text;
    msg.className = `msg msg-${type}`;
    msg.style.display = 'block';
}

// Request OTP
document.getElementById('requestForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    userEmail = document.getElementById('email').value;
    document.getElementById('requestSpinner').style.display = 'block';
    document.getElementById('requestBtn').disabled = true;

    try {
        const response = await fetch('api/send_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(userEmail)}&reset=1`
        });
        const data = await response.json();
        if (data.status === 'success') {
            document.getElementById('requestForm').style.display = 'none';
            document.getElementById('otpSection').style.display = 'block';
            showMsg('Reset code sent to your email.', 'success');
            if (data.debug_otp) console.log("Debug Reset OTP:", data.debug_otp);
        } else {
            showMsg(data.message, 'error');
        }
    } catch (err) {
        showMsg('Server error. Try again.', 'error');
    } finally {
        document.getElementById('requestSpinner').style.display = 'none';
        document.getElementById('requestBtn').disabled = false;
    }
});

// Verify OTP
document.getElementById('verifyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const otp = document.getElementById('otp').value;
    try {
        const response = await fetch('api/verify_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(userEmail)}&otp=${encodeURIComponent(otp)}&reset=1`
        });
        const data = await response.json();
        if (data.status === 'success') {
            verifiedOtp = otp;
            document.getElementById('otpSection').style.display = 'none';
            document.getElementById('resetSection').style.display = 'block';
            showMsg('Identity verified. Set new password.', 'success');
        } else {
            showMsg(data.message, 'error');
        }
    } catch (err) {
        showMsg('Verification failed.', 'error');
    }
});

// Reset Password
document.getElementById('resetForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const pass = document.getElementById('newPassword').value;
    const confirm = document.getElementById('confirmPassword').value;

    if (pass !== confirm) {
        showMsg('Passwords do not match!', 'error');
        return;
    }

    try {
        const response = await fetch('api/reset_password_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(userEmail)}&password=${encodeURIComponent(pass)}&otp=${encodeURIComponent(verifiedOtp)}`
        });
        const data = await response.json();
        if (data.status === 'success') {
            showMsg('Password changed! Redirecting...', 'success');
            setTimeout(() => window.location.href = 'login.php', 2000);
        } else {
            showMsg(data.message, 'error');
        }
    } catch (err) {
        showMsg('Reset failed.', 'error');
    }
});
</script>
</body>
</html>

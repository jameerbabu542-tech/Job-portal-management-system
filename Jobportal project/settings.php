<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | Job Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="min-height: 100vh; background: radial-gradient(circle at top right, #1e1b4b, #0f172a); color: white;">

    <header class="main-header" style="padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); border-bottom: 1px solid var(--glass-border);">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="javascript:history.back()" style="color: white; text-decoration: none; font-size: 20px;"><i class="fas fa-arrow-left"></i></a>
            <h1 style="font-size: 1.5rem; margin: 0;">Account Settings</h1>
        </div>
    </header>

    <div style="max-width: 1200px; margin: 20px auto 0; padding: 0 40px; display: flex; justify-content: flex-end;">
        <a href="javascript:history.back()" class="btn btn-primary" style="text-decoration: none; font-size: 0.9rem; padding: 10px 25px; display: flex; align-items: center; gap: 8px; box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);">
            <i class="fas fa-arrow-left"></i> <strong>Back</strong>
        </a>
    </div>

    <div class="dashboard-container" style="max-width: 600px; margin: 20px auto 40px; padding: 0;">
        <div class="job-card" style="padding: 40px;">
            <h2 style="margin-bottom: 30px;"><i class="fas fa-shield-alt" style="color: var(--accent);"></i> Security Settings</h2>
            
            <form id="settingsForm">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required placeholder="••••••••" style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); color: white;">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required placeholder="••••••••" style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); color: white;">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••" style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); color: white;">
                </div>

                <div style="margin-top: 30px; display: flex; flex-direction: column; gap: 15px;">
                    <button type="submit" class="btn btn-primary" id="updateBtn">Update Password</button>
                    <button type="button" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">Deactivate Account</button>
                </div>
            </form>
            
            <div id="msg" class="msg" style="margin-top: 20px; display: none;"></div>
        </div>
    </div>

    <script>
        document.getElementById('settingsForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const btn = document.getElementById('updateBtn');
            const msg = document.getElementById('msg');
            
            if (formData.get('new_password') !== formData.get('confirm_password')) {
                msg.textContent = 'New passwords do not match!';
                msg.className = 'msg msg-error';
                msg.style.display = 'block';
                return;
            }

            btn.disabled = true;
            btn.innerText = 'Updating...';
            msg.style.display = 'none';

            try {
                const res = await fetch('api/update_settings.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                
                msg.textContent = result.message;
                msg.className = `msg msg-${result.status === 'success' ? 'success' : 'error'}`;
                msg.style.display = 'block';
                if (result.status === 'success') e.target.reset();
            } catch (err) {
                msg.textContent = 'An error occurred.';
                msg.className = 'msg msg-error';
                msg.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.innerText = 'Update Password';
            }
        });
    </script>
</body>
</html>

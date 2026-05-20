<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch fresh user data
$stmt = $pdo->prepare("SELECT full_name, email, phone, location, bio, role, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Job Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="min-height: 100vh; background: radial-gradient(circle at top right, #1e1b4b, #0f172a); color: white; display: flex; flex-direction: column;">

    <header class="main-header" style="padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); border-bottom: 1px solid var(--glass-border);">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="javascript:history.back()" style="color: white; text-decoration: none; font-size: 20px;"><i class="fas fa-arrow-left"></i></a>
            <h1 style="font-size: 1.5rem; margin: 0;">Account Settings</h1>
        </div>
        <div class="avatar" style="width: 40px; height: 40px; font-size: 16px;"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
    </header>

    <div style="max-width: 1200px; margin: 20px auto 0; padding: 0 40px; display: flex; justify-content: flex-end;">
        <a href="javascript:history.back()" class="btn btn-primary" style="text-decoration: none; font-size: 0.9rem; padding: 10px 25px; display: flex; align-items: center; gap: 8px; box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);">
            <i class="fas fa-arrow-left"></i> <strong>Back</strong>
        </a>
    </div>

    <div class="dashboard-container" style="max-width: 800px; margin: 20px auto 40px; padding: 0;">
        <div class="job-card" style="padding: 40px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; width: 150px; height: 150px; background: var(--primary); filter: blur(100px); opacity: 0.1; z-index: 0;"></div>
            
            <div style="display: flex; align-items: center; gap: 30px; margin-bottom: 40px; position: relative; z-index: 1;">
                <div class="avatar" style="width: 100px; height: 100px; font-size: 40px; background: linear-gradient(135deg, var(--primary), var(--accent));"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                <div>
                    <h2 style="font-size: 2rem; margin: 0;"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p style="color: var(--text-muted); margin: 5px 0 0;"><i class="fas fa-shield-alt"></i> Account Role: <span style="color: var(--primary-light); font-weight: 700; text-transform: uppercase;"><?php echo $user['role']; ?></span></p>
                    <p style="color: var(--text-muted); font-size: 0.8rem;">Member since <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>

            <form id="profileForm" style="position: relative; z-index: 1;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); color: white;">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); color: white;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+1 234 567 890" style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); color: white;">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Current Location</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" placeholder="e.g. London, UK" style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); color: white;">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-info-circle"></i> Professional Bio</label>
                    <textarea name="bio" placeholder="Tell us about yourself..." style="width: 100%; height: 100px; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); border-radius: 12px; color: white; padding: 15px;"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <div style="margin-top: 30px; border-top: 1px solid var(--glass-border); padding-top: 30px; display: flex; justify-content: flex-end; gap: 15px;">
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>
                </div>
            </form>
            
            <div id="msg" class="msg" style="margin-top: 20px; display: none;"></div>
        </div>
    </div>

    <script>
        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const saveBtn = document.getElementById('saveBtn');
            const msg = document.getElementById('msg');
            
            saveBtn.disabled = true;
            saveBtn.innerText = 'Saving...';
            msg.style.display = 'none';

            try {
                const res = await fetch('api/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                
                msg.textContent = result.message;
                msg.className = `msg msg-${result.status === 'success' ? 'success' : 'error'}`;
                msg.style.display = 'block';
                
                if (result.status === 'success') {
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (err) {
                msg.textContent = 'Server error occurred.';
                msg.className = 'msg msg-error';
                msg.style.display = 'block';
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Save Changes';
            }
        });
    </script>
</body>
</html>

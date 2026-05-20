<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch existing resume
$stmt = $pdo->prepare("SELECT * FROM resumes WHERE user_id = ?");
$stmt->execute([$user_id]);
$resume = $stmt->fetch();

// If no resume exists, create empty defaults
if (!$resume) {
    $resume = [
        'education' => '',
        'experience' => '',
        'skills' => ''
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Resume | Job Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="min-height: 100vh; background: radial-gradient(circle at top right, #1e1b4b, #0f172a); color: white; display: flex; flex-direction: column;">

    <header class="main-header" style="padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); border-bottom: 1px solid var(--glass-border);">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="student_dashboard.php" style="color: white; text-decoration: none; font-size: 20px;"><i class="fas fa-arrow-left"></i></a>
            <h1 style="font-size: 1.5rem; margin: 0;">Digital Resume Builder</h1>
        </div>
        <div class="avatar" style="width: 40px; height: 40px; font-size: 16px;"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
    </header>

    <div style="max-width: 1200px; margin: 20px auto 0; padding: 0 40px; display: flex; justify-content: flex-end;">
        <a href="javascript:history.back()" class="btn btn-primary" style="text-decoration: none; font-size: 0.9rem; padding: 10px 25px; display: flex; align-items: center; gap: 8px; box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);">
            <i class="fas fa-arrow-left"></i> <strong>Back</strong>
        </a>
    </div>

    <div class="dashboard-container" style="max-width: 900px; margin: 20px auto 40px; padding: 0;">
        <div class="job-card" style="padding: 40px;">
            <h2 style="margin-bottom: 30px; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-file-invoice" style="color: var(--primary-light);"></i> 
                Professional Profile
            </h2>

            <form id="resumeForm">
                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="font-size: 1.1rem; color: var(--primary-light);"><i class="fas fa-graduation-cap"></i> Education History</label>
                    <textarea name="education" placeholder="e.g. Master of Science in Computer Science - Stanford University (2020)" style="width: 100%; height: 120px; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); border-radius: 12px; color: white; padding: 15px;"><?php echo htmlspecialchars($resume['education']); ?></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="font-size: 1.1rem; color: var(--primary-light);"><i class="fas fa-briefcase"></i> Work Experience</label>
                    <textarea name="experience" placeholder="e.g. Senior Developer at Google (3 years) - Led team of 5 in building scalable APIs." style="width: 100%; height: 150px; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); border-radius: 12px; color: white; padding: 15px;"><?php echo htmlspecialchars($resume['experience']); ?></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="font-size: 1.1rem; color: var(--primary-light);"><i class="fas fa-tools"></i> Core Skills (Comma separated)</label>
                    <input type="text" name="skills" value="<?php echo htmlspecialchars($resume['skills']); ?>" placeholder="e.g. PHP, MySQL, JavaScript, AWS, Docker" style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); color: white;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 15px;">
                    <button type="submit" class="btn btn-primary" id="saveBtn" style="width: 200px;">Save Digital Resume</button>
                </div>
            </form>
            
            <div id="msg" class="msg" style="margin-top: 20px; display: none;"></div>
        </div>
    </div>

    <script>
        document.getElementById('resumeForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const saveBtn = document.getElementById('saveBtn');
            const msg = document.getElementById('msg');
            
            saveBtn.disabled = true;
            saveBtn.innerText = 'Syncing...';
            msg.style.display = 'none';

            try {
                const res = await fetch('api/save_resume.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                
                msg.textContent = result.message;
                msg.className = `msg msg-${result.status === 'success' ? 'success' : 'error'}`;
                msg.style.display = 'block';
            } catch (err) {
                msg.textContent = 'Failed to save resume.';
                msg.className = 'msg msg-error';
                msg.style.display = 'block';
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Save Digital Resume';
            }
        });
    </script>
</body>
</html>

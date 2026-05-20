<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Console | Job Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="main-header" style="position: relative;">
        <div class="profile-area" style="position: absolute; top: 20px; right: 40px; z-index: 1000;">
            <div class="profile-menu">
                <div class="profile-trigger" onclick="toggleProfileDropdown()" style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                    <div class="avatar" style="box-shadow: 0 0 15px var(--primary);"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
                    <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-muted);"></i>
                </div>
                <div class="profile-dropdown" id="profileDropdown" style="display: none; position: absolute; top: 50px; right: 0; width: 180px; background: #1e293b; border: 1px solid var(--glass-border); border-radius: 12px; z-index: 1000; box-shadow: 0 10px 25px rgba(0,0,0,0.5); text-align: left;">
                    <a href="profile.php" style="display: block; padding: 12px 15px; color: white; text-decoration: none; border-bottom: 1px solid var(--glass-border);"><i class="fas fa-user-circle" style="margin-right: 10px;"></i> My Profile</a>
                    <a href="logout.php" style="display: block; padding: 12px 15px; color: #ef4444; text-decoration: none;"><i class="fas fa-sign-out-alt" style="margin-right: 10px;"></i> Logout</a>
                </div>
            </div>
        </div>
        <h1>MASTER CONSOLE</h1>
        <p>SYSTEM ADMIN | JOB PORTAL MANAGEMENT SYSTEM</p>
        <div style="margin-top: 30px; display: flex; justify-content: center; gap: 15px;">
            <button onclick="openPostModal()" class="btn btn-primary"><i class="fas fa-plus"></i> Add Job Dataset</button>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- System Health Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="stat-total-jobs">0</h3>
                <p>Global Listings</p>
            </div>
            <div class="stat-card">
                <h3 id="stat-total-apps">0</h3>
                <p>Total Applications</p>
            </div>
            <div class="stat-card">
                <h3 id="stat-active-users">0</h3>
                <p>Registered Users</p>
            </div>
        </div>

        <div class="job-card">
            <h2 style="margin-bottom: 25px;"><i class="fas fa-database"></i> Live Dataset Management</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #334155; text-align: left; color: var(--text-muted);">
                            <th style="padding: 15px;">Job Title</th>
                            <th style="padding: 15px;">Sector</th>
                            <th style="padding: 15px;">Location</th>
                            <th style="padding: 15px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="adminJobTable">
                        <!-- Content -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Post Job Modal for Admin -->
    <div id="postModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="header">
                <h2>Inject Global Dataset</h2>
                <p>Add new opportunities to the platform</p>
            </div>
            <form id="adminPostForm">
                <div class="form-group">
                    <label>Job Title</label>
                    <input type="text" name="title" required placeholder="e.g. Senior Data Scientist">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Sector</label>
                        <select name="sector" class="filter-select" style="width: 100%; background: #0f172a; color: white;">
                            <option value="Tech">Information Technology</option>
                            <option value="Banking">Banking / Finance</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Government">Government</option>
                            <option value="Teaching">Education</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" required placeholder="e.g. New York">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required style="width: 100%; height: 100px; background: #0f172a; color: white; border-radius: 10px; padding: 10px;"></textarea>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" onclick="closePostModal()" class="btn" style="background: #334155;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Publish Dataset</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPostModal() { document.getElementById('postModal').style.display = 'block'; }
        function closePostModal() { document.getElementById('postModal').style.display = 'none'; }
        
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
        
        window.onclick = e => { if(!e.target.closest('.profile-trigger')) document.getElementById('profileDropdown').style.display = 'none'; };

        async function fetchAdminStats() {
            const res = await fetch('api/get_stats.php');
            const result = await res.json();
            if (result.status === 'success') {
                document.getElementById('stat-total-jobs').innerText = result.data.total_jobs;
                document.getElementById('stat-total-apps').innerText = result.data.total_applications;
                document.getElementById('stat-active-users').innerText = '150+'; 
            }
        }

        async function fetchJobs() {
            const res = await fetch('api/fetch_jobs.php');
            const result = await res.json();
            const table = document.getElementById('adminJobTable');
            table.innerHTML = result.data.map(job => `
                <tr style="border-bottom: 1px solid #334155;">
                    <td style="padding: 15px;">${job.title}</td>
                    <td style="padding: 15px;"><span style="background: rgba(99,102,241,0.1); padding: 4px 10px; border-radius: 20px; font-size: 11px;">${job.sector}</span></td>
                    <td style="padding: 15px;">${job.location}</td>
                    <td style="padding: 15px;"><button class="btn" style="padding: 5px 12px; background: rgba(239,68,68,0.1); color: var(--danger); font-size: 12px;">Delete</button></td>
                </tr>
            `).join('');
        }

        document.getElementById('adminPostForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            // Admin posts as the system recruiter (ID 99)
            const res = await fetch('api/post_job.php', { method: 'POST', body: formData });
            const result = await res.json();
            alert(result.message);
            if (result.status === 'success') {
                closePostModal();
                fetchAdminStats();
                fetchJobs();
            }
        });

        fetchAdminStats();
        fetchJobs();
    </script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal Management System | Employer</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="display: block;">
    <div class="dashboard-container">
        <div class="dashboard-header" style="position: relative;">
            <div>
                <h1>Job Portal Management System</h1>
                <p>Employer Management Console | Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <button onclick="openModal()" class="btn btn-primary" style="width: auto;">+ Post New Job</button>
                <div class="profile-menu" style="position: relative;">
                    <div class="profile-trigger" onclick="toggleProfileDropdown()" style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <div class="avatar" style="width: 40px; height: 40px;"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
                        <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-muted);"></i>
                    </div>
                    <div class="profile-dropdown" id="profileDropdown" style="display: none; position: absolute; top: 50px; right: 0; width: 180px; background: #1e293b; border: 1px solid var(--glass-border); border-radius: 12px; z-index: 1000; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
                        <a href="profile.php" style="display: block; padding: 12px 15px; color: white; text-decoration: none; border-bottom: 1px solid var(--glass-border);"><i class="fas fa-user-circle" style="margin-right: 10px;"></i> My Profile</a>
                        <a href="logout.php" style="display: block; padding: 12px 15px; color: #ef4444; text-decoration: none;"><i class="fas fa-sign-out-alt" style="margin-right: 10px;"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="stat-total-jobs">0</h3>
                <p>Jobs Posted</p>
            </div>
            <div class="stat-card">
                <h3 id="stat-applications">0</h3>
                <p>Total Applications</p>
            </div>
            <div class="stat-card">
                <h3 id="stat-shortlisted">0</h3>
                <p>Shortlisted</p>
            </div>
        </div>

        <div class="job-list" id="employerJobs">
            <!-- Jobs posted by this employer will appear here -->
        </div>
    </div>

    <!-- Post Job Modal -->
    <div id="jobModal" class="modal">
        <div class="modal-content">
            <div class="header">
                <h2>Post a New Job</h2>
                <p>Fill in the details to attract the best candidates</p>
            </div>
            <form id="postJobForm">
                <div class="form-group">
                    <label>Job Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Senior PHP Developer">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Industry Sector *</label>
                        <select name="sector" required class="filter-select" style="width: 100%; background: #0f172a; border: 1px solid var(--glass-border); color: white;">
                            <option value="Tech">Information Technology / Software</option>
                            <option value="Teaching">Education / Teaching</option>
                            <option value="Government">Government / Public Service</option>
                            <option value="Banking">Banking / Fintech / Finance</option>
                            <option value="Healthcare">Healthcare / Medical / Biotech</option>
                            <option value="Real Estate">Real Estate / Construction</option>
                            <option value="E-commerce">Retail / E-commerce</option>
                            <option value="Hospitality">Tourism / Hospitality</option>
                            <option value="Energy">Energy / Oil & Gas</option>
                            <option value="Automotive">Automotive / EV</option>
                            <option value="Legal">Legal / Law Services</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Location *</label>
                        <select name="location" required class="filter-select" style="width: 100%; background: #0f172a; border: 1px solid var(--glass-border); color: white;">
                            <option value="Remote">Remote Only</option>
                            <option value="New York">New York, USA</option>
                            <option value="San Francisco">San Francisco, USA</option>
                            <option value="London">London, UK</option>
                            <option value="Berlin">Berlin, Germany</option>
                            <option value="Bangalore">Bangalore, India</option>
                            <option value="Singapore">Singapore</option>
                            <option value="Dubai">Dubai, UAE</option>
                            <option value="Sydney">Sydney, Australia</option>
                            <option value="Tokyo">Tokyo, Japan</option>
                            <option value="Toronto">Toronto, Canada</option>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Salary Range</label>
                        <input type="text" name="salary" placeholder="e.g. $50k - $80k">
                    </div>
                    <div class="form-group">
                        <label>Job Type</label>
                        <select name="job_type" class="filter-select" style="width: 100%; background: #0f172a; border: 1px solid var(--glass-border); color: white;">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                </div>
                    <div class="form-group">
                        <label>Experience Level</label>
                        <select name="experience" class="filter-select" style="width: 100%; background: #0f172a; border: 1px solid var(--glass-border); color: white;">
                            <option value="Entry Level">Entry Level</option>
                            <option value="1-3 Years">1-3 Years</option>
                            <option value="3-5 Years">3-5 Years</option>
                            <option value="5+ Years">5+ Years</option>
                        </select>
                    </div>
                <div class="form-group">
                    <label>Skills Required (Comma separated)</label>
                    <input type="text" name="skills" placeholder="e.g. PHP, MySQL, JavaScript">
                </div>
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" required style="width: 100%; height: 100px; padding: 12px; border-radius: 12px; border: 2px solid #e2e8f0;"></textarea>
                </div>
                <div class="form-group">
                    <label>Application Deadline</label>
                    <input type="date" name="deadline">
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" onclick="closeModal()" class="btn" style="background: #e2e8f0;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Post Job</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        async function fetchStats() {
            try {
                const res = await fetch('api/get_stats.php');
                const result = await res.json();
                if (result.status === 'success') {
                    document.getElementById('stat-total-jobs').innerText = result.data.total_jobs;
                    document.getElementById('stat-applications').innerText = result.data.total_applications;
                    document.getElementById('stat-shortlisted').innerText = result.data.shortlisted;
                }
            } catch (e) { console.error('Stats error:', e); }
        }

        async function fetchMyJobs() {
            // Reusing fetch_jobs.php but we could filter by employer if needed. 
            // For now, let's just show a simplified view or add an employer_id filter to the API.
            // Since we don't have a specific "my_jobs" API yet, let's just show the stats and allow posting.
            // I will create a quick 'api/fetch_employer_jobs.php' to make it robust.
        }

        function openModal() { document.getElementById('jobModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('jobModal').style.display = 'none'; }
        
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
        
        // Close dropdown when clicking outside
        window.onclick = e => { if(!e.target.closest('.profile-trigger')) document.getElementById('profileDropdown').style.display = 'none'; };

        document.getElementById('postJobForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            try {
                const res = await fetch('api/post_job.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                
                alert(result.message);
                if (result.status === 'success') {
                    closeModal();
                    e.target.reset();
                    fetchStats();
                }
            } catch (e) { alert('An error occurred'); }
        });

        fetchStats();
    </script>
</body>
</html>

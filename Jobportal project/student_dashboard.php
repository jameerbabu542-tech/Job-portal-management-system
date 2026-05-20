<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="main-header" style="position: relative;">
        <div class="profile-area" style="position: absolute; top: 20px; right: 40px; z-index: 1000;">
            <div class="profile-menu">
                <div class="profile-trigger" onclick="toggleProfileDropdown()">
                    <div class="avatar" style="box-shadow: 0 0 15px var(--primary);"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
                    <span class="user-name-header" style="font-weight: 600; font-size: 0.9rem; color: white;"><?php echo explode(' ', $_SESSION['full_name'])[0]; ?></span>
                    <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-muted);"></i>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="profile.php"><i class="fas fa-user-circle" style="margin-right: 10px;"></i> My Profile</a>
                    <a href="resume.php"><i class="fas fa-file-invoice" style="margin-right: 10px;"></i> My Resume</a>
                    <a href="settings.php"><i class="fas fa-cog" style="margin-right: 10px;"></i> Settings</a>
                    <hr style="border: 0; border-top: 1px solid var(--glass-border);">
                    <a href="logout.php" style="color: var(--danger);"><i class="fas fa-sign-out-alt" style="margin-right: 10px;"></i> Sign Out</a>
                </div>
            </div>
        </div>
        <h1>JOB PORTAL</h1>
        <p>Advanced Career Management System | Welcome back, <span style="color: var(--primary-light); font-weight: 800;"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span></p>
    </header>

    <div class="dashboard-container">
        <!-- Advanced Stats -->
        <div class="stats-grid">
            <div class="stat-card" onclick="showSection('applied')" style="cursor: pointer; border-left: 4px solid var(--success);">
                <h3 id="stat-applied">0</h3>
                <p>Applied Opportunities</p>
            </div>
            <div class="stat-card" onclick="showSection('saved')" style="cursor: pointer; border-left: 4px solid var(--primary);">
                <h3 id="stat-saved">0</h3>
                <p>Saved for Later</p>
            </div>
            <div class="stat-card" onclick="showSection('responses')" style="cursor: pointer; border-left: 4px solid var(--accent);">
                <h3 id="stat-responses">0</h3>
                <p>Employer Responses</p>
            </div>
        </div>

        <!-- Section Containers -->
        <div id="appliedSection" class="dashboard-section" style="display: none; margin-bottom: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.5rem; color: var(--success);"><i class="fas fa-check-double"></i> My Applications</h2>
                <button onclick="hideSections()" class="btn" style="background: rgba(255,255,255,0.05); font-size: 12px;">Close x</button>
            </div>
            <div id="appliedList" class="job-list"></div>
        </div>

        <div id="savedSection" class="dashboard-section" style="display: none; margin-bottom: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.5rem; color: var(--primary-light);"><i class="fas fa-bookmark"></i> Saved Opportunities</h2>
                <button onclick="hideSections()" class="btn" style="background: rgba(255,255,255,0.05); font-size: 12px;">Close x</button>
            </div>
            <div id="savedList" class="job-list"></div>
        </div>

        <div id="responsesSection" class="dashboard-section" style="display: none; margin-bottom: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.5rem; color: var(--accent);"><i class="fas fa-reply"></i> Recruiter Feedback</h2>
                <button onclick="hideSections()" class="btn" style="background: rgba(255,255,255,0.05); font-size: 12px;">Close x</button>
            </div>
            <div id="responsesList" class="job-list"></div>
        </div>

        <!-- Global Search & Filters -->
        <div class="job-controls">
            <input type="text" id="searchInput" class="search-input" placeholder="Search positions, skills, or companies...">
            <select id="sectorFilter" class="filter-select">
                <option value="">All Industry Sectors</option>
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
            <select id="locationFilter" class="filter-select">
                <option value="">Global Locations</option>
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

        <!-- Featured Opportunities Header -->
        <div style="margin: 40px 0 20px; display: flex; align-items: center; justify-content: space-between;">
            <h2 style="font-size: 1.8rem; font-weight: 700;"><i class="fas fa-fire" style="color: var(--accent); margin-right: 10px;"></i> Featured Opportunities</h2>
            <span id="jobCountBadge" style="background: rgba(99,102,241,0.1); color: var(--primary-light); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">0 Jobs Found</span>
        </div>

        <!-- Main Feed -->
        <div id="jobList" class="job-list">
            <!-- Dynamic Content -->
            <div style="grid-column: 1/-1; text-align: center; padding: 100px;">
                <div class="spinner" style="display: inline-block; width: 40px; height: 40px; border-width: 4px; border-top-color: var(--primary);"></div>
                <p style="margin-top: 20px; color: var(--text-muted);">Curating global opportunities...</p>
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div id="applyModal" class="modal">
        <div class="modal-content" style="max-width: 600px; margin: 50px auto;">
            <form id="applyForm">
                <input type="hidden" id="applyJobId" name="job_id">
                
                <div class="step" id="singleStep">
                    <h2 style="margin-bottom: 20px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px;">Job Application Details</h2>
                    
                    <!-- Section: Contact -->
                    <div style="margin-bottom: 25px;">
                        <h4 style="color: var(--primary-light); margin-bottom: 15px; font-size: 0.9rem; text-transform: uppercase;"><i class="fas fa-address-book"></i> Contact Information</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="applicant_email" required placeholder="name@example.com">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone" required placeholder="+1 234 567 890">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Background -->
                    <div style="margin-bottom: 25px;">
                        <h4 style="color: var(--primary-light); margin-bottom: 15px; font-size: 0.9rem; text-transform: uppercase;"><i class="fas fa-user-graduate"></i> Professional Background</h4>
                        <div class="form-group">
                            <label>Native Place / Current Location</label>
                            <input type="text" name="native_place" required placeholder="e.g. New York, USA">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label>Highest Education</label>
                                <input type="text" name="education" required placeholder="e.g. MS in CS">
                            </div>
                            <div class="form-group">
                                <label>Known Languages</label>
                                <input type="text" name="languages" required placeholder="e.g. English, Spanish">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Resume -->
                    <div>
                        <h4 style="color: var(--primary-light); margin-bottom: 15px; font-size: 0.9rem; text-transform: uppercase;"><i class="fas fa-file-alt"></i> Portfolio & Resume</h4>
                        <div class="form-group">
                            <label>LinkedIn / Portfolio URL</label>
                            <input type="url" name="portfolio" placeholder="https://linkedin.com/in/yourname">
                        </div>
                        <div class="form-group">
                            <label>Resume (Text summary)</label>
                            <textarea name="resume_text" required style="width: 100%; height: 80px; background: #0f172a; color: white; border-radius: 12px; padding: 15px; border: 1px solid var(--glass-border);" placeholder="Briefly summarize your experience..."></textarea>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 30px;">
                        <button type="button" onclick="closeModal()" class="btn" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Complete Application</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Chatbot -->
    <div id="chatbot" style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
        <div id="chatWindow" style="display: none; width: 320px; height: 450px; background: #1e293b; border: 1px solid var(--primary); border-radius: 24px; margin-bottom: 20px; flex-direction: column; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            <div style="padding: 20px; background: var(--primary); border-radius: 24px 24px 0 0; font-weight: 800;">Career Assistant AI</div>
            <div id="chatMessages" style="flex: 1; padding: 20px; overflow-y: auto; font-size: 14px;"></div>
            <div style="padding: 15px; border-top: 1px solid var(--glass-border); display: flex;">
                <input type="text" id="chatInput" placeholder="Ask about jobs..." style="flex:1; background: transparent; border: none; color: white; outline: none;">
                <button onclick="sendChatMessage()" style="background: none; border: none; color: var(--primary-light); cursor: pointer;"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
        <button onclick="toggleChat()" style="width: 60px; height: 60px; border-radius: 50%; background: var(--primary); color: white; border: none; cursor: pointer; box-shadow: 0 10px 20px rgba(99,102,241,0.4); font-size: 24px;">
            <i class="fas fa-robot"></i>
        </button>
    </div>

    <script>
        // Profile Dropdown
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Section Toggling
        function hideSections() {
            ['appliedSection', 'savedSection', 'responsesSection'].forEach(id => {
                const el = document.getElementById(id);
                if(el) el.style.display = 'none';
            });
        }

        async function showSection(type) {
            hideSections();
            const sectionId = type + 'Section';
            const listId = type + 'List';
            const el = document.getElementById(sectionId);
            if(!el) return;

            el.style.display = 'block';
            document.getElementById(listId).innerHTML = '<p style="padding: 20px;">Fetching your data...</p>';
            el.scrollIntoView({ behavior: 'smooth' });

            try {
                const res = await fetch(`api/get_user_content.php?type=${type}`);
                const result = await res.json();
                if (result.status === 'success') {
                    renderPersonalizedList(listId, result.data, type);
                }
            } catch (e) { console.error(e); }
        }

        function renderPersonalizedList(containerId, jobs, type) {
            const container = document.getElementById(containerId);
            if (jobs.length === 0) {
                container.innerHTML = '<p style="padding: 20px;">No records found.</p>';
                return;
            }
            container.innerHTML = jobs.map(job => `
                <div class="job-card">
                    <h2>${job.title}</h2>
                    <span class="company">${job.company_name}</span>
                    <p style="font-size: 12px; color: var(--text-muted);">${job.location} | ${job.salary}</p>
                </div>
            `).join('');
        }

        // Job Fetching
        async function fetchJobs() {
            const search = document.getElementById('searchInput').value;
            const sector = document.getElementById('sectorFilter').value;
            const location = document.getElementById('locationFilter').value;
            
            try {
                const res = await fetch(`api/fetch_jobs.php?search=${search}&sector=${sector}&location=${location}`);
                const result = await res.json();
                if (result.status === 'success') renderJobs(result.data);
            } catch (e) { console.error(e); }
        }

        function renderJobs(jobs) {
            const container = document.getElementById('jobList');
            document.getElementById('jobCountBadge').innerText = `${jobs.length} Jobs Found`;
            
            if (jobs.length === 0) {
                container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 50px;"><p>No opportunities found.</p></div>';
                return;
            }

            container.innerHTML = jobs.map(job => {
                const isApplied = parseInt(job.applied) > 0;
                return `
                <div class="job-card" style="animation: fadeInUp 0.5s ease-out forwards;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div style="width: 50px; height: 50px; background: rgba(99,102,241,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary-light); font-size: 20px;">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <span style="font-size: 10px; background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 20px; color: var(--text-muted); border: 1px solid var(--glass-border);">${job.sector || 'General'}</span>
                    </div>
                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted);">${job.job_type}</span>
                    <h2 style="margin-top: 5px; font-size: 1.3rem;">${job.title}</h2>
                    <span class="company"><i class="fas fa-building" style="font-size: 12px; margin-right: 5px;"></i> ${job.company_name}</span>
                    <div style="display: flex; gap: 15px; font-size: 12px; color: var(--text-muted); margin-bottom: 20px; flex-wrap: wrap;">
                        <span><i class="fas fa-map-marker-alt"></i> ${job.location || 'Remote'}</span>
                        <span><i class="fas fa-coins"></i> ${job.salary || 'Negotiable'}</span>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        ${isApplied ? 
                            `<button class="btn" style="flex: 1; background: #064e3b; color: #10b981; cursor: default;" disabled><i class="fas fa-check-circle"></i> Applied</button>` :
                            `<button onclick="applyJob(${job.id})" class="btn btn-primary" style="flex: 1;">Apply Now</button>`
                        }
                    </div>
                </div>
            `}).join('');
        }

        // Stats
        async function fetchStats() {
            const res = await fetch('api/get_stats.php');
            const result = await res.json();
            if (result.status === 'success') {
                document.getElementById('stat-applied').innerText = result.data.applied_jobs || 0;
                document.getElementById('stat-saved').innerText = result.data.saved_jobs || 0;
                document.getElementById('stat-responses').innerText = result.data.responses || 0;
            }
        }

        // Modal
        function applyJob(jobId) {
            document.getElementById('applyJobId').value = jobId;
            document.querySelectorAll('.step').forEach(s => s.style.display = 'none');
            document.getElementById('singleStep').style.display = 'block';
            document.getElementById('applyModal').style.display = 'block';
        }
        function closeModal() { document.getElementById('applyModal').style.display = 'none'; }

        document.getElementById('applyForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const res = await fetch('api/apply_job.php', { method: 'POST', body: formData });
            const result = await res.json();
            alert(result.message);
            if (result.status === 'success') {
                closeModal();
                fetchStats();
                fetchJobs();
            }
        });

        // Chatbot
        function toggleChat() {
            const win = document.getElementById('chatWindow');
            win.style.display = win.style.display === 'flex' ? 'none' : 'flex';
        }
        function sendChatMessage() {
            const input = document.getElementById('chatInput');
            const msg = input.value;
            if (!msg) return;
            const container = document.getElementById('chatMessages');
            container.innerHTML += `<div style="text-align:right; margin-bottom:10px;"><span style="background:var(--primary); padding:8px 12px; border-radius:12px;">${msg}</span></div>`;
            input.value = '';
            setTimeout(() => {
                container.innerHTML += `<div style="text-align:left; margin-bottom:10px;"><span style="background:rgba(255,255,255,0.05); padding:8px 12px; border-radius:12px;">I'm analyzing opportunities for "${msg}"...</span></div>`;
                container.scrollTop = container.scrollHeight;
            }, 1000);
        }

        // Event Listeners
        document.getElementById('searchInput').addEventListener('input', fetchJobs);
        document.getElementById('sectorFilter').addEventListener('change', fetchJobs);
        document.getElementById('locationFilter').addEventListener('change', fetchJobs);
        window.onclick = e => { if(!e.target.closest('.profile-trigger')) document.getElementById('profileDropdown').style.display = 'none'; };

        // Init
        fetchStats();
        fetchJobs();
    </script>
</body>
</html>

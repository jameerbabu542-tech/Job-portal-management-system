<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Job Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh;">

<div class="login-card">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <a href="login.php" class="btn btn-primary" style="text-decoration: none; font-size: 0.8rem; padding: 8px 15px; display: flex; align-items: center; gap: 6px; box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);">
            <i class="fas fa-arrow-left"></i> <strong>Back</strong>
        </a>
    </div>
    <div class="header">
        <h1>Create Account</h1>
        <p>JOB PORTAL MANAGEMENT SYSTEM</p>
    </div>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" required placeholder="John Doe">
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="john@example.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>
        <div class="form-group">
            <label>I am a:</label>
            <select name="role" required style="width: 100%; padding: 12px; background: #0f172a; border: 1px solid var(--glass-border); color: white; border-radius: 12px; outline: none;">
                <option value="student">Job Seeker (Student)</option>
                <option value="employer">Employer</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Register Now</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'config/db.php';
        
        $name = $_POST['full_name'];
        $email = $_POST['email'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $pass, $role]);
            echo "<div class='msg msg-success' style='display:block; margin-top:15px;'>Registration successful! <a href='login.php'>Login here</a></div>";
        } catch (PDOException $e) {
            echo "<div class='msg msg-error' style='display:block; margin-top:15px;'>Registration failed. Email might already exist.</div>";
        }
    }
    ?>

    <p style="text-align:center; margin-top: 25px; font-size: 14px; color: var(--text-muted);">
        Already have an account? <a href="login.php" style="color: var(--primary); text-decoration:none; font-weight:600;">Login here</a>
    </p>
</div>

</body>
</html>

<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $otp = trim($_POST['otp'] ?? '');

    if (!$email || empty($otp)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and OTP are required']);
        exit;
    }

    // Fetch OTP record
    $stmt = $pdo->prepare("SELECT * FROM otp_verification WHERE email = ?");
    $stmt->execute([$email]);
    $record = $stmt->fetch();

    if (!$record) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request. Please get a new OTP.']);
        exit;
    }

    // Check expiry
    if (strtotime($record['expiry_time']) < time()) {
        echo json_encode(['status' => 'error', 'message' => 'OTP has expired. Please resend.']);
        exit;
    }

    // Check max attempts
    if ($record['attempts'] >= 3) {
        echo json_encode(['status' => 'error', 'message' => 'Maximum attempts reached. Please resend a new OTP.']);
        exit;
    }

    // Verify OTP
    if ($record['otp'] === $otp) {
        // Success! Get user info to start session
        $stmt = $pdo->prepare("SELECT id, full_name, email, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $is_reset = isset($_POST['reset']) && $_POST['reset'] == '1';

            if (!$is_reset) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
            }
            
            // Clear OTP record
            $stmt = $pdo->prepare("DELETE FROM otp_verification WHERE email = ?");
            $stmt->execute([$email]);

            $redirect = 'student_dashboard.php';
            if ($user['role'] === 'employer') $redirect = 'employer_dashboard.php';
            if ($user['role'] === 'admin') $redirect = 'admin_dashboard.php';

            echo json_encode([
                'status' => 'success', 
                'message' => $is_reset ? 'Identity verified' : 'Login successful', 
                'redirect' => $is_reset ? null : $redirect,
                'user' => $is_reset ? null : ['name' => $user['full_name'], 'role' => $user['role']]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User account no longer exists.']);
        }
    } else {
        // Increment attempts
        $stmt = $pdo->prepare("UPDATE otp_verification SET attempts = attempts + 1 WHERE email = ?");
        $stmt->execute([$email]);
        
        $remaining = 2 - $record['attempts'];
        $msg = "Incorrect OTP. $remaining attempts remaining.";
        if ($remaining <= 0) $msg = "Maximum attempts reached. Please resend a new OTP.";
        
        echo json_encode(['status' => 'error', 'message' => $msg]);
    }
}
?>

<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Credentials correct, proceed to OTP
        echo json_encode([
            'status' => 'success', 
            'message' => 'Credentials verified. Sending OTP...',
            'trigger_otp' => true,
            'email' => $email
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
    }
}
?>

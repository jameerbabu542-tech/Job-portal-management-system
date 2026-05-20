<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $full_name = trim($_POST['full_name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if (empty($full_name) || !$email) {
        echo json_encode(['status' => 'error', 'message' => 'Please provide valid details']);
        exit;
    }

    try {
        // Check if email is already taken by someone else
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Email is already associated with another account']);
            exit;
        }

        // Update profile
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, location = ?, bio = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $phone, $location, $bio, $user_id]);

        // Update session
        $_SESSION['full_name'] = $full_name;

        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>

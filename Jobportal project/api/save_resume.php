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
    $education = trim($_POST['education'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $skills = trim($_POST['skills'] ?? '');

    try {
        $stmt = $pdo->prepare("INSERT INTO resumes (user_id, education, experience, skills) 
                               VALUES (?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE education = ?, experience = ?, skills = ?");
        $stmt->execute([$user_id, $education, $experience, $skills, $education, $experience, $skills]);

        echo json_encode(['status' => 'success', 'message' => 'Resume updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>

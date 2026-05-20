<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$student_id = $_SESSION['user_id'];
$job_id = $_POST['job_id'] ?? null;

if (!$job_id) {
    echo json_encode(['status' => 'error', 'message' => 'Job ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO saved_jobs (student_id, job_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE id=id");
    $stmt->execute([$student_id, $job_id]);

    echo json_encode(['status' => 'success', 'message' => 'Job saved successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

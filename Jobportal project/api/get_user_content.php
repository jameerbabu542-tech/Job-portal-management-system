<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'applied'; // applied, saved, responses

try {
    if ($type === 'applied') {
        $stmt = $pdo->prepare("SELECT j.*, u.full_name as company_name, 1 as applied 
                               FROM applications a 
                               JOIN jobs j ON a.job_id = j.id 
                               JOIN users u ON j.employer_id = u.id 
                               WHERE a.student_id = ? 
                               ORDER BY a.applied_at DESC");
        $stmt->execute([$user_id]);
    } elseif ($type === 'saved') {
        $stmt = $pdo->prepare("SELECT j.*, u.full_name as company_name, 
                               (SELECT COUNT(*) FROM applications WHERE job_id = j.id AND student_id = ?) as applied
                               FROM saved_jobs s 
                               JOIN jobs j ON s.job_id = j.id 
                               JOIN users u ON j.employer_id = u.id 
                               WHERE s.student_id = ? 
                               ORDER BY s.created_at DESC");
        $stmt->execute([$user_id, $user_id]);
    } elseif ($type === 'responses') {
        $stmt = $pdo->prepare("SELECT j.*, u.full_name as company_name, a.status as app_status, a.applied_at
                               FROM applications a 
                               JOIN jobs j ON a.job_id = j.id 
                               JOIN users u ON j.employer_id = u.id 
                               WHERE a.student_id = ? AND a.status != 'pending'
                               ORDER BY a.applied_at DESC");
        $stmt->execute([$user_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid type']);
        exit;
    }

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

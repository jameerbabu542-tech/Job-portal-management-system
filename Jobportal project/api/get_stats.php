<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stats = [];

try {
    if ($role === 'admin') {
        // Global stats for admin
        $stats['total_jobs'] = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
        $stats['total_applications'] = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
        $stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    } elseif ($role === 'employer') {
        // Total jobs posted
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE employer_id = ?");
        $stmt->execute([$user_id]);
        $stats['total_jobs'] = $stmt->fetch()['total'];

        // Total applications received
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = ?");
        $stmt->execute([$user_id]);
        $stats['total_applications'] = $stmt->fetch()['total'];

        // Shortlisted candidates
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = ? AND a.status = 'accepted'");
        $stmt->execute([$user_id]);
        $stats['shortlisted'] = $stmt->fetch()['total'];
    } else {
        // Total jobs applied
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ?");
        $stmt->execute([$user_id]);
        $stats['applied_jobs'] = $stmt->fetch()['total'];

        // Saved jobs
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM saved_jobs WHERE student_id = ?");
        $stmt->execute([$user_id]);
        $stats['saved_jobs'] = $stmt->fetch()['total'];
        
        // Response from employers (accepted/rejected)
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE student_id = ? AND status != 'pending'");
        $stmt->execute([$user_id]);
        $stats['responses'] = $stmt->fetch()['total'];
    }

    echo json_encode(['status' => 'success', 'data' => $stats]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

$student_id = $_SESSION['user_id'] ?? 0;

$keyword = $_GET['search'] ?? '';
$sector = $_GET['sector'] ?? '';
$location = $_GET['location'] ?? '';

// Build Query
$query = "SELECT j.*, u.full_name as company_name,
          (SELECT COUNT(*) FROM applications WHERE job_id = j.id AND student_id = ?) as applied
          FROM jobs j 
          JOIN users u ON j.employer_id = u.id 
          WHERE 1=1";

$params = [$student_id];

if ($keyword) {
    $query .= " AND (j.title LIKE ? OR j.skills LIKE ? OR j.description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

if ($sector) {
    // Exact match for sector
    $query .= " AND j.sector = ?";
    $params[] = $sector;
}

if ($location) {
    // Partial match for location
    $query .= " AND j.location LIKE ?";
    $params[] = "%$location%";
}

$query .= " ORDER BY j.created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $jobs]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>

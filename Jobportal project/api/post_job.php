<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['employer', 'admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$employer_id = ($_SESSION['role'] === 'admin') ? 99 : $_SESSION['user_id'];
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$sector = $_POST['sector'] ?? 'Tech';
$skills = $_POST['skills'] ?? '';
$location = $_POST['location'] ?? '';
$salary = $_POST['salary'] ?? '';
$experience = $_POST['experience'] ?? '';
$job_type = $_POST['job_type'] ?? 'Full-time';
$deadline = $_POST['deadline'] ?? null;

if (empty($title) || empty($description)) {
    echo json_encode(['status' => 'error', 'message' => 'Title and Description are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO jobs (employer_id, title, description, sector, skills, location, salary, experience, job_type, deadline) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$employer_id, $title, $description, $sector, $skills, $location, $salary, $experience, $job_type, $deadline]);

    echo json_encode(['status' => 'success', 'message' => 'Job posted successfully with sector classification!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

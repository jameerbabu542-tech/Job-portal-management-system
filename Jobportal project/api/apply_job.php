<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Only students can apply.']);
    exit;
}

$student_id = $_SESSION['user_id'];
$job_id = $_POST['job_id'] ?? null;

// Personal & Professional
$phone = $_POST['phone'] ?? '';
$experience_years = $_POST['experience_years'] ?? '';
$current_job = $_POST['current_job'] ?? '';
$native_place = $_POST['native_place'] ?? '';

// Education & Skills
$education = $_POST['education'] ?? '';
$cgpa = $_POST['cgpa'] ?? '';
$languages = $_POST['languages'] ?? '';

// Portfolio & Logistics
$linkedin_link = $_POST['linkedin_link'] ?? '';
$portfolio_link = $_POST['portfolio_link'] ?? '';
$expected_salary = $_POST['expected_salary'] ?? '';
$notice_period = $_POST['notice_period'] ?? '';
$cover_letter = $_POST['cover_letter'] ?? '';

if (!$job_id) {
    echo json_encode(['status' => 'error', 'message' => 'Job ID is required']);
    exit;
}

// Handle File Upload
$resume_path = '';
if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/resumes/';
    $file_ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
    $file_name = "resume_" . $student_id . "_" . time() . "." . $file_ext;
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
        $resume_path = 'uploads/resumes/' . $file_name;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload resume']);
        exit;
    }
}

try {
    // Check if already applied
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND student_id = ?");
    $stmt->execute([$job_id, $student_id]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Duplicate Application: You have already applied for this position.']);
        exit;
    }

    // Apply with full comprehensive details
    $sql = "INSERT INTO applications (
                job_id, student_id, phone, native_place, education, languages, 
                expected_salary, notice_period, portfolio_link, 
                experience_years, current_job, cgpa, cover_letter, linkedin_link,
                resume_path, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $job_id, $student_id, $phone, $native_place, $education, $languages,
        $expected_salary, $notice_period, $portfolio_link,
        $experience_years, $current_job, $cgpa, $cover_letter, $linkedin_link,
        $resume_path
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Application Suite Submitted Successfully! The employer has been notified.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>

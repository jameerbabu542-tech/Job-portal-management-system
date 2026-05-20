<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] === 'employer') {
    header('Location: employer_dashboard.php');
} else {
    header('Location: student_dashboard.php');
}
exit;
?>

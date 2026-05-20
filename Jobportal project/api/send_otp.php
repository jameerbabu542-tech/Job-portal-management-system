<?php
session_start();
require_once '../config/db.php';
require_once '../config/mail_config.php';

// Import PHPMailer classes (Assuming PHPMailer is in the 'includes' folder)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Note: You must have PHPMailer installed in the includes/PHPMailer folder
// If not installed, this will throw an error. 
// Standard path: includes/PHPMailer/src/PHPMailer.php, etc.

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'No account found with this email']);
        exit;
    }

    // Generate 6-digit OTP
    $otp = sprintf("%06d", mt_rand(0, 999999));
    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Check for cooldown (30 seconds)
    $stmt = $pdo->prepare("SELECT last_resend FROM otp_verification WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if ($row && (time() - strtotime($row['last_resend']) < 30)) {
        $wait = 30 - (time() - strtotime($row['last_resend']));
        echo json_encode(['status' => 'error', 'message' => "Please wait $wait seconds before resending"]);
        exit;
    }

    // Store OTP in database
    $stmt = $pdo->prepare("INSERT INTO otp_verification (email, otp, expiry_time, attempts, last_resend) 
                           VALUES (?, ?, ?, 0, CURRENT_TIMESTAMP) 
                           ON DUPLICATE KEY UPDATE otp = ?, expiry_time = ?, attempts = 0, last_resend = CURRENT_TIMESTAMP");
    $stmt->execute([$email, $otp, $expiry, $otp, $expiry]);

    // Send Email via PHPMailer
    $mail_path = '../includes/PHPMailer/src/';
    if (file_exists($mail_path . 'PHPMailer.php')) {
        require $mail_path . 'Exception.php';
        require $mail_path . 'PHPMailer.php';
        require $mail_path . 'SMTP.php';

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            //Recipients
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Job Portal OTP Verification';
            $mail->Body    = "Your OTP is: <b>$otp</b>. It will expire in 5 minutes.";
            $mail->AltBody = "Your OTP is: $otp. It will expire in 5 minutes.";

            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully to your email']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        // For development/demo if PHPMailer is missing, we still return success but log locally
        // In production, this should be an error or PHPMailer must be present.
        echo json_encode([
            'status' => 'success', 
            'message' => 'OTP generated (Simulated). Please check database table as PHPMailer was not found in includes folder.',
            'debug_otp' => $otp // REMOVE THIS IN PRODUCTION
        ]);
    }
}
?>

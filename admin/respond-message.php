<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include('../includes/db.php');

header('Content-Type: application/json');

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($_POST['id']) &&
    !empty($_POST['to_email']) &&
    !empty($_POST['subject']) &&
    !empty($_POST['message'])
) {
    $id = intval($_POST['id']);
    $to = filter_var($_POST['to_email'], FILTER_VALIDATE_EMAIL);
    $subject = trim($_POST['subject']);
    $body = trim($_POST['message']);

    if (!$to) {
        echo json_encode(['success' => false, 'error' => 'Invalid email address']);
        exit;
    }

    $mail = new PHPMailer(true);
    try {
        // SMTP configuration (make sure these constants are defined!)
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;

        // Email content
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br(htmlspecialchars($body, ENT_QUOTES | ENT_HTML5));
        $mail->AltBody = $body;

        $mail->send();

        // Mark message as responded by id
        $stmt = $conn->prepare("UPDATE contact_messages SET responded_at = NOW() WHERE id = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Database error']);
            exit;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        echo json_encode(['success' => false, 'error' => 'Failed to send email']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

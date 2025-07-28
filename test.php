<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'Penixkujs@gmail.com';
    $mail->Password = 'mjrw tuub khwg osdk';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('Penixkujs@gmail.com', 'SBHD');
    $mail->addAddress('cjbugero@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email.';

    $mail->SMTPDebug = 2; // DEBUG: turn this on
    $mail->Debugoutput = 'html';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}

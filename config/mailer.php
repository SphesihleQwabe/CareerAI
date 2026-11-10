<?php
/**
 * CareerAI - Email Sending Helper
 */

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_mail($toEmail, $toName, $subject, $htmlBody)
{
    $username = env('GMAIL_USERNAME');
    $password = env('GMAIL_APP_PASSWORD');
    $fromName = env('MAIL_FROM_NAME', 'CareerAI');

    if (!$username || !$password) {
        return 'Email configuration is missing. Please check the .env file.';
    }

    $mail = new PHPMailer(true);

    try {
        // Enable verbose debug output
        // 0 = off, 1 = client messages, 2 = client and server messages
        $mail->SMTPDebug = 0; 

        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $username;
        $mail->Password   = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Encoding
        $mail->CharSet = 'UTF-8';

        // From / To
        $mail->setFrom($username, $fromName);
        $mail->addAddress($toEmail, $toName ?: $toEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return 'Mail could not be sent. Error: ' . $mail->ErrorInfo;
    }
}
<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

function send_confirmation_email(string $email, string $name, string $confirmationLink): array
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = APP_EMAIL;
        $mail->Password = APP_EMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom(APP_EMAIL, APP_EMAIL_FROM_NAME);
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Confirm your Civil Service Reviewer registration';
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; color:#172554; line-height:1.6;">
                <h2 style="color:#075985;">Civil Service Exam Reviewer</h2>
                <p>Hello <strong>' . htmlspecialchars($name) . '</strong>,</p>
                <p>Please confirm your registration to access the online reviewer.</p>
                <p><a href="' . htmlspecialchars($confirmationLink) . '" style="background:#0f5ea8;color:white;padding:12px 18px;text-decoration:none;border-radius:8px;display:inline-block;">Confirm Registration</a></p>
                <p>If the button does not work, open this link:</p>
                <p><a href="' . htmlspecialchars($confirmationLink) . '">' . htmlspecialchars($confirmationLink) . '</a></p>
            </div>';
        $mail->AltBody = "Hello {$name}, confirm your registration here: {$confirmationLink}";
        $mail->send();
        return ['ok' => true, 'message' => 'Confirmation email sent.'];
    } catch (Exception $e) {
        return ['ok' => false, 'message' => $mail->ErrorInfo ?: $e->getMessage()];
    }
}

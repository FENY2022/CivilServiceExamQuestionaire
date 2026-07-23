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
        $rawError = $mail->ErrorInfo ?: $e->getMessage();
        return [
            'ok' => false,
            'message' => friendly_mail_error($rawError),
            'raw_error' => $rawError,
        ];
    }
}

function friendly_mail_error(string $error): string
{
    $normalized = strtolower($error);

    if (str_contains($normalized, 'authenticate') || str_contains($normalized, 'username and password not accepted')) {
        return 'Gmail login failed. The Gmail app password may have expired or been revoked. Please generate a new app password in your Google Account settings.';
    }

    if (str_contains($normalized, 'smtp connect') || str_contains($normalized, 'could not connect') || str_contains($normalized, 'could not access smtp host')) {
        return 'Could not connect to Gmail SMTP server. Please check the internet connection, XAMPP network access, and Gmail SMTP settings.';
    }

    if (str_contains($normalized, 'invalid address') || str_contains($normalized, 'invalid email')) {
        return 'The recipient email address is invalid. Please check the email address and try again.';
    }

    if (str_contains($normalized, 'extension missing') || str_contains($normalized, 'openssl')) {
        return 'Email encryption support is not available. Please enable the OpenSSL extension in PHP/XAMPP.';
    }

    if (str_contains($normalized, 'timed out') || str_contains($normalized, 'timeout')) {
        return 'The email request timed out. Please try again or check the server connection.';
    }

    return 'Email could not be sent due to a server error. Please try again later.';
}

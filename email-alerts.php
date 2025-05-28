<?php
// email-alerts.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Safe access to environment variables with optional default.
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}

/**
 * Ensures required environment keys are present or halts with error.
 *
 * @param array $keys
 * @return void
 */
function validateRequiredEnv(array $keys): void
{
    foreach ($keys as $key) {
        if (empty(env($key))) {
            error_log("❌ Missing required environment variable: $key");
            exit("Configuration error: missing $key");
        }
    }
}

/**
 * Sends an email alert if the appointments list is empty.
 *
 * @param array $apiData      The data sent in the API request.
 * @param array $responseData The decoded API response.
 * @param array $queryData    The original query string (e.g., $_GET).
 *
 * @return void
 */
function sendEmptyAppointmentsAlert(array $apiData, array $responseData, array $queryData = []): void
{
    $appointments = $responseData['appointments'] ?? [];

    if (!is_array($appointments) || !empty($appointments)) {
        return; // Appointments exist — nothing to alert
    }

    error_log("⚠️ Empty appointment list received from API.");

    // Validate required ENV values
    validateRequiredEnv([
        'SMTP_HOST',
        'SMTP_PORT',
        'SMTP_USERNAME',
        'SMTP_PASSWORD',
        'SMTP_FROM',
        'SMTP_FROM_NAME'
    ]);

    $mail = new PHPMailer(true);

    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host       = env('SMTP_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('SMTP_USERNAME');
        $mail->Password   = env('SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = (int)env('SMTP_PORT');

        // Email metadata
        $fromEmail = env('SMTP_FROM');
        $fromName  = env('SMTP_FROM_NAME');
        $mail->setFrom($fromEmail, $fromName ?: $fromEmail);

        // Recipient (same as sender, or override here)
        $mail->addAddress($fromEmail); // You can replace with ALERT_RECIPIENT

        // Email content
        $mail->CharSet = 'UTF-8';  // 👈 This line fixes the weird character issue

        $mail->Subject = '🚨 Prospect Was Unable to Schedule an Appointment 🚨';
        $mail->isHTML(false);

        $mail->Body =
            "The ListAvailableAppointments API returned an empty list.\n\n" .
            "Timestamp:\n" . date('Y-m-d H:i:s') . "\n\n" .
            "Query String:\n" . json_encode($queryData, JSON_PRETTY_PRINT) . "\n\n" .
            "Request Payload:\n" . json_encode($apiData, JSON_PRETTY_PRINT) . "\n\n" .
            "API Response:\n" . json_encode($responseData, JSON_PRETTY_PRINT);

        $mail->send();
        error_log("✅ Empty appointment alert email sent.");
    } catch (Exception $e) {
        error_log("❌ Failed to send alert email: " . $mail->ErrorInfo);
    }
}

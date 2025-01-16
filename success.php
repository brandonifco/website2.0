<?php
/**
 * Processes and confirms a reserved appointment.
 *
 * This file handles the final step of the appointment scheduling process. It
 * performs the ReserveAppointment API call to confirm the user's selected
 * appointment. Upon successful reservation, it displays a confirmation message
 * and sends email notifications to both the user and the site administrator.
 *
 * PHP Version 7.4+
 *
 * @category Xtreme_Scheduler_API_Integration
 * @package  Xtreme_Scheduler_API_Integration_Tools
 * @author   Brandon Baker <brandonifco@gmail.com>
 * @license  Creative Commons Attribution-NonCommercial 4.0 International
 *             (CC BY-NC 4.0)
 * @link     https://scheduler.xtremecrm.com/
 *
 * Key Features:
 * - Processes the ReserveAppointment API call to confirm the appointment.
 * - Sends confirmation emails to both the user and the site administrator.
 * - Displays a detailed confirmation message including appointment details,
 *   address, and contact information.
 *
 * Dependencies:
 * - `header.php` and `footer.php` for consistent site-wide structure.
 * - `config.php` for API configuration and credentials.
 * - External ReserveAppointment API for confirming appointments.
 *
 * Notes:
 * - Ensure the ReserveAppointment API endpoint is functional and credentials in
 *   `config.php` are up-to-date.
 * - Validate all user-provided data to prevent invalid API calls or security
 *   vulnerabilities.
 * - Test email functionality regularly to ensure notifications are delivered
 *   correctly.
 */

// Include necessary configuration files
require_once './xtreme_api_config.php';

// Extract form data
$postData = $_POST;

if (!isset($postData['entityId']) 
    || !isset($postData['dealerCode']) || !isset($postData['appointment'])
) {
    echo "Missing required parameters.";
    echo $postData['entityId'];
    echo $postData['dealerCode'];
    echo $postData['appointment'];
    exit;
}

// Prepare data for ReserveAppointment API call
list($start, $end) = explode(',', $postData['appointment']);
$reserveData = [
    'dealerCode' => $postData['dealerCode'],
    'entityId' => $postData['entityId'],
    'start' => $start,
    'end' => $end
];

$url = $config['apiBaseUrl'] . '/reserveappointment';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt(
    $ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-ApiKey: ' . $config['apiKey']
    ]
);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($reserveData));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo "Failed to reserve the appointment.";
    exit;
}

$responseData = json_decode($response, true);
if (!$responseData['success']) {
    echo "Reservation failed: " . htmlspecialchars($responseData['message']);
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$email_from = $_ENV['SMTP_FROM'] ?? null;
if (!is_string($email_from)) {
    echo "Invalid SMTP_FROM value.";
    exit;
}

if (!filter_var($email_from, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address in SMTP_FROM: {$email_from}";
    exit;
}

$businessPhone = $settings['contactInfo']['phone'] ?? '';
$toAdmin = "brandonifco@gmail.com";
$toUser = $postData['emailAddress'];

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USERNAME'];
    $mail->Password = $_ENV['SMTP_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $_ENV['SMTP_PORT'];

    $mail->setFrom($email_from, $_ENV['SMTP_FROM_NAME']);

    // Admin Email
    $mail->addAddress($toAdmin, 'Admin');
    $mail->isHTML(true);
    $mail->Subject = 'New Appointment Reserved';
    $mail->Body = "<p>New Appointment Reserved:</p>" .
                  "<p><strong>Details:</strong><br>" .
                  "<strong>Date:</strong> " . date('l, F j, Y', strtotime($start)) . "<br>" .
                  "<strong>Time:</strong> " . date('g:i A', strtotime($start)) . "</p>";
    $mail->send();

    // User Email
    $mail->clearAddresses();
    $mail->addAddress($toUser);
    $mail->Subject = 'Appointment Confirmation';
    $mail->Body = "<p>Dear Customer,</p>" .
                  "<p>Your appointment has been successfully reserved:</p>" .
                  "<ul><li><strong>Date:</strong> " . date('l, F j, Y', strtotime($start)) . "</li>" .
                  "<li><strong>Time:</strong> " . date('g:i A', strtotime($start)) . "</li></ul>" .
                  "<p>If you have any questions, feel free to contact us at {$businessPhone}.</p>" .
                  "<p>Thank you!</p>";
    $mail->send();
} catch (Exception $e) {
    echo "Error sending email: " . $mail->ErrorInfo;
    exit;
}

// Display confirmation message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/styles.css">
    <title>Appointment Confirmation</title>
</head>
<body>
    <?php require './header.php'; ?>

    <div class="content-container">
        <h1>Appointment Confirmed</h1>
        <section class="reg">
        <p>Thank you for scheduling your appointment! Here are your details:</p>
        <section class="confirmed">
        <ul>
            <li>Date:
            <?php echo htmlspecialchars(date('l, F j, Y', strtotime($start))); ?>
            </li>
            <li>Time:
            <?php echo htmlspecialchars(date('g:i A', strtotime($start))); ?></li>
        </ul>
        </section><p>If you have any questions, feel free to call us at</p>
        <p data-content="contact.contactInformation.phoneNumber"></p>
        </section>
    </div>

    <?php require './footer.php'; ?>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['reservation_attempts'])) {
    $_SESSION['reservation_attempts'] = 0;
}
$_SESSION['reservation_attempts']++;

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

if (
    empty($postData['entityId']) ||
    empty($postData['dealerCode']) ||
    empty($postData['appointment'])
) {
    http_response_code(400); // Let clients know it's a bad request
    echo "Missing or invalid required parameters.";
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
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        'Content-Type: application/json',
        'X-ApiKey: ' . $config['apiKey']
    ]
);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($reserveData));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$responseData = json_decode($response, true);
if (!$responseData['success']) {
    http_response_code(400); // Bad request or invalid data
    echo "Reservation failed: " . htmlspecialchars($responseData['message']);
    exit;
}

if ($httpCode !== 200 || !$response || !$responseData['success']) {
    $errorMessage = $responseData['message'] ?? 'Unknown API failure';
    error_log("❌ Reservation failed: " . $errorMessage);

    if ($_SESSION['reservation_attempts'] < 2) {
        // Redirect back with error flag
        header("Location: listappointments.php?retry=1");
        exit;
    }

    // Clear the attempt counter after second failure
    $_SESSION['reservation_attempts'] = 0;

    // Send admin/user emails
    require_once './email-alerts.php';
    sendSchedulerFailureEmails($postData, $errorMessage, $reserveData);

    // Show fallback message
    echo "<p>There seems to be a problem with the scheduler. Someone from our office will contact you shortly.</p>";
    exit;
}



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$requiredEnvVars = [
    'SMTP_FROM',
    'SMTP_FROM_NAME',
    'SMTP_HOST',
    'SMTP_USERNAME',
    'SMTP_PASSWORD',
    'SMTP_PORT',
];

foreach ($requiredEnvVars as $var) {
    if (empty($_ENV[$var])) {
        http_response_code(500);
        echo "Missing required environment variable: {$var}";
        exit;
    }
}

$email_from = $_ENV['SMTP_FROM'] ?? null;
if (!is_string($email_from)) {
    http_response_code(500); // Internal server config issue
    echo "Invalid SMTP_FROM value.";
    exit;
}

if (!filter_var($email_from, FILTER_VALIDATE_EMAIL)) {
    http_response_code(500);
    echo "Invalid email address in SMTP_FROM: {$email_from}";
    exit;
}
$settingsJson = file_get_contents(__DIR__ . '/content.json');
$settings = json_decode($settingsJson, true);
$businessPhone = $settings['contact']['contactInformation']['phoneNumber'] ?? null;
$escapedPhone = !empty($businessPhone)
    ? htmlspecialchars($businessPhone)
    : 'our main office line (see website)';



$toAdmin = "admin@glcontainmenttraining.com";
$toUser = filter_var($postData['emailAddress'], FILTER_VALIDATE_EMAIL);
if (!$toUser) {
    echo "Invalid email address provided.";
    exit;
}
$customerFirstName = $postData['firstName'] ?? 'Not provided';
$customerLastName = $postData['lastName'] ?? 'Not provided';
$fullName = "{$customerLastName}, {$customerFirstName}";
$customerName = htmlspecialchars($fullName ?? 'Not provided');
$customerEmail = htmlspecialchars($postData['emailAddress'] ?? 'Not provided');
$customerPhone = htmlspecialchars($postData['phoneNumber'] ?? 'Not provided');
$customerAddress = htmlspecialchars($postData['address'] ?? 'Not provided');
$customerCity = htmlspecialchars($postData['city'] ?? 'Not provided');
$customerState = htmlspecialchars($postData['state'] ?? 'Not provided');
$customerZip = htmlspecialchars($postData['zip'] ?? 'Not provided');
$cityStateZip = "{$customerCity}, {$customerState} {$customerZip}";

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
    $mail->Body =   "<p>New Appointment Reserved:</p>" .
        "<p><strong>Appointment Details:</strong><br>" .
        "<strong>Date:</strong> " . date('l, F j, Y', strtotime($start)) . "<br>" .
        "<strong>Time:</strong> " . date('g:i A', strtotime($start)) . "</p>" .
        "<p><strong>Customer Details:</strong><br>" .
        "<strong>Name:</strong> {$customerName}<br>" .
        "<strong>Email:</strong> {$customerEmail}<br>" .
        "<strong>Phone:</strong> {$customerPhone}<br>" .
        "<strong>Address:<br></strong> {$customerAddress}<br> {$cityStateZip}</p>";
    $mail->send();

    // User Email
    $mail->clearAddresses();
    $mail->addAddress($toUser);
    $mail->Subject = 'Free Consultation with Great Lakes Containment and Training Confirmed!';
    $mail->Body = "<p>Dear {$customerFirstName},</p>" .
        "<p>Your appointment has been successfully reserved:</p>" .
        "<ul><li><strong>Date:</strong> " . date('l, F j, Y', strtotime($start)) . "</li>" .
        "<li><strong>Time:</strong> " . date('g:i A', strtotime($start)) . "</li></ul>" .
        "<p>If you have any questions, feel free to contact us at {$businessPhone}.</p>" .
        "<p>Thank you!</p>";
    $mail->send();
} catch (Exception $e) {
    http_response_code(500); // Internal server error
    echo "Error sending email: " . htmlspecialchars($mail->ErrorInfo);
    exit;
}

function sendSchedulerFailureEmails(array $user, string $errorMessage, array $attemptedReservation): void
{
    $toAdmin = "admin@glcontainmenttraining.com";
    $toUser  = filter_var($user['emailAddress'], FILTER_VALIDATE_EMAIL);

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USERNAME'];
    $mail->Password = $_ENV['SMTP_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $_ENV['SMTP_PORT'];
    $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);
    $mail->isHTML(true);

    // Admin email
    $mail->addAddress($toAdmin);
    $mail->Subject = '⚠️ Scheduler Failure - Appointment Could Not Be Reserved';
    $mail->Body = "
        <p><strong>Reservation failed for:</strong></p>
        <ul>
            <li>Name: " . htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) . "</li>
            <li>Email: " . htmlspecialchars($user['emailAddress']) . "</li>
            <li>Phone: " . htmlspecialchars($user['phoneNumber']) . "</li>
            <li>Address: " . htmlspecialchars($user['address']) . ", " . htmlspecialchars($user['city']) . ", " . htmlspecialchars($user['state']) . " " . htmlspecialchars($user['zip']) . "</li>
        </ul>
        <p><strong>Attempted Appointment:</strong><br>
        Start: " . htmlspecialchars($attemptedReservation['start']) . "<br>
        End: " . htmlspecialchars($attemptedReservation['end']) . "</p>
        <p><strong>API Error:</strong> " . htmlspecialchars($errorMessage) . "</p>
    ";
    $mail->send();

    // User email
    $mail->clearAddresses();
    if ($toUser) {
        $mail->addAddress($toUser);
        $mail->Subject = 'We’re Working On Your Appointment';
        $mail->Body = "
            <p>Dear " . htmlspecialchars($user['firstName']) . ",</p>
            <p>We attempted to reserve your selected appointment, but the scheduler encountered an issue.</p>
            <p>Our team has been notified and will contact you shortly to finalize your booking.</p>
            <p>We’re sorry for the inconvenience!</p>
            <p>- Great Lakes Containment & Training</p>
        ";
        $mail->send();
    }
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
    <meta name="description" content="Thank you! Your consultation request has been received by Great Lakes Containment & Training. We will confirm your appointment shortly.">
    <link rel="canonical" href="https://glcontainmenttraining.com/success.php">
    <meta name="robots" content="noindex">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
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
            </section>
            <p>
                If you have any questions, feel free to call us at
                <?php if (!empty($businessPhone)): ?>
                    <?php echo $businessPhone; ?>
                <?php else: ?>
                    <span data-content="contact.contactInformation.phoneNumber"></span>
                <?php endif; ?>
            </p>

        </section>
    </div>

    <?php require './footer.php'; ?>
</body>

</html>
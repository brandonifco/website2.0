<?php

/**
 * Schedule.php
 *
 * This script renders and handles submission of a consultation scheduling form.
 * It validates input data, sends an API request to retrieve a unique Entity ID,
 * and redirects users to a success page upon success.
 *
 * PHP Version 7.4+
 *
 * @category Xtreme_Scheduler_API_Integration
 * @package  Xtreme_Scheduler_API_Integration
 * @author   Brandon Baker <brandonifco@gmail.com>
 * @license  Creative Commons Attribution-NonCommercial 4.0 International
 *             (CC BY-NC 4.0)
 * @link     https://scheduler.xtremecrm.com/
 *
 * Inputs:
 * - firstName: User's first name (required)
 * - lastName: User's last name (required)
 * - address: User's address (required)
 * - city: User's city (required)
 * - state: User's state (required)
 * - zip: User's zip code (required)
 *
 * Outputs:
 * - Redirects to success.php with query parameters:
 *   - entityId: Retrieved Entity ID from the API.
 *   - firstName: User's first name.
 *   - lastName: User's last name.
 */


require_once 'xtreme_api_config.php'; // Include configuration file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = [
        'dealerCode' => $config['dealerCode'],
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'state' => $_POST['state'],
        'zip' => $_POST['zip'],
        'phoneNumber' => $_POST['phoneNumber'],
        'emailAddress' => $_POST['emailAddress'],
        'contactMe' => isset($_POST['contactMe']) ? filter_var($_POST['contactMe'], FILTER_VALIDATE_BOOLEAN) : false,
        'source' => $_POST['source'] ?? '',
        'promotion' => $_POST['promotion'] ?? '',
        'ipAddress' => $_SERVER['REMOTE_ADDR'],
        'comments' => $_POST['comments'] ?? ''
    ];

    $requiredFields = ['firstName', 'lastName', 'address', 'city', 'state', 'zip', 'phoneNumber', 'emailAddress'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errorMessage = "Missing required field: $field";
            break;
        }
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['apiBaseUrl'] . '/getentityid');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [
            'Content-Type: application/json',
            'X-ApiKey: ' . $config['apiKey']
        ]
    );

    error_log("Sending data to API: " . json_encode($postData));

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseBody = null;
    if ($response !== false) {
        $responseBody = json_decode($response, true);

        // Log API response regardless of error
        error_log("📬 API Response:");
        error_log("HTTP Code: $httpCode");
        error_log("cURL Error: " . ($curlError ?: 'None'));
        error_log("Raw Response: " . substr($response, 0, 500)); // limit if needed
        error_log("Decoded Entity ID: " . ($responseBody['entityId'] ?? 'N/A'));

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errorMessage = "Invalid JSON response: " . json_last_error_msg();
        }
    }

    if (isset($errorMessage)) {
        error_log("❌ API Error Log: " . json_encode([
            'message' => $errorMessage,
            'httpCode' => $httpCode,
            'rawResponse' => $response,
            'decodedResponse' => $responseBody,
            'postData' => $postData
        ]));
    }

    if ($response === false) {
        $errorMessage = "Request failed. cURL error: $curlError";
    } elseif ($httpCode === 500) {
        $errorMessage = "That ZIP code is outside of our area of coverage, please try again.";
    } elseif ($httpCode !== 200) {
        $errorMessage = "API returned HTTP $httpCode. Response: $response";
    } elseif (json_last_error() !== JSON_ERROR_NONE) {
        $errorMessage = "Invalid JSON response from API.";
    } elseif (!isset($responseBody['entityId'])) {
        $errorMessage = "Missing entityId in API response.";
    } else {
        // Success
        $entityId = $responseBody['entityId'];
        $queryString = http_build_query(array_merge($_POST, ['entityId' => $entityId]));
        header("Location: listappointments.php?$queryString&dealerCode={$config['dealerCode']}");
        exit;
    }

    // Final fallback — only set if no earlier error message
    if (!isset($errorMessage)) {
        $errorMessage = "Unknown error occurred.";
    }
    error_log("❌ API Error: $errorMessage | HTTP $httpCode | Response: $response");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule A Consultation</title>
    <meta name="description" content="Schedule a free in‑home consultation with Great Lakes Containment & Training—choose your preferred date and time to start protecting your pet today.">
    <link rel="canonical" href="https://glcontainmenttraining.com/schedule.php">

    <link rel="stylesheet" href="css/styles.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
</head>

<body>
    <?php require 'header.php'; ?>

    <div class="content-container">
        <h1>Please tell us about yourself:</h1>
        <?php if (isset($successMessage)) : ?>
            <div class="success-message">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>
        <section class="reg">
            <?php if (isset($errorMessage)) : ?>
                <div class="error-message">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>
            <form id="contact-form" method="POST" action="">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required>

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required>

                <label for="state">State:</label>
                <input type="text" id="state" name="state" required>

                <label for="zip">ZIP Code:</label>
                <input type="text" id="zip" name="zip" required>

                <label for="phoneNumber">Phone Number:</label>
                <input type="tel" id="phoneNumber" name="phoneNumber" required>

                <label for="emailAddress">Email Address:</label>
                <input type="email" id="emailAddress" name="emailAddress" required>

                <label for="comments">Comments (Optional):</label>
                <textarea id="comments" name="comments"></textarea>
                <div id="cta-section">
                    <button type="submit" class="cta-button">LIST AVAILABLE APPOINTMENTS</button>
                </div>
            </form>
        </section>
    </div>

    <?php require 'footer.php'; ?>
</body>

</html>
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

// Check if form is submitted
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
        'contactMe' => filter_var($_POST['contactMe'], FILTER_VALIDATE_BOOLEAN),
        'source' => $_POST['source'] ?? '',
        'promotion' => $_POST['promotion'] ?? '',
        'ipAddress' => $_SERVER['REMOTE_ADDR'],
        'comments' => $_POST['comments'] ?? ''
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['apiBaseUrl'] . '/getentityid');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-ApiKey: ' . $config['apiKey']
        ]
    );

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseBody = json_decode($response, true);

    if ($httpCode === 200 && $response) {
        $responseData = json_decode($response, true);
        if (isset($responseData['entityId'])) {
            $entityId = $responseData['entityId'];

            // Build query string including all form data and entityId
            $queryString = http_build_query(
                array_merge(
                    $_POST,
                    ['entityId' => $responseData['entityId']]
                )
            );

            // Redirect to listAppointments.php with the generated query string
            header(
                "Location: listappointments.php?$queryString" 
                . '&dealerCode=' . $config['dealerCode']
            );
            exit;
        }
    }

    if ($httpCode === 200 && isset($responseBody['entityId'])) {
        $entityId = $responseBody['entityId'];
        $successMessage = "Entity ID retrieved successfully: $entityId";
    } else {
        $errorMessage = "Failed to retrieve Entity ID. Error: " 
        . ($responseBody['message'] ?? 'Unknown error.');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule A Consultation</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="content-container">
        <h1>Please tell us about yourself:</h1>
        <?php if (isset($successMessage)) : ?>
            <div class="success-message">
                <?php htmlspecialchars($successMessage) ?>
            </div>
        <?php elseif (isset($errorMessage)) : ?>
            <div class="error-message">
                <?php htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>
            <section class="reg">
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

            <button type="submit">List Available Appointments</button>
        </form>
            </section>
    </div>

    <?php require 'footer.php'; ?>
</body>
</html>
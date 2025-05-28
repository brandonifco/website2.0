<?php

/**
 * Appointment Selection Script
 * 
 * This script displays available appointment slots by interacting with the
 * Xtreme Scheduler API. It processes a GET request containing the `dealerCode`
 * and `entityId`, calls the `ListAvailableAppointments` endpoint, and presents 
 * the results in a date-grouped format for user selection.
 *
 * PHP Version: 7.4+
 * 
 * Responsibilities:
 * - Loads environment variables from `.env` using `vlucas/phpdotenv`
 * - Imports API configuration and alert email logic
 * - Validates presence of required query parameters (`dealerCode`, `entityId`)
 * - Prepares and sends a POST request to the `listavailableappointments` API
 * - Decodes and verifies the response structure
 * - Optionally triggers a fallback email alert if no appointments are returned
 * - Groups available appointments by date and formats them for frontend display
 * 
 * Dependencies:
 * - `Dotenv` package for loading environment configuration
 * - `xtreme_api_config.php` for API keys and base URL
 * - `email-alerts.php` for handling alerts when appointment data is missing
 * - `header.php` and `footer.php` for page layout consistency
 * 
 * Input (via query string):
 * - `dealerCode` (string): Unique dealer identifier required by the API
 * - `entityId` (string): Customer's unique entity ID required by the API
 * 
 * Output:
 * - A webpage rendering a user-selectable list of appointment time slots
 * - If no appointments are available, a message informs the user
 * - If API fails, a generic fallback message is shown without exposing details
 *
 * Example usage:
 * https://example.com/listappointments.php?dealerCode=123&entityId=abc456
 * 
 * Security Considerations:
 * - Input is validated for presence only; consider adding format checks
 * - Avoids exposing API errors to the end user
 * 
 * @author Brandon Baker
 * @license Creative Commons Attribution-NonCommercial 4.0 International
 * @link https://scheduler.xtremecrm.com/
 */

// Load Composer's autoloader (used for external packages like vlucas/phpdotenv)
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables from .env file into $_ENV and getenv()
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load API configuration settings and helper functions
require_once './xtreme_api_config.php'; // Contains $config['apiKey'], ['apiBaseUrl'], etc.
require_once './email-alerts.php';      // Provides sendEmptyAppointmentsAlert()

// Capture query parameters from the URL (e.g., ?entityId=...&dealerCode=...)
$queryData = $_GET;

// Validate that both required parameters are provided
if (!isset($queryData['entityId']) || !isset($queryData['dealerCode'])) {
    echo "Missing required parameters.";
    exit; // Stop script execution if required inputs are missing
}

// Build the data payload for the API request
$apiData = [
    'dealerCode' => $queryData['dealerCode'], // Unique identifier for the dealer
    'entityId'   => $queryData['entityId']    // Unique identifier for the customer/entity
];

// Compose full API endpoint URL for listing available appointments
$url = $config['apiBaseUrl'] . '/listavailableappointments';

// Initialize cURL session
$ch = curl_init($url);

// Configure cURL options for a POST request with JSON payload
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as string
curl_setopt($ch, CURLOPT_POST, true);           // Use POST method
curl_setopt($ch, CURLOPT_HTTPHEADER, [          // Set headers for JSON and API key
    'Content-Type: application/json',
    'X-ApiKey: ' . $config['apiKey']
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData)); // JSON-encode POST body

// Execute the API request
$response = curl_exec($ch);

// Get HTTP status code from the response
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close the cURL session
curl_close($ch);

// Decode the JSON response into a PHP array
$responseData = json_decode($response, true);

// Optional: trigger alert if appointments array is unexpectedly empty or malformed
if (is_array($apiData ?? null) && is_array($responseData ?? null)) {
    sendEmptyAppointmentsAlert($apiData, $responseData, $_GET);
}

// Handle request failure: invalid status code, no response, or JSON decoding issues
if ($httpCode !== 200 || !$response || json_last_error() !== JSON_ERROR_NONE) {
    $errorMessage = "Failed to retrieve available appointments.";

    // Append JSON decoding error message if applicable
    if (json_last_error() !== JSON_ERROR_NONE) {
        $errorMessage .= " JSON error: " . json_last_error_msg();
    }

    // Display generic fallback error to user
    echo "An error occurred while contacting the scheduler API. Someone from the office will reach out to you shortly.";
    exit;
}

// Safely extract appointments array from response, defaulting to empty if missing
$appointments = $responseData['appointments'] ?? [];

// Group the appointments by human-readable date for cleaner display
$groupedAppointments = [];

foreach ($appointments as $appointment) {
    // Format date as "Monday, January 1, 2025"
    $date = date('l, F j, Y', strtotime($appointment['start']));

    // Format time range as "1:00 PM - 1:30 PM"
    $time = date('g:i A', strtotime($appointment['start']))
        . ' - ' . date('g:i A', strtotime($appointment['end']));

    // Store grouped data as a subarray for each date
    $groupedAppointments[$date][] = [
        'time'  => $time,
        'value' => $appointment['start'] . ',' . $appointment['end'] // Preserve raw values for POST
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Basic character encoding and responsive layout support -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title for browser tab and search engine previews -->
    <title>Choose an Appointment</title>

    <!-- SEO Description for search engines and social sharing -->
    <meta name="description"
        content="Admin view—see all scheduled consultations and service appointments for Great Lakes Containment & Training. Secure, up‑to‑date list.">

    <!-- Canonical URL for SEO to avoid duplicate content issues -->
    <link rel="canonical" href="https://glcontainmenttraining.com/listappointments.php">

    <!-- Prevent indexing by search engines (e.g., for staging or private admin pages) -->
    <meta name="robots" content="noindex">

    <!-- JavaScript function to update the "Selected Appointment" field when a radio button is clicked -->
    <script>
        function updateSelectedAppointment(details) {
            const selectedDetailsField = document.getElementById('selected-appointment-details');
            selectedDetailsField.value = details;
        }
    </script>
</head>

<body>
    <!-- Include shared header (e.g., logo, navigation, styling hooks) -->
    <?php require './header.php'; ?>
    <?php if (isset($_GET['retry'])): ?>
        <p class="alert warning">Sorry, the selected appointment was no longer available. Please choose a different time.</p>
    <?php endif; ?>

    <!-- Main content container for page layout -->
    <div class="content-container">
        <h1>Select an Appointment</h1>

        <!-- Main appointment selection section -->
        <section class="reg">
            <?php if (empty($groupedAppointments)) : ?>
                <!-- Message shown when no appointments are available -->
                <p>No available appointments at this time. Someone will reach out to you for scheduling.</p>
            <?php else: ?>
                <!-- Appointment selection form -->
                <form action="success.php" method="POST" class="appointment-form">

                    <!-- Retain original query parameters in hidden fields to persist data across requests -->
                    <?php foreach ($queryData as $key => $value): ?>
                        <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                            value="<?php echo htmlspecialchars($value); ?>">
                    <?php endforeach; ?>

                    <!-- Display available appointment options, grouped by date -->
                    <div class="appointment-options">
                        <?php foreach ($groupedAppointments as $date => $times): ?>
                            <div class="appointment-date">
                                <!-- Group header showing the date -->
                                <h2><?php echo htmlspecialchars($date); ?></h2>

                                <?php foreach ($times as $time): ?>
                                    <div class="appointment-option">
                                        <!-- Radio button for each time slot -->
                                        <label>
                                            <input type="radio" name="appointment"
                                                value="<?php echo htmlspecialchars($time['value']); ?>"
                                                onchange="updateSelectedAppointment('<?php echo htmlspecialchars($date . ' at ' . $time['time']); ?>')">
                                            <!-- Human-readable time slot -->
                                            <span class="appointment-time">
                                                <?php echo htmlspecialchars($time['time']); ?>
                                            </span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Display of the selected appointment time (read-only text field) -->
                    <div style="margin-top: 20px;">
                        <label for="selected-appointment-details">Selected Appointment:</label>
                        <input type="text" id="selected-appointment-details" name="selectedAppointmentDetails" readonly
                            style="width: 100%; margin-bottom: 20px;">
                    </div>

                    <!-- Form submission button -->
                    <button type="submit" class="cta-button">CHOOSE APPOINTMENT</button>
                </form>
            <?php endif; ?>
        </section>
    </div>

    <!-- Include shared footer (e.g., contact info, links, copyright) -->
    <?php require './footer.php'; ?>
</body>

</html>
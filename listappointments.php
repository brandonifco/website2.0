<?php
/**
 * Displays available appointment slots for selection.
 *
 * This file retrieves available appointment slots using the 
 * ListAvailableAppointments API call. It allows users to 
 * select a slot and proceed to reserve an appointment.
 * The selected slot is passed to the next step for reservation.
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
 * - Retrieves available appointments via ListAvailableAppointments API.
 * - Groups available slots by date for better readability and user experience.
 * - Displays a dynamic form with options for users to select a preferred slot.
 * - Includes a "Choose Appointment" button to proceed to reservation.
 * - Integrates hidden fields to retain query data between pages.
 *
 * Dependencies:
 * - `header.php` and `footer.php` for consistent site structure.
 * - `config.php` for API configuration and credentials.
 * - External API for fetching available appointment data.
 *
 * Notes:
 * - Ensure API credentials in `config.php` are valid and updated.
 * - Validate the data passed through query strings to prevent invalid API calls.
 * - Test the API response regularly to ensure compatibility with the integration.
 */

// Include necessary configuration files
require_once './xtreme_api_config.php';

// Extract query string parameters
$queryData = $_GET;

if (!isset($queryData['entityId']) || !isset($queryData['dealerCode'])) {
    echo "Missing required parameters.";
    exit;
}

// Prepare data for ListAvailableAppointments API call
$apiData = [
    'dealerCode' => $queryData['dealerCode'],
    'entityId' => $queryData['entityId']
];

$url = $config['apiBaseUrl'] . '/listavailableappointments';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt(
    $ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-ApiKey: ' . $config['apiKey']
    ]
);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo "Failed to retrieve available appointments.";
    exit;
}

$responseData = json_decode($response, true);
$appointments = $responseData['appointments'] ?? [];

// Group appointments by date
$groupedAppointments = [];
foreach ($appointments as $appointment) {
    $date = date('l, F j, Y', strtotime($appointment['start']));
    $time = date('g:i A', strtotime($appointment['start'])) 
        . ' - ' . date('g:i A', strtotime($appointment['end']));
    $groupedAppointments[$date][] = [
        'time' => $time,
        'value' => $appointment['start'] . ',' . $appointment['end']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose an Appointment</title>
    <meta name="description" content="Admin view—see all scheduled consultations and service appointments for Great Lakes Containment & Training. Secure, up‑to‑date list.">
    <link rel="canonical" href="https://glcontainmenttraining.com/listappointments.php">

    <script>
        function updateSelectedAppointment(details) {
            const selectedDetailsField = document.getElementById(
              'selected-appointment-details');
            selectedDetailsField.value = details;
        }
    </script>
</head>
<body>
    <?php require './header.php'; ?>

    <div class="content-container">
        <h1>Select an Appointment</h1>
        <section class="reg">
        <?php if (empty($groupedAppointments)) : ?>
            <p>No available appointments at this time. Please try again later.</p>
        <?php else: ?>
            <form action="success.php" method="POST" class="appointment-form">
                <!-- Hidden fields to retain query data -->
                <?php foreach ($queryData as $key => $value): ?>
    <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
<?php endforeach; ?>
                <div class="appointment-options">
                    <?php foreach ($groupedAppointments as $date => $times): ?>
                        <div class="appointment-date">
                            <h2><?php echo htmlspecialchars($date); ?></h2>
                            <?php foreach ($times as $time): ?>
                                <div class="appointment-option">
                                    <label>
                                        <input type="radio" name="appointment" 
                                        value="<?php
                                         echo htmlspecialchars($time['value']);
                                        ?>" 
                                        onchange="updateSelectedAppointment('<?php echo htmlspecialchars($date . ' at ' . $time['time']); ?>')">
                                        <span class="appointment-time">
                                            <?php echo htmlspecialchars(
                                                $time['time']
                                            ); ?>
                                        </span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 20px;">
                    <label for="selected-appointment-details">
                      Selected Appointment:</label>
                    <input type="text" id="selected-appointment-details" 
                    name="selectedAppointmentDetails" 
                    readonly style="width: 100%; margin-bottom: 20px;">
                </div>

                <button type="submit" class="cta-button">CHOOSE APPOINTMENT</button>
            </form>
        <?php endif; ?>
        </section>
    </div>

    <?php require './footer.php'; ?>
</body>
</html>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Load environment variables
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

$email_from = $_ENV['SMTP_FROM'];
if (!filter_var($email_from, FILTER_VALIDATE_EMAIL)) {
    http_response_code(500);
    echo "Invalid email address in SMTP_FROM.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Invalid request method.";
    exit;
}

// Extract and sanitize form data
$first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''));
$last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

if (!$first_name || !$last_name || !$email || !$message) {
    http_response_code(400);
    echo "Missing required fields.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Invalid email address.";
    exit;
}

// Load site settings (e.g., business contact info)
$settingsJson = file_get_contents(__DIR__ . '/content.json');
$settings = json_decode($settingsJson, true);
$businessPhone = $settings['contact']['contactInformation']['phoneNumber'] ?? null;
$escapedPhone = $businessPhone ? htmlspecialchars($businessPhone) : 'our main office line (see website)';

// Email addresses
$toAdmin = "brandonifco@gmail.com";
$toUser = $email;
$customerName = "{$first_name} {$last_name}";

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

    // Send to admin
    $mail->addAddress($toAdmin, 'Admin');
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission';
    $mail->Body = "
        <h2>New Contact Form Submission</h2>
        <p><strong>Name:</strong> {$customerName}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Phone:</strong> {$phone}</p>
        <p><strong>Message:</strong><br>{$message}</p>
    ";
    $mail->AltBody = "Name: {$customerName}\nEmail: {$email}\nPhone: {$phone}\nMessage:\n{$message}";
    $mail->send();

    // Send confirmation to user
    $mail->clearAddresses();
    $mail->addAddress($toUser);
    $mail->Subject = 'We Received Your Message!';
    $mail->Body = "
        <p>Hi {$first_name},</p>
        <p>Thanks for reaching out! We’ve received your message and will get back to you shortly.</p>
        <p>If you have urgent questions, feel free to call us at {$escapedPhone}.</p>
        <p>Thanks again,<br>{$_ENV['SMTP_FROM_NAME']}</p>
    ";
    $mail->send();

    echo "Thank you for contacting us, {$first_name}!";
} catch (Exception $e) {
    http_response_code(500);
    echo "Error sending email: " . htmlspecialchars($mail->ErrorInfo);
}
?>

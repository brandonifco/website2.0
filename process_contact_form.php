<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);

    // Add logic to save data or send email
    // Example: mail($to, $subject, $body, $headers);

    echo "Thank you for contacting us, $first_name!";
} else {
    echo "Invalid request.";
}
?>

<?php
// Include the header file
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <?php include 'styles.php' ?>

</head>
<body>
    <!-- Main Content Area -->
    <div id="main-content" class="content-container">
    <h1 id="hero-tagline" data-content="contact.contactInformation.title">Contact Us</h1>    
    <section id="container-content">
    <br><section class="reg"><br>
                <!-- Contact Information Section -->
        <div id="contact-info">
            <p id="contact-phone" data-content="contact.contactInformation.phoneNumber"></p>
            <p id="contact-email" data-content="contact.contactInformation.emailAddress"></p>
            <p id="contact-address" data-content="contact.contactInformation.physicalAddress"></p>
            <p id="contact-description" data-content="contact.contactInformation.description"></p>
        </div><br>

        <!-- Contact Form Section -->
        <div id="contact-form">
            <form id="dynamic-contact-form">
                <!-- Form fields will be dynamically rendered -->
            </form>
            <p id="form-confirmation" style="display: none;" data-content="contact.contactForm.submissionConfirmation"></p>
        </div>

        <!-- Operating Hours Section -->
        <div id="operating-hours">
            <h2>Operating Hours</h2><br>
            <ul id="hours-list" data-content="contact.operatingHours.hours"></ul><br>
            <p>Closed on:</p><br>
            <ul id="holidays-list" data-content="contact.operatingHours.holidays"></ul><br>
        </div>

        <!-- Map Section -->
        <div id="map">
            <iframe src="" data-content="contact.map.embedURL" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            <a id="directions-link" href="" data-content="contact.map.directionsURL">Get Directions</a>
        </div><br>
        </section><br>
    </section><br>
    </div>

    <script src="js/dynamic-content.js"></script>
</body>
</html>

<?php
// Include the footer file
include 'footer.php';
?>

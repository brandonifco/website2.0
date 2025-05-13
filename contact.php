<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <meta name="description" content="Contact Great Lakes Containment & Training—request a free consultation or ask questions about our pet containment and training services. Phone, email, and map.">
    <link rel="canonical" href="https://glcontainmenttraining.com/contact.php">

    <?php include 'styles.php' ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
</head>

<body>
<?php require './header.php'; ?>

    <!-- Main Content Area -->
    <div id="main-content" class="content-container">
        <h1 id="hero-tagline" data-content="contact.contactInformation.title">Contact Us</h1>
        <section id="container-content">
            <section class="reg">
                <!-- Contact Information Section -->
                <div id="contact-info">
                    Phone:
                    <p id="contact-phone" data-content="contact.contactInformation.phoneNumber"></p>
                    Email:
                    <p id="contact-email" data-content="contact.contactInformation.emailAddress"></p>
                    Address:
                    <p id="contact-address" data-content="contact.contactInformation.physicalAddress"></p>
                    <p id="contact-description" data-content="contact.contactInformation.description"></p>
                </div>

                <!-- Operating Hours Section -->
                <div id="operating-hours">
                    <h2>Operating Hours</h2>
                    <ul id="hours-list" data-content="contact.operatingHours.hours"></ul>
                    <p><strong>Closed on:</strong></p>
                    <ul id="holidays-list" data-content="contact.operatingHours.holidays"></ul>
                </div>

                <!-- Map Section -->
                <div id="map">
                    <iframe src="" data-content="contact.map.embedURL" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div><br>

                <!-- Contact Form Section -->
                <div id="contact-form">
                    <h2>Send us a message!</h2>
                    <form id="dynamic-contact-form" action="process_contact_form.php" method="POST">
                        <div class="form-group">
                            <label for="first-name">First Name</label>
                            <input type="text" id="first-name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last-name">Last Name</label>
                            <input type="text" id="last-name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="message">What do we need to know?</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn-submit">Submit</button>
                    </form>
                </div>
            </section>
        </section><br>
    </div>

    <script src="js/dynamic-content.js"></script>
</body>

</html>

<?php
// Include the footer file
include 'footer.php';
?>
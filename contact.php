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
            <br>
            <section class="reg"><br>
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
                    <iframe src="" data-content="contact.map.embedURL" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div><br>

                <!-- Contact Form Section -->
                <div id="contact-form" >
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
                    </form><br>
                </div><br>
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
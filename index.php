<?php
// Include the header file
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <?php include 'styles.php'; ?>
</head>

<body>
    <!-- Main Content Area -->
    <div id="main-content">
        <div class="content-container">
            <h1 id="hero-tagline" data-content="homepage.heroTagline"></h1><br>
            <!-- Call to Action Section -->

            <div id="cta-section">
                <a href="" id="primary-cta" class="cta-button" data-content="homepage.cta.primary.url">
                    <span data-content="homepage.cta.primary.label"></span>
                </a>
                <br><br>
                <a href="" id="secondary-cta" class="cta-button secondary" data-content="homepage.cta.secondary.url">
                    <span data-content="homepage.cta.secondary.label"></span>
                </a>
            </div>

            <section class="reg"><br>
                <!-- Service Overview Section -->

                <div id="service-overview" data-content="homepage.serviceOverview">
                    <!-- Service cards will be dynamically loaded here -->
                </div><br>

            </section><br>

            <section class="reg"><br>
                <!-- Client Testimonials Section -->

                <div id="client-testimonials" data-content="homepage.clientTestimonials">
                    <!-- Testimonials will be dynamically loaded here -->
                </div><br>

            </section><br>

            <section class="reg">
                <br>
                <!-- About Us Section -->

                <div id="about-us" class="about">
                    <br>
                    <p id="about-intro" data-content="aboutUs.intro"></p><br>
                    <p id="about-history" data-content="aboutUs.companyHistory"></p><br>
                    <p id="about-mission" data-content="aboutUs.missionStatement"></p><br>
                    <p id="about-values" data-content="aboutUs.coreValues"></p><br>
                    <br>

                    <div id="about-team">
                        <img id="team-image" src="" alt="Our Team" data-content="aboutUs.teamImageURL">
                    </div><br>

                </div><br>

            </section><br>
        </div>
    </div>

</body>

</html>

<?php
// Include the footer file
include 'footer.php';
?>
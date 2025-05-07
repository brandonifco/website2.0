<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Great Lakes Containment and Training</title>
    <meta name="description"
        content="Professional pet containment and training for dogs in the Great Lakes region. Free consultation. Keep your pet happy, safe, and home.">
    <link rel="canonical" href="https://glcontainmenttraining.com/">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "Great Lakes Containment & Training",
        "image": "https://glcontainmenttraining.com/images/GreatLakesLogo.png",
        "@id": "https://glcontainmenttraining.com",
        "url": "https://glcontainmenttraining.com",
        "telephone": "+1-231-938-1138",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "3501 Kirkland Court #A",
            "addressLocality": "Williamsburg",
            "addressRegion": "MI",
            "postalCode": "49690",
            "addressCountry": "US"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 44.76405699211413,
            "longitude": -85.50765329710538
        },
        "openingHoursSpecification": [{
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": [
                    "Monday",
                    "Friday"
                ],
                "opens": "10:00",
                "closes": "17:00",
                "description": "Phone support only"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": [
                    "Tuesday",
                    "Wednesday",
                    "Thursday"
                ],
                "opens": "10:00",
                "closes": "17:00"
            }
        ],
        "sameAs": [
            "https://www.facebook.com/IFofUP/"
        ]
    }
    </script>

</head>

<body>
    <!-- Main Content Area -->
    <div id="main-content">
        <div class="content-container">
            <h1 id="hero-tagline">Give Your Pet the Freedom They Crave and the Safety They Need.</h1>
            <!-- Call to Action Section -->

            <div id="cta-section">
                <a href="/schedule.php" id="primary-cta" class="cta-button">
                    SCHEDULE A FREE CONSULTATION
                </a>

                <br>
                <a href="" id="secondary-cta" class="cta-button secondary" data-content="homepage.cta.secondary.url">
                    <span data-content="homepage.cta.secondary.label"></span>
                </a>


            </div>

            <section class="reg">
                <p class="hero-intro">
                    Great Lakes Containment & Training provides certified Invisible Fence® installation and professional
                    pet training throughout the Great Lakes region. We specialize in safe, effective, and compassionate
                    containment solutions that keep your pet happy and home.
                </p>
                <!-- Service Overview Section -->

                <div id="service-overview" data-content="homepage.serviceOverview">
                    <!-- Service cards will be dynamically loaded here -->
                </div>

            </section><br>

            <section class="reg">
                <!-- Client Testimonials Section -->

                <div id="client-testimonials" data-content="homepage.clientTestimonials">
                    <!-- Testimonials will be dynamically loaded here -->
                </div>

            </section><br>

            <section class="reg">
                <!-- About Us Section -->

                <div id="about-us" class="about">
                    <br>
                    <p id="about-intro" data-content="aboutUs.intro"></p><br>
                    <p id="about-history" data-content="aboutUs.companyHistory"></p><br>
                    <p id="about-mission" data-content="aboutUs.missionStatement"></p><br>
                    <p id="about-values" data-content="aboutUs.coreValues"></p><br>
                    <br>
                </div>

            </section><br>
        </div>
    </div>
</body>

</html>
<?php
include 'footer.php';
?>
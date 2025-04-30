<?php
// Include the header file
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <?php include 'styles.php' ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
</head>

<body>


    <!-- Main Content Area -->
    <div id="main-content" class="content-container">
        <section id="container-content">
            <!-- Introduction Section -->
            <div id="about-intro" class="reg">
                <p data-content="aboutUs.intro"></p>
            </div>
<br>
            <!-- Company History Section -->
            <div id="company-history" class="reg">

                <h2>Our History</h2>
                <p data-content="aboutUs.companyHistory"></p>
            </div>
<br>
            <!-- Mission Statement Section -->
            <div id="mission-statement" class="reg">
                <h2>Mission Statement</h2>
                <p data-content="aboutUs.missionStatement"></p>
            </div>
<br>
            <!-- Core Values Section -->
            <div id="core-values" class="reg">
                <h2>Core Values</h2>
                <p data-content="aboutUs.coreValues"></p>
            </div>
<br>

        </section><br>
    </div>

    <script src="js/dynamic-content.js"></script>
</body>

</html>

<?php
// Include the footer file
include 'footer.php';
?>
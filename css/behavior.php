<?php
// Include the header file
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <?php include 'styles.php' ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
</head>
<body>

    <!-- Main Content Area -->
    <div id="main-content" class="content-container">
    <br><h1 id="hero-tagline" data-content="services.behaviorTraining.title"></h2><br>  


        <!-- Behavior Training Section -->
        <section class="reg" id="behavior-training">
            <p data-content="services.behaviorTraining.description"></p>
            <h3>Behavioral Issues Addressed</h3>
            <div id="behavior-issues">
                <!-- Each issue will render as a card -->
                <div data-content="services.behaviorTraining.issuesAddressed"></div>
            </div><br>

            <h3>Our Training Process</h3>
            <ul data-content="services.behaviorTraining.process"></ul><br>
            <h3>Choose the Training Package That's Right for You</h3>
            <ul data-content="services.behaviorTraining.packages"></ul><br>
            <a href="contact.php"><p data-content="services.behaviorTraining.cta"></p></a><br>
        </section><br>

    </div>


    <script src="js/dynamic-content.js"></script>
</body>
</html>

<?php
// Include the footer file
include 'footer.php';
?>

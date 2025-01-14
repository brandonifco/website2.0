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

</head>
<body>

    <!-- Main Content Area -->
    <div id="main-content" class="content-container">
    <br><h1 id="hero-tagline" data-content="services.behaviorTraining.title"></h2><br>  


        <!-- Behavior Training Section -->
        <section class="reg" id="behavior-training">
            <br>
            <p data-content="services.behaviorTraining.description"></p><br>
            <p><h3>Behavioral Issues Addressed</h3></p>
            <div id="behavior-issues">
                <!-- Each issue will render as a card -->
                <div data-content="services.behaviorTraining.issuesAddressed"></div>
            </div><br>

            <h3>Our Training Process</h3>
            <ul data-content="services.behaviorTraining.process"></ul><br>
            <h3>Choose the Training Package That's Right for You</h3>
            <ul data-content="services.behaviorTraining.packages"></ul>
            <p data-content="services.behaviorTraining.cta"></p><br>
        </section><br>

    </div>


    <script src="js/dynamic-content.js"></script>
</body>
</html>

<?php
// Include the footer file
include 'footer.php';
?>

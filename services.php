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
    <br><h1 data-content="services.title"></h1><br>
    <section id="container-content"><br>

        <!-- Behavior Training Section -->
        <section class="reg" id="behavior-training">
            <br><h2 data-content="services.behaviorTraining.title"></h2><br>
            <p data-content="services.behaviorTraining.description"></p><br>
            <h3>Behavioral Issues Addressed</h3>
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

        <!-- Obedience Training Section -->
        <section class="reg" id="obedience-training">
            <h2 data-content="services.obedienceTraining.title"></h2>
            <p data-content="services.obedienceTraining.description"></p><br>
            <h3>Core Skills</h3>
            <ul data-content="services.obedienceTraining.coreSkills"></ul><br>
            <h3>Our Training Methods</h3>
            <ul data-content="services.obedienceTraining.methods"></ul><br>
            <h3>Choose the Training Package That's Right for You</h3>
            <ul data-content="services.obedienceTraining.packages"></ul>
            <p data-content="services.obedienceTraining.cta"></p><br>
        </section><br>

        <!-- Containment Systems Section -->
        <section class="reg" id="containment-systems">
            <p>
            <h2 data-content="services.containmentSystems.title"></h2></p>
            <p data-content="services.containmentSystems.description"></p><br>
            <h3>How it Works</h3>
            <ul data-content="services.containmentSystems.howItWorks"></ul><br>
            <h3>Benefits of Invisible Fence®</h3>
            <ul data-content="services.containmentSystems.benefits"></ul><br>
            <h3>Our Services</h3>
            <ul data-content="services.containmentSystems.services"></ul><br>
            <a href="schedule.php"><p data-content="services.containmentSystems.cta"></p></a>
        <br></section>
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

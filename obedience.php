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
    <br><h1 id="hero-tagline" data-content="services.obedienceTraining.title"></h1>
    <section id="container-content">
        <!-- Obedience Training Section -->
        <section class="reg" id="obedience-training">
            <p data-content="services.obedienceTraining.description"></p>
            <h3>Core Skills</h3>
            <ul data-content="services.obedienceTraining.coreSkills"></ul><br>
            <h3>Our Training Methods</h3>
            <ul data-content="services.obedienceTraining.methods"></ul><br>
            <h3>Choose the Training Package That's Right for You</h3>
            <ul data-content="services.obedienceTraining.packages"></ul>
            <a href="contact.php"><p data-content="services.obedienceTraining.cta"></p></a>
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

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
    <br><h1 id="hero-tagline" data-content="services.containmentSystems.title"></h1><br>
    <section id="container-content">


        <!-- Containment Systems Section -->
        <section class="reg" id="containment-systems">
            <p data-content="services.containmentSystems.description"></p><br>
            <h3>How it Works</h3>
            <ul data-content="services.containmentSystems.howItWorks"></ul><br>
            <h3>Benefits of Invisible Fence® Brand</h3>
            <ul data-content="services.containmentSystems.benefits"></ul><br>
            <h3>Our Services</h3>
            <ul data-content="services.containmentSystems.services"></ul><br>
            <a href="schedule.php"><p data-content="services.containmentSystems.cta"></p></a>
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

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
</head>
<body>


    <!-- Main Content Area -->
    <div id="main-content" class="content-container">
    <section id="container-content"><br>
        <!-- Introduction Section -->
        <section id="about-intro" class="reg">
            <br>
            <p data-content="aboutUs.intro"></p><br>
        </section>

        <!-- Company History Section -->
        <section id="company-history" class="reg">
            <br><h2>Our History</h2>
            <p data-content="aboutUs.companyHistory"></p><br>
        </section>

        <!-- Mission Statement Section -->
        <section id="mission-statement" class="reg">
            <br><h2>Mission Statement</h2>
            <p data-content="aboutUs.missionStatement"></p><br>
        </section>

        <!-- Core Values Section -->
        <section id="core-values" class="reg">
            <br><h2>Core Values</h2>
            <p data-content="aboutUs.coreValues"></p><br>
        </section>

        <!-- Team Image Section -->
        <section id="team-photo" class="reg">
            <br>
            <img src="" alt="Our Team" data-content="aboutUs.teamImageURL"><br>
        </section><br>
    </section><br>
    </div>

    <script src="js/dynamic-content.js"></script>
</body>
</html>

<?php
// Include the footer file
include 'footer.php';
?>

<?php
// Include the header file
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ</title>
    <?php include 'styles.php' ?>

</head>
<body>
    <!-- Main Content Area -->
    <div id="main-content" class="content-container">
        <h1 id="faq-title" data-content="faq.title"></h1>
        
        <!-- FAQ Categories -->
        <div id="faq-categories">
            <!-- Each category with questions will be rendered dynamically -->
            <div data-content="faq.categories"></div>
        </div>
    </div>

    <script src="js/dynamic-content.js"></script>
</body>
</html>

<?php
// Include the footer file
include 'footer.php';
?>

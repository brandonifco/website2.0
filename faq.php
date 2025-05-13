
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ</title>
    <meta name="description"
        content="Answers to common questions about Invisible Fence® electronic pet containment, training, costs, collar safety, and maintenance with Great Lakes Containment & Training.">
    <link rel="canonical" href="https://glcontainmenttraining.com/faq.php">

    <?php include 'styles.php' ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [{
                "@type": "Question",
                "name": "How does Invisible Fence® Brand work?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "We have customized technologies for every situation, including Invisible Fence Brand professional GPS technology. <a href='whyif.php'>Click here to learn more</a>."
                }
            },
            {
                "@type": "Question",
                "name": "What types of pets do you work with?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Our training methods are designed primarily for dogs and cats, but we’ve worked successfully with a variety of pets over the years. If your animal has a curious mind and needs boundaries, chances are we can help. We’re always happy to talk through your specific needs and see if we’re the right fit. We have PhD animal behaviorists at our fingertips, so if needed, we can quickly and easily consult with other experts."
                }
            }
        ]
    }
    </script>

</head>

<body>
<?php require './header.php'; ?>

    <!-- Main Content Area -->
    <div id="main-content" class="content-container">
        <h1 id="hero-tagline" data-content="faq.title"></h1><br>
        <section class=faq>
            <!-- FAQ Categories -->
            <div id="faq-categories">
                <!-- Each category with questions will be rendered dynamically -->
                <div data-content="faq.categories"></div>
            </div>
        </section>
    </div>

    <script src="js/dynamic-content.js"></script>
</body>

</html>

<?php
// Include the footer file
include 'footer.php';
?>
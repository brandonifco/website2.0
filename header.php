<?php
// Start of header.php
?>
<link rel="stylesheet" href="css/header.css">
<link rel="stylesheet" href="fonts/fonts.css">

<header class="site-header">
    <!-- Logo -->
    <div class="logo">
        <a href="index.php">
            <img id="company-logo" data-content="header.logoImageURL" alt="Company Logo">
        </a>
    </div>

    <!-- Menu Toggle for Mobile -->
    <button class="menu-toggle" aria-label="Toggle Mobile Menu">☰</button>

    <!-- Navigation Menus -->
    <nav class="header-nav">
        <!-- Desktop Menu -->
        <ul class="desktop-menu" data-content="header.menuItems.desktop"></ul>

        <!-- Mobile Menu -->
        <ul class="mobile-menu" data-content="header.menuItems.mobile"></ul>
    </nav>
</header>


<script src="js/header.js"></script>
<script src="js/scroll.js"></script>
<script src="js/dynamic-content.js"></script>
<?php
// End of header.php
?>

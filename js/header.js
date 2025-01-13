document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');

    // Toggle the mobile menu visibility
    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('open');
    });

    // Optional: Close the menu if the user clicks outside
    document.addEventListener('click', (event) => {
        if (!mobileMenu.contains(event.target) && !menuToggle.contains(event.target)) {
            mobileMenu.classList.remove('open');
        }
    });

    // Accessibility improvement: Close menu with "Esc" key
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            mobileMenu.classList.remove('open');
        }
    });
});

document.addEventListener('scroll', function () {
    const scrollPosition = window.scrollY;
    document.body.style.backgroundPosition = `center ${scrollPosition * 0.5}px`;
});
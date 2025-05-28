document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');

    navToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
});


// Wait for the entire page to load
window.addEventListener('load', function () {
    // Hide the preloader
    document.getElementById('preloader').style.display = 'none';
    // Show the content
    document.getElementById('content').style.display = 'block';
});

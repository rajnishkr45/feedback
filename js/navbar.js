
const menuIcon = document.querySelector('.menu-icon');
const navLinks = document.querySelector('.nav-links');

document.getElementById('theme').addEventListener('click', function(event) {
    event.preventDefault();
});

menuIcon.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    menuIcon.classList.toggle('active');
});
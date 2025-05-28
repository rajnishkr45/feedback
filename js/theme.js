document.addEventListener('DOMContentLoaded', () => {
    const btn = document.querySelector('#theme');

    // Check the saved theme preference in local storage
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        btn.innerHTML = '<span class="material-symbols-outlined">light_mode</span>';
    } else {
        document.body.classList.remove('dark-mode');
        btn.innerHTML = '<span class="material-symbols-outlined">dark_mode</span>';
    }

    btn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');

        if (document.body.classList.contains('dark-mode')) {
            btn.innerHTML = '<span class="material-symbols-outlined">light_mode</span>';
            localStorage.setItem('theme', 'dark');
        } else {
            btn.innerHTML = '<span class="material-symbols-outlined">dark_mode</span>';
            localStorage.setItem('theme', 'light');
        }
    });
});
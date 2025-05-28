const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item => {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i => {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});



// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

// Check localStorage for sidebar state and apply it
if (localStorage.getItem('sidebarState') === 'hidden') {
	sidebar.classList.add('hide'); // Hide sidebar if stored state is hidden
}

// Toggle sidebar visibility
menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');

	// Store the current state in localStorage
	if (sidebar.classList.contains('hide')) {
		localStorage.setItem('sidebarState', 'hidden'); // Save hidden state
	} else {
		localStorage.setItem('sidebarState', 'visible'); // Save visible state
	}
});







const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

searchButton.addEventListener('click', function (e) {
	if (window.innerWidth < 576) {
		e.preventDefault();
		searchForm.classList.toggle('show');
		if (searchForm.classList.contains('show')) {
			searchButtonIcon.classList.replace('bx-search', 'bx-x');
		} else {
			searchButtonIcon.classList.replace('bx-x', 'bx-search');
		}
	}
})


if (window.innerWidth < 768) {
	sidebar.classList.add('hide');
} else if (window.innerWidth > 576) {
	searchButtonIcon.classList.replace('bx-x', 'bx-search');
	searchForm.classList.remove('show');
}


window.addEventListener('resize', function () {
	if (this.innerWidth > 576) {
		searchButtonIcon.classList.replace('bx-x', 'bx-search');
		searchForm.classList.remove('show');
	}
})


let switchMode = document.getElementById('switch-mode');
// Check localStorage for saved theme on page load
document.addEventListener('DOMContentLoaded', () => {
	let savedTheme = localStorage.getItem('theme');

	// If theme is saved and is 'dark', apply dark mode
	if (savedTheme === 'dark') {
		document.body.classList.add('dark');
		switchMode.checked = true; // Make sure the switch is in the correct state
	} else {
		document.body.classList.remove('dark');
		switchMode.checked = false; // Ensure the switch reflects light mode
	}
});

// Add event listener to the theme switcher
switchMode.addEventListener('change', function () {
	if (this.checked) {
		document.body.classList.add('dark');
		localStorage.setItem('theme', 'dark'); // Save theme to localStorage
	} else {
		document.body.classList.remove('dark');
		localStorage.setItem('theme', 'light'); // Save light theme to localStorage
	}
});

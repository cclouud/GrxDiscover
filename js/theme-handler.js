document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle ? themeToggle.querySelector('i') : null;

    // Function to apply theme
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (icon) {
            updateIcon(theme);
        }
    }

    // Function to update icon
    function updateIcon(theme) {
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    // Check for saved theme preference on load
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        applyTheme(savedTheme);
    } else {
        // Default to dark mode if no preference is saved
        applyTheme('dark');
    }

    // Add event listener to theme toggle button if it exists
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
});

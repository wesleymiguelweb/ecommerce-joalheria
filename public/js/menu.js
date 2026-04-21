// User menu dropdown functionality
document.addEventListener('DOMContentLoaded', () => {
    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const userMenuDropdown = document.querySelector('.user-menu-dropdown');

    if (!userMenuToggle || !userMenuDropdown) return;

    // Toggle menu visibility
    userMenuToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        userMenuDropdown.classList.toggle('visible');
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.user-menu')) {
            userMenuDropdown.classList.remove('visible');
        }
    });

    // Close menu when clicking a link
    userMenuDropdown.addEventListener('click', (e) => {
        if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
            userMenuDropdown.classList.remove('visible');
        }
    });
});

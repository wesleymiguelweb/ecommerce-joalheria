/**
 * Menu do usuário dropdown
 */
document.addEventListener('DOMContentLoaded', () => {
    const userMenuToggle = document.getElementById('user-menu-toggle');
    const userMenuDropdown = document.getElementById('user-menu-dropdown');

    if (userMenuToggle && userMenuDropdown) {
        userMenuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isVisible = userMenuDropdown.style.display === 'block';
            userMenuDropdown.style.display = isVisible ? 'none' : 'block';
        });

        // Fechar menu ao clicar em qualquer lugar
        document.addEventListener('click', () => {
            userMenuDropdown.style.display = 'none';
        });

        // Evitar fechar ao clicar dentro do menu
        userMenuDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
});

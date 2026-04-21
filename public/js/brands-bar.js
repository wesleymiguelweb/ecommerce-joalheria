/**
 * Brands Bar Animation
 * Rotação infinita com pausa ao passar o mouse
 */

document.addEventListener('DOMContentLoaded', () => {
    const brandsBar = document.querySelector('.brands-bar');
    const scrollContent = document.querySelector('.brands-scroll-content');

    if (!brandsBar || !scrollContent) return;

    // Adiciona pausa ao passar o mouse
    brandsBar.addEventListener('mouseenter', () => {
        scrollContent.classList.add('paused');
    });

    // Remove pausa ao sair o mouse
    brandsBar.addEventListener('mouseleave', () => {
        scrollContent.classList.remove('paused');
    });

    // Adiciona listener para clique em marcas
    const brandSpans = scrollContent.querySelectorAll('span[data-brand]');
    brandSpans.forEach(span => {
        span.addEventListener('click', (e) => {
            e.preventDefault();
            const brand = span.getAttribute('data-brand');
            const category = span.getAttribute('data-category');
            
            // Redireciona para a página de categoria com filtro de marca
            if (category === 'feminino') {
                window.location.href = `/feminino?brand=${encodeURIComponent(brand)}`;
            } else if (category === 'masculino') {
                window.location.href = `/masculino?brand=${encodeURIComponent(brand)}`;
            }
        });
    });
});

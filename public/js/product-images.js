/**
 * Gerador de Imagens de Produtos em SVG
 * Substitui automaticamente imagens placeholder por SVGs decorativos
 */

document.addEventListener('DOMContentLoaded', () => {
    /**
     * Gera um SVG baseado no tipo de produto e cor
     */
    function generateProductImage(type, color, productName) {
        const colors = {
            'ouro': { primary: '#FFD700', secondary: '#FFC700', accent: '#DAA520' },
            'prata': { primary: '#C0C0C0', secondary: '#D3D3D3', accent: '#A9A9A9' },
            'neutro': { primary: '#E8E8E8', secondary: '#F0F0F0', accent: '#D0D0D0' }
        };

        const colorSet = colors[color] || colors['neutro'];
        let svgContent = '';

        switch (type) {
            case 'anel':
                svgContent = `
                    <circle cx="50" cy="50" r="35" fill="${colorSet.primary}" stroke="${colorSet.accent}" stroke-width="2"/>
                    <circle cx="50" cy="50" r="30" fill="none" stroke="${colorSet.secondary}" stroke-width="1" opacity="0.6"/>
                    <circle cx="65" cy="35" r="8" fill="${colorSet.accent}" filter="url(#shadow)"/>
                `;
                break;
            case 'colar':
                svgContent = `
                    <path d="M 20 30 Q 50 50 80 30" stroke="${colorSet.primary}" stroke-width="4" fill="none"/>
                    <circle cx="50" cy="60" r="12" fill="${colorSet.accent}" filter="url(#shadow)"/>
                    <circle cx="50" cy="60" r="8" fill="${colorSet.secondary}"/>
                `;
                break;
            case 'brinco':
                svgContent = `
                    <circle cx="35" cy="35" r="6" fill="${colorSet.primary}" stroke="${colorSet.accent}" stroke-width="1"/>
                    <rect x="30" y="40" width="10" height="20" fill="${colorSet.primary}" stroke="${colorSet.accent}" stroke-width="1" rx="2"/>
                    <circle cx="65" cy="35" r="6" fill="${colorSet.primary}" stroke="${colorSet.accent}" stroke-width="1"/>
                    <rect x="60" y="40" width="10" height="20" fill="${colorSet.primary}" stroke="${colorSet.accent}" stroke-width="1" rx="2"/>
                `;
                break;
            case 'pulseira':
                svgContent = `
                    <path d="M 20 50 Q 30 20 50 20 Q 70 20 80 50" stroke="${colorSet.primary}" stroke-width="5" fill="none" stroke-linecap="round"/>
                    <circle cx="35" cy="35" r="4" fill="${colorSet.accent}"/>
                    <circle cx="50" cy="25" r="4" fill="${colorSet.accent}"/>
                    <circle cx="65" cy="35" r="4" fill="${colorSet.accent}"/>
                `;
                break;
            case 'corrente':
                svgContent = `
                    <path d="M 20 50 L 80 50" stroke="${colorSet.primary}" stroke-width="3" fill="none"/>
                    <circle cx="25" cy="50" r="3" fill="${colorSet.accent}"/>
                    <circle cx="35" cy="50" r="3" fill="${colorSet.accent}"/>
                    <circle cx="45" cy="50" r="3" fill="${colorSet.accent}"/>
                    <circle cx="55" cy="50" r="3" fill="${colorSet.accent}"/>
                    <circle cx="65" cy="50" r="3" fill="${colorSet.accent}"/>
                    <circle cx="75" cy="50" r="3" fill="${colorSet.accent}"/>
                `;
                break;
            case 'relogio':
                svgContent = `
                    <circle cx="50" cy="45" r="20" fill="${colorSet.primary}" stroke="${colorSet.accent}" stroke-width="2"/>
                    <circle cx="50" cy="45" r="15" fill="none" stroke="${colorSet.secondary}" stroke-width="1" opacity="0.6"/>
                    <line x1="50" y1="35" x2="50" y2="25" stroke="${colorSet.accent}" stroke-width="2"/>
                    <line x1="60" y1="45" x2="68" y2="45" stroke="${colorSet.accent}" stroke-width="2"/>
                    <circle cx="50" cy="45" r="2" fill="${colorSet.accent}"/>
                `;
                break;
            default:
                svgContent = `
                    <rect x="10" y="10" width="80" height="80" fill="${colorSet.primary}" stroke="${colorSet.accent}" stroke-width="2" rx="5"/>
                    <circle cx="50" cy="50" r="15" fill="${colorSet.accent}" opacity="0.5"/>
                `;
        }

        const svg = `
            <svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);">
                <defs>
                    <filter id="shadow" x="-50%" y="-50%" width="200%" height="200%">
                        <feDropShadow dx="2" dy="2" stdDeviation="3" flood-opacity="0.4"/>
                    </filter>
                </defs>
                ${svgContent}
                <text x="50" y="95" font-size="8" text-anchor="middle" fill="${colorSet.accent}" opacity="0.7">${type.toUpperCase()}</text>
            </svg>
        `;

        return `data:image/svg+xml;base64,${btoa(svg)}`;
    }

    /**
     * Processa todos os cartões de produto e gera imagens SVG
     */
    function processProductImages() {
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            const img = card.querySelector('img');
            if (!img) return;

            const type = card.getAttribute('data-type') || 'anel';
            const color = card.getAttribute('data-color') || 'neutro';
            const name = card.querySelector('h3')?.textContent || 'Produto';

            // Gerar e aplicar imagem SVG
            const svgUrl = generateProductImage(type, color, name);
            img.src = svgUrl;
            img.style.objectFit = 'cover';
            img.style.objectPosition = 'center';
            
            // Fallback em caso de erro
            img.onerror = () => {
                img.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2RkZCIvPjx0ZXh0IHRleHQtYW5jaG9yPSJtaWRkbGUiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDUwIDUwKSIgZm9udC1zaXplPSIyNCIgZmlsbD0iI2FhYSI+PzwvdGV4dD48L3N2Zz4=';
            };
        });
    }

    // Processar imagens ao carregar
    processProductImages();

    // Reprocessar ao aplicar filtros (observar mudanças no DOM)
    const observer = new MutationObserver(() => {
        processProductImages();
    });

    const productListing = document.getElementById('product-listing');
    if (productListing) {
        observer.observe(productListing, {
            childList: true,
            subtree: true
        });
    }
});

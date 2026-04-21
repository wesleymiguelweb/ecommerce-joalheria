/**
 * Sistema de Filtros com API MySQL e Paginação
 * Consulta /api/products-filter em tempo real
 */

document.addEventListener('DOMContentLoaded', () => {
    // Elementos do DOM
    const productListing = document.getElementById('product-listing');
    const priceSlider = document.getElementById('price-slider-input');
    const priceValue = document.getElementById('price-slider-value');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const categoryFilters = document.getElementById('category-filters');
    const colorFilters = document.getElementById('color-filters');
    const brandFilters = document.getElementById('brand-filters');
    const sortSelect = document.getElementById('sort-select');
    const filterCounter = document.getElementById('filter-counter');
    const paginationContainer = document.getElementById('pagination-container');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const currentPageSpan = document.getElementById('current-page');
    const totalPagesSpan = document.getElementById('total-pages');
    const resultsCountSpan = document.getElementById('results-count');

    if (!productListing) return;

    // Estado dos filtros e paginação
    const filterState = {
        category: 'feminino', // ou 'masculino'
        type: 'todos',
        color: null,
        brand: null,
        maxPrice: 10000,
        minPrice: 0,
        sort: 'popular',
        search: '',
        page: 1,
        perPage: 12
    };

    let totalProducts = 0;
    let totalPages = 1;

    // ===== DETECTAR CATEGORIA DA PÁGINA =====
    function detectCategory() {
        const url = window.location.pathname;
        if (url.includes('/feminino')) {
            filterState.category = 'feminino';
        } else if (url.includes('/masculino')) {
            filterState.category = 'masculino';
        }
    }

    // ===== FETCH PRODUTOS COM FILTROS =====
    async function fetchFilteredProducts() {
        try {
            // Mostrar loading
            if (filterCounter) {
                filterCounter.textContent = 'Carregando produtos...';
            }
            productListing.innerHTML = '<p style="grid-column: 1 / -1; text-align: center; padding: 40px;">🔄 Carregando produtos...</p>';

            // Construir query string
            const params = new URLSearchParams({
                category: filterState.category,
                type: filterState.type,
                max_price: filterState.maxPrice,
                min_price: filterState.minPrice,
                sort: filterState.sort,
                page: filterState.page,
                per_page: filterState.perPage
            });

            if (filterState.color) params.append('color', filterState.color);
            if (filterState.brand) params.append('brand', filterState.brand);
            if (filterState.search) params.append('search', filterState.search);

            console.log('Filtros aplicados:', {
                category: filterState.category,
                type: filterState.type,
                color: filterState.color,
                brand: filterState.brand,
                maxPrice: filterState.maxPrice,
                sort: filterState.sort,
                page: filterState.page
            });

            const response = await fetch(`/api/products-filter?${params.toString()}`);
            const data = await response.json();

            if (data.success) {
                console.log(`${data.total} produtos encontrados com ordenação: ${filterState.sort}`);
                renderProducts(data.products);
                updateFilterCounter(data.total);
                updatePagination(data.total);
                // Scroll para o topo dos produtos
                productListing.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                console.error('Erro ao buscar produtos:', data);
                productListing.innerHTML = '<p style="grid-column: 1 / -1; text-align: center;">Erro ao carregar produtos.</p>';
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            productListing.innerHTML = '<p style="grid-column: 1 / -1; text-align: center;">Erro ao conectar ao servidor.</p>';
        }
    }

    // ===== RENDERIZAR PRODUTOS =====
    function renderProducts(products) {
        if (!products || products.length === 0) {
            productListing.innerHTML = '<p style="grid-column: 1 / -1; text-align: center;">Nenhum produto encontrado.</p>';
            return;
        }

        productListing.innerHTML = products.map(product => `
            <div class="product-card" data-productid="${product.id}" data-price="${product.price}" data-color="${product.color || 'neutro'}" data-brand="${product.brand || ''}">
                <a href="/produto/${product.id}" class="product-card-link">
                    <img src="/img/${product.image}" alt="${product.name}" onerror="this.src='/img/placeholder.svg'">
                    <h3>${product.name}</h3>
                    <p class="price">
                        <span class="sale">R$ ${parseFloat(product.price).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    </p>
                    <p class="stock">
                        ${product.stock > 0 ? `Estoque: ${product.stock}` : '<span style="color: #dc3545;">Indisponível</span>'}
                    </p>
                </a>
                ${product.stock > 0 ? `
                    <button class="btn btn-dark add-to-cart-btn-listing"
                            data-product-id="${product.id}"
                            data-product-name="${product.name}"
                            data-product-price="${product.price}"
                            data-product-img="/img/${product.image}">
                        Adicionar ao Carrinho
                    </button>
                ` : ''}
            </div>
        `).join('');

        // Reattach event listeners
        attachAddToCartListeners();
    }

    // ===== ATUALIZAR PAGINAÇÃO =====
    function updatePagination(total) {
        totalProducts = total;
        totalPages = Math.ceil(total / filterState.perPage);

        if (paginationContainer) {
            if (totalPages > 1) {
                paginationContainer.style.display = 'block';
            } else {
                paginationContainer.style.display = 'none';
            }
        }

        // Atualizar informações de página
        if (currentPageSpan) currentPageSpan.textContent = filterState.page;
        if (totalPagesSpan) totalPagesSpan.textContent = totalPages;
        if (resultsCountSpan) resultsCountSpan.textContent = totalProducts;

        // Atualizar estado dos botões
        if (prevPageBtn) {
            prevPageBtn.disabled = filterState.page <= 1;
        }
        if (nextPageBtn) {
            nextPageBtn.disabled = filterState.page >= totalPages;
        }
    }

    // ===== ATUALIZAR CONTADOR =====
    function updateFilterCounter(total) {
        if (filterCounter) {
            filterCounter.textContent = `${total} produto(s) encontrado(s)`;
        }

        // Garantir que o select mantém o valor correto
        if (sortSelect && sortSelect.value !== filterState.sort) {
            sortSelect.value = filterState.sort;
        }
    }

    // ===== EVENT LISTENERS =====

    // Filtro de categoria
    if (categoryFilters) {
        categoryFilters.addEventListener('click', (e) => {
            e.preventDefault();
            const target = e.target.closest('.filter-item');
            if (!target) return;

            const type = target.getAttribute('data-category');
            if (type) {
                filterState.type = type;
                filterState.page = 1; // Reset page
                // Remove active de todos
                categoryFilters.querySelectorAll('.filter-item').forEach(item => item.classList.remove('active'));
                target.classList.add('active');
                fetchFilteredProducts();
            }
        });
    }

    // Filtro de preço
    if (priceSlider) {
        priceSlider.addEventListener('input', (e) => {
            filterState.maxPrice = parseInt(e.target.value);
            priceValue.textContent = `R$${filterState.maxPrice.toLocaleString('pt-BR')}`;
        });
    }

    // Filtro de cor
    if (colorFilters) {
        colorFilters.addEventListener('click', (e) => {
            const button = e.target.closest('.color-swatch');
            if (!button) return;

            const color = button.getAttribute('data-color');
            if (filterState.color === color) {
                filterState.color = null;
                button.classList.remove('active');
            } else {
                colorFilters.querySelectorAll('.color-swatch').forEach(b => b.classList.remove('active'));
                filterState.color = color;
                button.classList.add('active');
            }

            // Aplicar filtro imediatamente
            filterState.page = 1;
            fetchFilteredProducts();
        });
    }

    // Filtro de marca
    if (brandFilters) {
        brandFilters.addEventListener('click', (e) => {
            const button = e.target.closest('.filter-item');
            if (!button) return;

            const brand = button.getAttribute('data-brand');
            if (brand) {
                if (filterState.brand === brand) {
                    filterState.brand = null;
                    button.classList.remove('active');
                } else {
                    brandFilters.querySelectorAll('.filter-item').forEach(b => b.classList.remove('active'));
                    filterState.brand = brand === 'todos' ? null : brand;
                    button.classList.add('active');
                }

                // Aplicar filtro imediatamente
                filterState.page = 1;
                fetchFilteredProducts();
            }
        });
    }

    // Aplicar filtros
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', () => {
            filterState.page = 1; // Reset page
            fetchFilteredProducts();
        });
    }

    // Ordenação
    if (sortSelect) {
        sortSelect.addEventListener('change', (e) => {
            filterState.sort = e.target.value;
            filterState.page = 1; // Reset page
            console.log('Ordenação alterada para:', filterState.sort);
            fetchFilteredProducts();
        });
    }

    // Botões de paginação
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (filterState.page > 1) {
                filterState.page--;
                fetchFilteredProducts();
            }
        });
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            if (filterState.page < totalPages) {
                filterState.page++;
                fetchFilteredProducts();
            }
        });
    }

    // ===== ADICIONAR AO CARRINHO =====
    function attachAddToCartListeners() {
        document.querySelectorAll('.add-to-cart-btn-listing').forEach(btn => {
            btn.removeEventListener('click', handleAddToCart);
            btn.addEventListener('click', handleAddToCart);
        });
    }

    function handleAddToCart(e) {
        e.preventDefault();
        const productId = this.getAttribute('data-product-id');
        if (typeof addToCart === 'function') {
            addToCart(productId, 1);
        }
    }

    // ===== INICIALIZAR =====
    detectCategory();
    fetchFilteredProducts();
});

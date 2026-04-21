document.addEventListener('DOMContentLoaded', () => {

    // --- FUNÇÕES GLOBAIS AUXILIARES ---
    const formatCurrency = (value) => {
        const numberValue = Number(value);
        if (isNaN(numberValue)) { return "R$ -.--"; }
        return numberValue.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };
    const generateStarsHTML = (rating) => {
        let starsHTML = '';
        const starSVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
        const emptyStarSVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="star-empty"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
        for (let i = 1; i <= 5; i++) {
            starsHTML += (i <= rating) ? starSVG : emptyStarSVG;
        }
        return `<div class="rating-stars">${starsHTML}</div>`;
    };

    // --- Interação Global: Fechar Top Bar ---
    const closeTopBarBtn = document.querySelector('.top-bar .close-btn');
    if (closeTopBarBtn) { closeTopBarBtn.addEventListener('click', () => { closeTopBarBtn.parentElement.style.display = 'none'; }); }

    // --- Menu do Usuário (preview de login/conta) ---
    // NOTA: Lógica do user-menu agora está no header.blade.php
    // para evitar conflitos de múltiplos event listeners


    // --- NOVA LÓGICA DO CARRINHO GLOBAL (LocalStorage) ---

    // Pega o elemento do badge no header
    const cartCountBadge = document.querySelector('.cart-item-count');

    /**
     * Atualiza o badge do carrinho no header (o número).
     * @param {number} totalItems - O número total de itens.
     */
    const updateCartCounter = (totalItems) => {
        if (cartCountBadge) {
            cartCountBadge.textContent = totalItems;
            cartCountBadge.classList.toggle('visible', totalItems > 0);
        }
    };

    /**
     * Lê os itens do carrinho salvos no LocalStorage.
     * @returns {Array} - Um array de objetos (itens do carrinho).
     */
    const getCartFromStorage = () => {
        const cartJSON = localStorage.getItem('joalheriaCart');
        // Se não existir, retorna um array vazio
        return cartJSON ? JSON.parse(cartJSON) : [];
    };

    /**
     * Salva o array de itens do carrinho no LocalStorage.
     * @param {rray} cart - O array de itens do carrinho.
     */
    const saveCartToStorage = (cart) => {
        localStorage.setItem('joalheriaCart', JSON.stringify(cart));
    };

    /**
     * Calcula o total de itens (pela quantidade) do carrinho salvo.
     * @returns {number} - O número total de unidades de produtos.
     */
    const getTotalItemsInCart = () => {
        const cart = getCartFromStorage();
        // Soma o campo 'quantity' de cada item no carrinho
        return cart.reduce((total, item) => total + item.quantity, 0);
    };

    /**
     * Adiciona um produto ao carrinho (ou incrementa a quantidade).
     * Esta função será chamada pelos botões "Adicionar".
     */
    const addItemToCart = (productId, productData = null) => {
        let cart = getCartFromStorage();

        // Verifica se o item já existe
        let existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            existingItem.quantity++; // Se existe, só aumenta a quantidade
        } else {
            // Se productData não foi passado, tenta buscar do botão clicado
            if (!productData) {
                const button = document.querySelector(`[data-product-id="${productId}"]`);
                if (button) {
                    productData = {
                        id: productId,
                        name: button.dataset.productName || `Produto ${productId}`,
                        price: parseFloat(button.dataset.productPrice) || 0,
                        img: button.dataset.productImg || 'img/placeholder.svg',
                        quantity: 1
                    };
                } else {
                    // Fallback se não encontrar o botão
                    productData = {
                        id: productId,
                        name: `Produto ${productId}`,
                        price: 0,
                        img: 'img/placeholder.svg',
                        quantity: 1
                    };
                }
            }

            cart.push(productData);
        }

        saveCartToStorage(cart); // Salva o carrinho atualizado

        // Atualiza o contador no header
        const totalItems = getTotalItemsInCart();
        updateCartCounter(totalItems);

        // Feedback visual (opcional)
        if(cartCountBadge) {
            cartCountBadge.classList.add('pulse-animation');
            setTimeout(() => {
                cartCountBadge.classList.remove('pulse-animation');
            }, 300); // Duração da animação CSS
        }

        console.log('Produto adicionado ao carrinho:', productData);
        console.log('Carrinho atual:', cart);
    };

    // Tornar a função disponível globalmente para fallback de outros scripts
    try {
        window.addItemToCart = addItemToCart;
    } catch (e) {
        console.warn('Não foi possível expor addItemToCart globalmente:', e);
    }

    /**
     * Atualiza o carrinho (LocalStorage) com base nos elementos da página.
     * Usado nas páginas 'carrinho.html' e 'pagamento.html'
     * @param {HTMLElement} container - O elemento que contém os .cart-item
     */
    const updateStorageFromPage = (container) => {
        if (!container) return 0; // Retorna 0 se o container não existir

        const cartItemsElements = container.querySelectorAll('.cart-item');
        let newCart = [];

        cartItemsElements.forEach(itemEl => {
            const id = itemEl.dataset.id || `simulated-${Math.random()}`;
            const name = itemEl.querySelector('h4')?.textContent || 'Produto Simulado';
            const price = parseFloat(itemEl.dataset.price);
            const img = itemEl.querySelector('.cart-item-img img')?.src || 'img/anel1.png';
            const quantityElement = itemEl.querySelector('.quantity-value'); // Seleciona o elemento da quantidade

            // Verifica se o elemento da quantidade existe antes de tentar ler
            const quantity = quantityElement ? parseInt(quantityElement.textContent) : 0;


            if (!isNaN(price) && !isNaN(quantity) && quantity > 0) { // Garante quantidade válida
                newCart.push({
                    id: id,
                    name: name,
                    price: price,
                    img: img,
                    quantity: quantity
                });
            } else {
                console.warn("Item inválido ou com quantidade zero encontrado:", itemEl);
            }
        });

        saveCartToStorage(newCart); // Salva o estado atual da página no LocalStorage

        // Retorna o total de itens calculado desta página
        const totalItems = newCart.reduce((total, item) => total + item.quantity, 0);
        return totalItems;
    };


    // --- INICIALIZAÇÃO GLOBAL DO CONTADOR ---
    // Verifica a URL atual para aplicar suas exceções
    const currentPage = window.location.pathname.split('/').pop();
    const excludedPages = ['cadastro.html', 'login.html', 'pagamento.html'];

    if (!excludedPages.includes(currentPage)) {
        // Se NÃO for uma página excluída, atualiza o contador ao carregar
        console.log("Atualizando contador global (não estamos em login/cadastro/pagamento)");
        const totalItems = getTotalItemsInCart();
        updateCartCounter(totalItems);
    } else {
        // Se ESTIVER em login/cadastro, zeramos o badge (ele não deve aparecer)
        // Em 'pagamento.html' ele será atualizado pela própria lógica da página
        if (currentPage !== 'pagamento.html') {
            updateCartCounter(0); // Esconde o contador
        }
    }
    // --- FIM DA NOVA LÓGICA GLOBAL ---


    // --- LÓGICA DA PÁGINA INICIAL (Novidades / Ver Mais / Ver Menos) ---
    const productGridContainer = document.getElementById('product-grid-container');
    const loadMoreBtn = document.getElementById('load-more-btn');

    if (productGridContainer && loadMoreBtn && !productGridContainer.dataset.inlineLoadMore) { // Só executa se estiver na página inicial e sem lógica inline
        console.log("Home page logic loaded."); // Debug

        const allProducts = [ /* ... Seu array de produtos da Home aqui ... */
            { id: 1, name: 'Anel de Ouro', price: '2.099,90', original: null, img:'img/anel1.png', rating: 4 },
            { id: 2, name: 'Colar de Ouro com Pedras', price: '2.156,50', original: '2.600,00', img:'img/colar1.png', rating: 5 },
            { id: 3, name: 'Chaveiro SUN', price: '1.980,10', original: null, img: 'img/colar3.png', rating: 3 },
            { id: 4, name: 'Blusa Striped T-shirt', price: '1.530,55', original: '2.040,00', img: 'img/colar4.png', rating: 4 },
            { id: 5, name: 'Pulseira Prata Fina', price: '899,00', original: null, img: 'img/colar1.png', rating: 5 },
            { id: 6, name: 'Brinco Argola Ouro', price: '1.250,00', original: '1.500,00', img: 'img/colar1.png', rating: 4 },
            { id: 7, name: 'Anel Solitário Brilhante', price: '3.500,00', original: null, img: 'img/colar1.png', rating: 5 },
            { id: 8, name: 'Corrente Masculina Grossa', price: '4.100,00', original: '4.500,00', img: 'img/colar1.png', rating: 4 },
            { id: 9, name: 'Anel Novo Modelo', price: '999,90', original: null, img: 'img/colar1.png', rating: 4 },
            { id: 10, name: 'Colar Pérolas', price: '1.150,50', original: '1.300,00', img: 'img/colar1.png', rating: 5 },
            { id: 11, name: 'Brinco Pequeno Ouro', price: '780,10', original: null, img: 'img/colar1.png', rating: 3 },
            { id: 12, name: 'Pulseira Couro Masculina', price: '530,55', original: '640,00', img: 'img/colar1.png', rating: 4 },
        ];
        const initialProductsToShow = 8;
        const productsPerLoad = 4;
        let productsShown = 0;

        // Função para exibir um produto na Home
        function displayHomeProduct(product) {
            // Convertendo o preço para o formato numérico (ex: "2.099,90" -> 2099.90)
            const numericPrice = parseFloat(product.price.replace('.', '').replace(',', '.'));

            const productHTML = `
                <a href="detalhe-produto.html?id=${product.id}" class="product-card">
                    <div class="product-image-container">
                        <img src="${product.img}" alt="${product.name}">
                        ${product.original ? '<span class="badge sale-badge">Promoção</span>' : ''}

                        <button class="btn add-to-cart-btn"
                                data-product-id="${product.id}"
                                data-product-name="${product.name}"
                                data-product-price="${numericPrice}"
                                data-product-img="${product.img}">
                            Adicionar
                        </button>

                    </div>
                    <h3>${product.name}</h3>
                    <div class="product-rating">${generateStarsHTML(product.rating)}</div>
                    <p class="price">
                        <span class="sale">R$${product.price}</span>
                        ${product.original ? `<span class="original">R$${product.original}</span>` : ''}
                    </p>
                </a>`;
            productGridContainer.insertAdjacentHTML('beforeend', productHTML);
        }

        // Funções Ver Mais / Ver Menos
        function loadMoreProducts() {
            const nextProducts = allProducts.slice(productsShown, productsShown + productsPerLoad);
            nextProducts.forEach(displayHomeProduct);
            productsShown += nextProducts.length;
            loadMoreBtn.textContent = (productsShown >= allProducts.length) ? 'Ver menos' : 'Ver mais';
        }
        function hideExtraProducts() {
            const allCards = productGridContainer.querySelectorAll('.product-card');
            for (let i = allCards.length - 1; i >= initialProductsToShow; i--) { allCards[i].remove(); }
            productsShown = initialProductsToShow;
            loadMoreBtn.textContent = 'Ver mais';
            loadMoreBtn.style.display = 'inline-block';
        }

        // Carregamento Inicial Home
        allProducts.slice(0, initialProductsToShow).forEach(displayHomeProduct);
        productsShown = initialProductsToShow;
        if (productsShown >= allProducts.length) { loadMoreBtn.style.display = 'none'; }
        else { loadMoreBtn.textContent = 'Ver mais'; loadMoreBtn.style.display = 'inline-block'; }

        // Event Listener Botão Home
        loadMoreBtn.addEventListener('click', () => {
            if (loadMoreBtn.textContent === 'Ver mais') { loadMoreProducts(); }
            else { hideExtraProducts(); }
        });

        // Event Listener Botão Adicionar (na Home)
        productGridContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('add-to-cart-btn')) {
                event.preventDefault(); // Impede a navegação do link <a>

                const button = event.target;
                const productId = button.dataset.productId;

                addItemToCart(productId);

                // Feedback visual no botão
                button.textContent = 'Adicionado!';
                button.classList.add('added');
                setTimeout(() => {
                    button.textContent = 'Adicionar';
                    button.classList.remove('added');
                }, 1500);
            }
        });
    }
    // --- FIM DA LÓGICA DA PÁGINA INICIAL ---


    // --- LÓGICA DA PÁGINA DO CARRINHO ---
    const cartPage = document.querySelector('.cart-layout:not(.checkout-layout)');
    // Quando o carrinho é renderizado pelo backend (flag data-backend-cart), pulamos a lógica antiga de LocalStorage
    if (cartPage && !cartPage.dataset.backendCart) {
        console.log("Cart page logic loaded."); // Debug
        let isCouponApplied = false;
        const WELCOME_COUPON_CODE = "PRIMEIRACOMPRA";
        const cartItemsContainer = cartPage.querySelector('.cart-items');
        const couponForm = cartPage.querySelector('#coupon-form');
        const couponInput = cartPage.querySelector('#coupon-input');
        const couponMessage = cartPage.querySelector('#coupon-message');
        const discountLabel = cartPage.querySelector('#discount-label');
        const subtotalEl = cartPage.querySelector('#subtotal');
        const discountEl = cartPage.querySelector('#discount');
        const totalEl = cartPage.querySelector('#total');

        // === NOVAS REFERÊNCIAS PARA FRETE (INJETADO) ===
        const shippingValueEl = cartPage.querySelector('#shipping-value'); // Onde mostra o VALOR
        const cepForm = cartPage.querySelector('#cep-form');
        const cepInput = cartPage.querySelector('#cep-input');
        const calculateShippingBtn = cartPage.querySelector('#calculate-shipping-btn');
        const shippingMessageEl = cartPage.querySelector('#shipping-message');
        // ===================================


        // --- ADIÇÃO NA PÁGINA DO CARRINHO ---
        // Função para popular o carrinho com dados do LocalStorage
        const populateCartFromStorage = () => {
            if (!cartItemsContainer) { // Verifica se o container existe
                console.error("Cart items container not found for population.");
                return;
            }
            const cart = getCartFromStorage();
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<p class="empty-cart-message">Seu carrinho está vazio.</p>';
                return;
            }

            cartItemsContainer.innerHTML = ''; // Limpa antes de adicionar
            cart.forEach(item => {
                // Normaliza caminho da imagem: aceita URL absoluta, caminho iniciando com '/', ou apenas nome de arquivo
                let imgSrc = item.img || '/img/placeholder.svg';
                // Normalização do caminho da imagem:
                // - se começar com '/' ou 'http' usa como está
                // - se começar com 'img/' adiciona apenas a barra inicial
                // - caso contrário prefixa com '/img/'
                if (imgSrc.startsWith('/') || imgSrc.startsWith('http')) {
                    // usa como está
                } else if (imgSrc.startsWith('img/')) {
                    imgSrc = '/' + imgSrc; // '/img/arquivo.png'
                } else {
                    imgSrc = '/img/' + imgSrc;
                }

                // Codifica espaços para evitar 404 em nomes/pastas com espaço
                imgSrc = imgSrc.replace(/\s/g, '%20');

                // ATENÇÃO: O HTML aqui deve ser IDÊNTICO ao que você usa em 'carrinho.html'
                const itemHTML = `
                <div class="cart-item" data-price="${item.price}" data-id="${item.id}">
                    <div class="cart-item-info">
                        <div class="cart-item-img">
                            <img src="${imgSrc}" alt="${item.name}" onerror="this.src='/img/placeholder.svg'">
                        </div>
                        <div class="cart-item-details">
                            <h4>${item.name}</h4>
                            <p class="cart-item-price">${formatCurrency(item.price)}</p>
                        </div>
                    </div>
                    <div class="cart-item-controls">
                        <div class="quantity-selector">
                            <button class="quantity-btn decrease-qty">-</button>
                            <span class="quantity-value">${item.quantity}</span>
                            <button class="quantity-btn increase-qty">+</button>
                        </div>
                        <button class="delete-item-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 011 1v6a1 1 0 11-2 0V9a1 1 0 011-1zm4 0a1 1 0 011 1v6a1 1 0 11-2 0V9a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                </div>
                `;
                cartItemsContainer.insertAdjacentHTML('beforeend', itemHTML);
            });
        };
        // --- FIM DA ADIÇÃO ---


        // --- Função updateCartTotals (MODIFICADA/SUBSTITUÍDA) ---
        const updateCartTotals = () => {
            let subtotal = 0;
            const totalItems = updateStorageFromPage(cartItemsContainer); // Já salva no storage
            const cartItems = cartItemsContainer ? cartItemsContainer.querySelectorAll('.cart-item') : [];

            cartItems.forEach(item => {
                const price = parseFloat(item.dataset.price);
                const quantityElement = item.querySelector('.quantity-value');
                const quantity = quantityElement ? parseInt(quantityElement.textContent) : 0;
                if (!isNaN(price) && !isNaN(quantity) && quantity > 0) {
                    subtotal += price * quantity;
                } else {
                    console.error("Invalid price or quantity found in cart item:", item);
                }
            });

            let discount = 0;
            if (isCouponApplied) {
                discount = subtotal * 0.20;
                if(discountLabel) discountLabel.textContent = "Desconto (-20%)";
            } else {
                discount = 0;
                if(discountLabel) discountLabel.textContent = "Desconto";
            }

            // === MODIFICAÇÃO AQUI: LER O VALOR ATUAL DO FRETE ===
            let currentShippingCost = 0;
            if (shippingValueEl && shippingValueEl.textContent !== '--') {
                // Tenta converter o texto (ex: "R$ 18,90") de volta para número
                const formattedValue = shippingValueEl.textContent;
                // Remove "R$", espaço, troca vírgula por ponto
                const numericString = formattedValue.replace('R$', '').trim().replace('.', '').replace(',', '.');
                currentShippingCost = parseFloat(numericString);
                if (isNaN(currentShippingCost)) {
                    currentShippingCost = 0; // Fallback se a conversão falhar
                    console.error("Falha ao converter valor do frete para número:", formattedValue);
                }
            }
                // Aplica frete apenas se houver subtotal
            const shipping = (subtotal > 0) ? currentShippingCost : 0;
            // ==================================================

            const total = subtotal - discount + shipping;

            // Atualiza o HTML do resumo
            if(subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);
            if(discountEl) discountEl.textContent = `-${formatCurrency(discount)}`;
            // Atualiza o frete apenas se ele foi calculado (não sobrescreve o '--')
            if (shippingValueEl && shipping !== 0) {
                // Não precisa atualizar aqui, pois o cálculo do frete já atualiza
            } else if (shippingValueEl && subtotal === 0) {
                shippingValueEl.textContent = formatCurrency(0); // Zera frete se carrinho vazio
            }
            if(totalEl) totalEl.textContent = formatCurrency(total);

            updateCartCounter(totalItems); // Atualiza badge do header

            if (totalItems === 0 && cartItemsContainer) {
                cartItemsContainer.innerHTML = '<p class="empty-cart-message">Seu carrinho está vazio.</p>';
                if(shippingValueEl) shippingValueEl.textContent = formatCurrency(0); // Zera frete visualmente
            }
        };

        // Event listener para itens do carrinho
        if (cartItemsContainer) { // Verifica se container existe
            cartItemsContainer.addEventListener('click', (event) => {
                const target = event.target;
                const cartItem = target.closest('.cart-item');
                if (!cartItem) return;

                const quantityElement = cartItem.querySelector('.quantity-value');
                if (!quantityElement) return; // Sai se não encontrar o elemento

                let quantity = parseInt(quantityElement.textContent);

                if (target.classList.contains('increase-qty')) {
                    quantity++;
                    quantityElement.textContent = quantity;
                    updateCartTotals(); // Já salva no storage
                }
                if (target.classList.contains('decrease-qty')) {
                    if (quantity > 1) {
                        quantity--;
                        quantityElement.textContent = quantity;
                        updateCartTotals(); // Já salva no storage
                    }
                }
                if (target.closest('.delete-item-btn')) {
                    cartItem.remove();
                    updateCartTotals(); // Já salva no storage
                }
            });
        } else {
            console.error("Cart items container not found on cart page.");
        }

        // Event listener para cupom
        if (couponForm && couponInput && couponMessage) { // Verifica se elementos existem
            couponForm.addEventListener('submit', (event) => {
                event.preventDefault();
                const code = couponInput.value.trim().toUpperCase();

                if (code === WELCOME_COUPON_CODE) {
                    isCouponApplied = true;
                    couponMessage.textContent = "Cupom 'PRIMEIRACOMPRA' aplicado! 20% de desconto.";
                    couponMessage.className = "coupon-message success";
                    couponInput.disabled = true;
                    event.target.querySelector('button').disabled = true;
                } else {
                    isCouponApplied = false; // Garante que reverte se inválido
                    couponMessage.textContent = "Cupom inválido.";
                    couponMessage.className = "coupon-message error";
                }
                updateCartTotals(); // Recalcula totais
            });
        } else {
            console.warn("Coupon elements (form, input, or message) not found on cart page.");
        }

        // === NOVO LISTENER: CALCULAR FRETE (SIMULAÇÃO) (INJETADO) ===
        if (cepForm && cepInput && calculateShippingBtn && shippingValueEl && shippingMessageEl) {

            // Opcional: Máscara simples para CEP (#####-###)
            cepInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, ''); // Remove não dígitos
                if (value.length > 5) {
                    value = value.substring(0, 5) + '-' + value.substring(5, 8);
                }
                e.target.value = value;
            });

            cepForm.addEventListener('submit', (event) => {
                event.preventDefault(); // Impede envio do formulário
                const cep = cepInput.value.replace(/\D/g, ''); // Pega só os números

                // Validação simples
                if (cep.length !== 8) {
                    shippingMessageEl.textContent = "CEP inválido. Digite 8 números.";
                    shippingMessageEl.className = 'shipping-message error'; // Usa classe CSS
                    shippingValueEl.textContent = '--'; // Reseta valor
                    updateCartTotals(); // Recalcula total sem frete
                    return;
                }

                // Simulação de chamada API
                shippingMessageEl.textContent = "Calculando...";
                shippingMessageEl.className = 'shipping-message loading';
                calculateShippingBtn.disabled = true; // Desabilita botão
                cepInput.disabled = true;

                setTimeout(() => {
                    // Simula um valor de frete (ex: fixo ou aleatório)
                    const simulatedShippingCost = 18.90; // Ou Math.random() * 20 + 10;

                    shippingValueEl.textContent = formatCurrency(simulatedShippingCost);
                    shippingMessageEl.textContent = `Frete calculado para ${cepInput.value}.`; // Mensagem de sucesso (opcional)
                    shippingMessageEl.className = 'shipping-message success'; // (opcional)

                    // Reabilita os campos
                    calculateShippingBtn.disabled = false;
                    cepInput.disabled = false;

                    // IMPORTANTE: Recalcula os totais APÓS atualizar o frete
                    updateCartTotals();

                }, 1500); // Simula 1.5 segundos de espera
            });
        } else {
            console.warn("Elementos do cálculo de frete não encontrados.");
        }
        // ================================================

        // --- MUDANÇA AQUI ---
        // Carregamento inicial ao carregar a página do carrinho
        populateCartFromStorage(); // 1. Popula o carrinho com dados do Storage
        updateCartTotals();       // 2. Calcula os totais com base no que foi populado
    }
    // --- FIM DA LÓGICA DO CARRINHO ---


    // --- LÓGICA DA PÁGINA DE DETALHE DO PRODUTO ---
    const productPage = document.querySelector('.product-details-layout');
    const relatedProductsGrid = document.getElementById('related-products-grid');

    if (productPage) { // Verifica se é a página de detalhe
        console.log("Product detail page logic loaded.");

        // Galeria
        const mainImage = productPage.querySelector('.main-image img');
        const thumbnails = productPage.querySelectorAll('.thumbnail-images img');
        if (mainImage && thumbnails.length > 0) {
            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', () => {
                    mainImage.src = thumb.src;
                });
            });
        }

        // Tabs
        const tabLinks = document.querySelectorAll('.tabs-nav .tab-link');
        const tabPanes = document.querySelectorAll('.tabs-content .tab-pane');
        if (tabLinks.length > 0 && tabPanes.length > 0) {
            tabLinks.forEach(link => {
                link.addEventListener('click', () => {
                    const targetTab = link.dataset.tab;
                    tabLinks.forEach(l => l.classList.remove('active'));
                    tabPanes.forEach(p => p.classList.remove('active'));
                    link.classList.add('active');
                    document.getElementById(targetTab)?.classList.add('active');
                });
            });
        }

        // Quantity selector
        const qtyValue = productPage.querySelector('.quantity-value');
        const qtyPlus = productPage.querySelector('.qty-plus');
        const qtyMinus = productPage.querySelector('.qty-minus');

        if (qtyValue && qtyPlus && qtyMinus) {
            qtyPlus.addEventListener('click', (e) => {
                e.preventDefault();
                let current = parseInt(qtyValue.textContent);
                qtyValue.textContent = current + 1;
            });

            qtyMinus.addEventListener('click', (e) => {
                e.preventDefault();
                let current = parseInt(qtyValue.textContent);
                if (current > 1) {
                    qtyValue.textContent = current - 1;
                }
            });
        }

        // Add to cart button na página de detalhe
        const addToCartBtnDetail = productPage.querySelector('.add-to-cart-btn');
        if (addToCartBtnDetail) {
            addToCartBtnDetail.addEventListener('click', async (e) => {
                e.preventDefault();
                const productId = addToCartBtnDetail.dataset.productId;
                const quantity = parseInt(qtyValue?.textContent || '1');

                console.log(`Adicionando ${quantity}x produto ${productId} ao carrinho`);

                // Preferir usar a API do servidor (se disponível). Caso a API não exista
                // ou retorne falha, usamos o LocalStorage como fallback.
                if (typeof window.addToCart === 'function') {
                    try {
                        const success = await window.addToCart(productId, quantity);
                        if (!success) {
                            for (let i = 0; i < quantity; i++) addItemToCart(productId);
                        }
                    } catch (err) {
                        console.error('Erro ao chamar addToCart do servidor:', err);
                        for (let i = 0; i < quantity; i++) addItemToCart(productId);
                    }
                } else {
                    for (let i = 0; i < quantity; i++) addItemToCart(productId);
                }

                // Feedback visual
                const originalText = addToCartBtnDetail.textContent;
                addToCartBtnDetail.textContent = 'Adicionado!';
                addToCartBtnDetail.style.background = 'var(--color-verified-green)';
                setTimeout(() => {
                    addToCartBtnDetail.textContent = originalText;
                    addToCartBtnDetail.style.background = '';
                }, 1500);
            });
        }

        // Comment modal
        const commentModalOverlay = document.getElementById('comment-modal-overlay');
        const openCommentBtn = document.getElementById('open-comment-modal');
        const closeCommentBtn = document.getElementById('close-comment-modal');
        const commentForm = document.getElementById('comment-form');
        const commentsGrid = document.querySelector('.comments-grid');

        if (openCommentBtn && commentModalOverlay) {
            openCommentBtn.addEventListener('click', () => {
                commentModalOverlay.style.display = 'flex';
                commentModalOverlay.style.alignItems = 'center';
                commentModalOverlay.style.justifyContent = 'center';
            });
        }

        if (closeCommentBtn && commentModalOverlay) {
            closeCommentBtn.addEventListener('click', () => {
                commentModalOverlay.style.display = 'none';
            });

            commentModalOverlay.addEventListener('click', (e) => {
                if (e.target === commentModalOverlay) {
                    commentModalOverlay.style.display = 'none';
                }
            });
        }

        // Star rating selection
        let selectedRating = 0;
        const starRatings = document.querySelectorAll('.star-rating');
        starRatings.forEach(star => {
            star.style.cursor = 'pointer';
            star.style.fontSize = '1.5rem';
            star.addEventListener('click', () => {
                selectedRating = parseInt(star.dataset.value);
                starRatings.forEach((s, idx) => {
                    s.style.opacity = idx < selectedRating ? '1' : '0.3';
                });
            });
        });

        // Comment form submission
        if (commentForm && commentsGrid) {
            commentForm.addEventListener('submit', (e) => {
                e.preventDefault();

                const name = document.getElementById('comment-name').value;
                const text = document.getElementById('comment-text').value;

                if (!selectedRating) {
                    alert('Por favor, selecione uma avaliação');
                    return;
                }

                // Generate stars HTML
                let starsHTML = '';
                for (let i = 0; i < 5; i++) {
                    starsHTML += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="' + (i < selectedRating ? 'currentColor' : 'none') + '" stroke="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
                }

                const newComment = document.createElement('div');
                newComment.className = 'comment-card';
                newComment.innerHTML = `
                    <div class="user-info">
                        <div class="user"><span>${name}</span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg></div>
                        <span class="options">...</span>
                    </div>
                    <div class="rating">${starsHTML}</div>
                    <p class="comment-body">"${text}"</p>
                    <p class="comment-date">Postado em ${new Date().toLocaleDateString('pt-BR', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                `;

                commentsGrid.insertBefore(newComment, commentsGrid.firstChild);

                // Reset form
                commentForm.reset();
                selectedRating = 0;
                starRatings.forEach(s => s.style.opacity = '1');
                commentModalOverlay.style.display = 'none';

                alert('Comentário adicionado com sucesso!');
            });
        }


        // Lógica dos PRODUTOS RELACIONADOS (executa aqui)
        if (relatedProductsGrid) {
            console.log("Loading related products..."); // Debug
            const relatedProducts = [
                { id: 13, name: 'Polo with Contrast Trims', price: '2.142,25', original: '2.424,57', img: 'img/anel1.png', rating: 4 },
                { id: 14, name: 'Gradient Graphic T-shirt', price: '145,59', original: null, img: 'img/anel2.png', rating: 5 },
                { id: 15, name: 'Polo with Tigging Detalis', price: '1805,56', original: null, img: 'img/colar1.png', rating: 3 },
                { id: 16, name: 'Black Striped T-shirt', price: '1.200,00', original: '1.580,25', img: 'img/anel2.png', rating: 4 }
            ];
            function displayRelatedProduct(product) {
                const numericPrice = parseFloat(product.price.replace('.', '').replace(',', '.'));
                const productHTML = `
                    <a href="detalhe-produto.html?id=${product.id}" class="product-card">
                        <div class="product-image-container">
                            <img src="${product.img}" alt="${product.name}">
                            ${product.original ? '<span class="badge sale-badge">Sale</span>' : ''}

                            <button class="btn add-to-cart-btn"
                                    data-product-id="${product.id}"
                                    data-product-name="${product.name}"
                                    data-product-price="${numericPrice}"
                                    data-product-img="${product.img}">
                                Adicionar
                            </button>
                        </div>
                        <h3>${product.name}</h3>
                        <div class="product-rating">${generateStarsHTML(product.rating)}</div>
                        <p class="price">
                            <span class="sale">R$${product.price}</span>
                            ${product.original ? `<span class="original">R$${product.original}</span>` : ''}
                        </p>
                    </a>`;
                relatedProductsGrid.insertAdjacentHTML('beforeend', productHTML);
            }
            relatedProductsGrid.innerHTML = ''; // Limpa antes de adicionar
            relatedProducts.forEach(displayRelatedProduct);

            relatedProductsGrid.addEventListener('click', function(event) {
                if (event.target.classList.contains('add-to-cart-btn')) {
                    event.preventDefault();
                    const button = event.target;
                    const productId = button.dataset.productId;

                    addItemToCart(productId); // Chama a função global

                    button.textContent = 'Adicionado!';
                    button.classList.add('added');
                    setTimeout(() => {
                        button.textContent = 'Adicionar';
                        button.classList.remove('added');
                    }, 1500);
                }
            });
        } else {
            console.warn("Related products grid container not found on product detail page.");
        }
    }


    // --- Lógica das Páginas de Login e Cadastro ---
    const authForm = document.querySelector('.auth-form');
    if (authForm) {
        console.log("Auth page logic loaded.");
        authForm.addEventListener('submit', (event) => { /* ... (Código de validação) ... */ });
    }


    // --- LÓGICA DA PÁGINA DE PAGAMENTO (MODAL, CRONÔMETRO, INTERAÇÃO RESUMO) ---
    const checkoutPage = document.querySelector('.checkout-layout');
    if (checkoutPage) {
        console.log("Checkout page logic loaded.");

        const overlay = document.getElementById('payment-overlay');
        const modal = document.getElementById('payment-modal');
        const paymentForm = document.getElementById('payment-form');
        const addCardBtn = document.getElementById('add-card-btn');
        const timerDisplay = document.getElementById('payment-timer');
        const checkoutItemsContainer = checkoutPage.querySelector('.checkout-summary .cart-items');
        const subtotalElCheckout = checkoutPage.querySelector('#subtotal');
        const discountElCheckout = checkoutPage.querySelector('#discount');
        const shippingElCheckout = checkoutPage.querySelector('#shipping');
        const totalElCheckout = checkoutPage.querySelector('#total');
        const discountLabelCheckout = checkoutPage.querySelector('#discount-label');

        let timerInterval; // Variável para o timer

        // --- ADIÇÃO NA PÁGINA DE PAGAMENTO ---
        // Função para popular o resumo com dados do LocalStorage
        const populateCheckoutFromStorage = () => {
            if (!checkoutItemsContainer) {
                console.error("Checkout items container not found for population.");
                return;
            }
            const cart = getCartFromStorage();
            if (cart.length === 0) {
                checkoutItemsContainer.innerHTML = '<p class="empty-cart-message">Seu carrinho está vazio.</p>';
                window.location.href = 'carrinho.html';
                return;
            }

            checkoutItemsContainer.innerHTML = ''; // Limpa
            cart.forEach(item => {
                // ATENÇÃO: O HTML aqui deve ser IDÊNTICO ao do resumo em 'pagamento.html'
                const itemHTML = `
                <div class="cart-item" data-price="${item.price}" data-id="${item.id}">
                    <div class="cart-item-info">
                        <div class="cart-item-img">
                            <img src="${item.img}" alt="${item.name}">
                        </div>
                        <div class="cart-item-details">
                            <h4>${item.name}</h4>
                            <p class="cart-item-price">${formatCurrency(item.price)}</p>
                        </div>
                    </div>
                    <div class="cart-item-controls">
                        <div class="quantity-selector">
                            <button class="quantity-btn decrease-qty">-</button>
                            <span class="quantity-value">${item.quantity}</span>
                            <button class="quantity-btn increase-qty">+</button>
                        </div>
                        <button class="delete-item-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 011 1v6a1 1 0 11-2 0V9a1 1 0 011-1zm4 0a1 1 0 011 1v6a1 1 0 11-2 0V9a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                </div>
                `;
                checkoutItemsContainer.insertAdjacentHTML('beforeend', itemHTML);
            });
        };


        // Função para recalcular os totais NA PÁGINA DE CHECKOUT
        const updateCheckoutTotals = () => {
            let subtotal = 0;

            // --- MUDANÇA AQUI ---
            // Salva o estado atual da página no LocalStorage E obtém o total de itens
            const totalItems = updateStorageFromPage(checkoutItemsContainer);
            // --- FIM DA MUDANÇA ---

            const checkoutItems = checkoutItemsContainer ? checkoutItemsContainer.querySelectorAll('.cart-item') : [];
            checkoutItems.forEach(item => {
                const price = parseFloat(item.dataset.price);
                const quantityElement = item.querySelector('.quantity-value');
                const quantity = quantityElement ? parseInt(quantityElement.textContent) : 0;
                if (!isNaN(price) && !isNaN(quantity) && quantity > 0) { subtotal += price * quantity; }
            });

            const discount = (getCartFromStorage().length > 0) ? subtotal * 0.20 : 0;
            if(discountLabelCheckout) discountLabelCheckout.textContent = "Desconto (-20%)";

            const shipping = (subtotal > 0) ? 15.99 : 0;
            const total = subtotal - discount + shipping;

            if(subtotalElCheckout) subtotalElCheckout.textContent = formatCurrency(subtotal);
            if(discountElCheckout) discountElCheckout.textContent = `-${formatCurrency(discount)}`;
            if(shippingElCheckout) shippingElCheckout.textContent = formatCurrency(shipping);
            if(totalElCheckout) totalElCheckout.textContent = formatCurrency(total);

            updateCartCounter(totalItems); // Atualiza header

            if (totalItems === 0) {
                alert("Seu carrinho ficou vazio! Redirecionando...");
                window.location.href = 'carrinho.html';
            }
        };

        // Adiciona listener para os botões +/-/remover DENTRO do resumo do checkout
        if (checkoutItemsContainer) {
            checkoutItemsContainer.addEventListener('click', (event) => {
                const target = event.target; const cartItem = target.closest('.cart-item');
                if (!cartItem) return;
                const quantityElement = cartItem.querySelector('.quantity-value');
                if (!quantityElement) return;

                let quantity = parseInt(quantityElement.textContent);
                if (target.classList.contains('increase-qty')) { quantity++; quantityElement.textContent = quantity; updateCheckoutTotals(); }
                if (target.classList.contains('decrease-qty')) { if (quantity > 1) { quantity--; quantityElement.textContent = quantity; updateCheckoutTotals(); } }
                if (target.closest('.delete-item-btn')) { cartItem.remove(); updateCheckoutTotals(); }
            });

            // --- MUDANÇA AQUI ---
            // Cálculo inicial para a página de checkout
            populateCheckoutFromStorage(); // 1. Popula
            updateCheckoutTotals();       // 2. Calcula
        } else { console.error("Checkout summary items container not found."); }


        // --- LÓGICA DO CRONÔMETRO ---
        if (timerDisplay) {
            let timerDurationSeconds = 10 * 60; // 10 minutos

            function updateTimerDisplay() {
                const minutes = Math.floor(timerDurationSeconds / 60);
                const seconds = timerDurationSeconds % 60;
                const formattedSeconds = seconds < 10 ? '0' + seconds : seconds;
                timerDisplay.textContent = `${minutes}:${formattedSeconds}`;
                if (timerDurationSeconds <= 0) { timerDisplay.style.color = 'red'; }
                else if (timerDurationSeconds <= 60) { timerDisplay.style.color = 'orange'; }
                else { timerDisplay.style.color = 'var(--color-link-red)';}
            }

            function startTimer() {
                console.log("Starting payment timer...");
                if (timerInterval) clearInterval(timerInterval);
                updateTimerDisplay(); // Mostra valor inicial
                timerInterval = setInterval(() => {
                    timerDurationSeconds--;
                    updateTimerDisplay();
                    if (timerDurationSeconds < 0) {
                        clearInterval(timerInterval);
                        timerDisplay.textContent = "0:00";
                        alert("Seu tempo para pagamento esgotou! Você será redirecionado para o carrinho.");
                        if(paymentForm) paymentForm.querySelector('button[type="submit"]').disabled = true;

                        window.location.href = 'carrinho.html';
                    }
                }, 1000);
            }
            startTimer(); // Inicia o timer
        } else { console.error("Element #payment-timer not found!"); }
        // --- FIM LÓGICA DO CRONÔMETRO ---


        // Lógica para o botão "Continue" (mostrar modal)
        if (paymentForm && overlay && modal) {
            paymentForm.addEventListener('submit', (event) => {
                event.preventDefault();
                console.log("Payment form submitted!");

                // Para o timer
                if (timerInterval) {
                    clearInterval(timerInterval);
                    console.log("Timer stopped on payment submission.");
                }

                // Captura dados do pedido antes de limpar
                const cart = getCartFromStorage();
                const orderSummary = {
                    items: cart,
                    subtotal: subtotalElCheckout?.textContent || 'R$0,00',
                    discount: discountElCheckout?.textContent || 'R$0,00',
                    shipping: shippingElCheckout?.textContent || 'R$0,00',
                    total: totalElCheckout?.textContent || 'R$0,00',
                    date: new Date().toLocaleString('pt-BR'),
                    orderNumber: '#' + Math.random().toString(36).substr(2, 9).toUpperCase()
                };

                console.log('Pedido realizado:', orderSummary);

                // Limpa carrinho
                saveCartToStorage([]);
                updateCartCounter(0);

                // Mostra modal de sucesso
                overlay.classList.add('active');
                modal.classList.add('active');

                // Atualiza texto do modal
                const modalTitle = modal.querySelector('h3');
                if (modalTitle) {
                    modalTitle.innerHTML = `Compra realizada com sucesso!<br><small style="font-size: 0.8em; font-weight: normal;">Pedido ${orderSummary.orderNumber}</small>`;
                }

                setTimeout(() => {
                    overlay.classList.remove('active');
                    modal.classList.remove('active');
                    window.location.href = '/'; // Redireciona para a Home
                }, 4000);
            });
        } else { console.error("Could not attach payment submit listener..."); }

        // Lógica Adicionar Cartão
        if (addCardBtn) { addCardBtn.addEventListener('click', () => { alert('Funcionalidade "Cadastrar Novo Cartão" ainda não implementada.'); }); }
    }
    // --- FIM DO CÓDIGO DE PAGAMENTO ---


    // --- LÓGICA DO SLIDER DE PREÇO E FILTRAGEM (PÁGINA DE LISTAGEM) ---
    const priceSlider = document.getElementById('price-slider-input');
    const priceValueDisplay = document.getElementById('price-slider-value');

    // Esta lógica só roda se os elementos do slider existirem
    if (priceSlider && priceValueDisplay) {
        console.log("Listing page logic (slider, filter, pagination) loaded.");

        // --- 1. Referências reais dos elementos na página ---
        const listingGrid = document.getElementById('product-listing');
        const applyFiltersBtn = document.getElementById('apply-filters');
        const paginationContainer = document.querySelector('.pagination-container') || document.querySelector('.pagination');
        const filterCountDisplay = document.getElementById('filter-counter');
        const categoryFilters = document.querySelectorAll('.listing-sidebar .filter-item');
        const colorFilters = document.querySelectorAll('.listing-sidebar .color-swatch');
        const brandFilters = document.querySelectorAll('#brand-filters .filter-item');

        // Snapshot dos cards já renderizados no Blade
        // const productCards = listingGrid ? Array.from(listingGrid.querySelectorAll('.product-card')) : [];

        // Estado dos filtros atuais
        let currentFilters = {
            price: priceSlider ? parseFloat(priceSlider.value) : 10000,
            category: null,
            color: null,
            brand: null
        };

        const formatSliderPrice = (value) => `R$${Number(value).toLocaleString('pt-BR')}`;

        function applyFilterVisibility() {
            if (!listingGrid) return;

            const maxPrice = priceSlider ? parseFloat(priceSlider.value) : 10000;
            let visibleCount = 0;

            // Reseleciona os cards a cada chamada (para pegar os cards da página atual)
            const productCards = Array.from(listingGrid.querySelectorAll('.product-card'));

            productCards.forEach(card => {
                const cardPrice = parseFloat(card.dataset.price || '0');
                const cardCategory = (card.dataset.type || '').toLowerCase();
                const cardColor = (card.dataset.color || '').toLowerCase();
                const cardBrand = (card.dataset.brand || '').toUpperCase();

                const priceMatch = cardPrice <= maxPrice;
                const categoryMatch = !currentFilters.category || cardCategory === currentFilters.category;
                const colorMatch = !currentFilters.color || cardColor === currentFilters.color;
                const brandMatch = !currentFilters.brand || cardBrand === currentFilters.brand;

                const show = priceMatch && categoryMatch && colorMatch && brandMatch;
                card.style.display = show ? 'flex' : 'none';
                if (show) visibleCount += 1;
            });

            // Atualiza contador
            if (filterCountDisplay) {
                filterCountDisplay.textContent = `${visibleCount} produtos encontrados`;
            }

            // Mensagem de vazio
            const emptyMsg = listingGrid.querySelector('.empty-filter-message');
            if (visibleCount === 0) {
                if (!emptyMsg) {
                    listingGrid.insertAdjacentHTML('beforeend', '<p class="empty-filter-message" style="grid-column: 1 / -1; text-align: center; padding: 2rem;">Nenhum produto encontrado com estes filtros.</p>');
                }
            } else if (emptyMsg) {
                emptyMsg.remove();
            }
        }

        // --- Listeners ---
        if(priceSlider) {
            priceSlider.addEventListener('input', (event) => {
                if(priceValueDisplay) priceValueDisplay.textContent = formatSliderPrice(event.target.value);
                applyFilterVisibility();
            });
        }

        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', (e) => {
                e.preventDefault();
                applyFilterVisibility();
            });
        }

        // Categoria
        if (categoryFilters.length > 0) {
            categoryFilters.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    categoryFilters.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    const categoryText = link.dataset.category || link.querySelector('span:first-child')?.textContent?.toLowerCase()?.trim();
                    currentFilters.category = categoryText === 'todos' ? null : categoryText;
                    applyFilterVisibility();
                });
            });
        }

        // Cor
        if (colorFilters.length > 0) {
            colorFilters.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    colorFilters.forEach(c => c.classList.remove('active'));
                    btn.classList.add('active');
                    const color = btn.dataset.color || null;
                    currentFilters.color = color;
                    applyFilterVisibility();
                });
            });
        }

        // Marca
        if (brandFilters.length > 0) {
            brandFilters.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    brandFilters.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const brand = btn.dataset.brand || null;
                    currentFilters.brand = brand === 'todos' ? null : brand;
                    applyFilterVisibility();
                });
            });
        }

        // Carga inicial
        if(priceValueDisplay && priceSlider) {
            priceValueDisplay.textContent = formatSliderPrice(priceSlider.value);
        }
        applyFilterVisibility();

        // Event listener para botões de adicionar ao carrinho nas listagens
        if (listingGrid) {
            listingGrid.addEventListener('click', (e) => {
                if (e.target.classList.contains('add-to-cart-btn-listing')) {
                    e.preventDefault();
                    e.stopPropagation();

                    const btn = e.target;
                    const productId = btn.dataset.productId;

                    addItemToCart(productId);

                    const originalText = btn.textContent;
                    btn.textContent = 'Adicionado!';
                    btn.style.background = 'var(--color-verified-green)';

                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.style.background = '';
                    }, 1500);
                }
            });
        }
    }
    // --- FIM DA LÓGICA DE FILTRAGEM ---


    // --- Lógica do Formulário de Contato ---
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Impede o recarregamento da página

            const email = contactForm.querySelector('#contact-email').value;
            const message = contactForm.querySelector('#contact-message').value;

            if (email && message) {
                alert('Mensagem enviada com sucesso! (Simulação)');
                contactForm.reset();
            } else {
                alert('Por favor, preencha todos os campos.');
            }
        });
    }

    // ===========================================
    //         LÓGICA DO PAINEL ADMIN
    // ===========================================
    const adminArea = document.querySelector('.admin-main'); // Verifica se estamos no admin

    if (adminArea) {
        console.log("Admin JS loaded.");

        // --- Troca visual das Abas (Tabs) ---
        const adminTabs = adminArea.querySelectorAll('.admin-tabs a');
        if (adminTabs.length > 0) {
            adminTabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    // Não previne o default aqui, pois queremos navegar entre páginas HTML estáticas
                    // e.preventDefault();

                    // Apenas para efeito visual se ficasse na mesma página (não necessário agora)
                    // adminTabs.forEach(t => t.classList.remove('active'));
                    // tab.classList.add('active');
                });
            });
            // Garante que a aba correta já tem a classe 'active' no HTML carregado
        }

        // --- Preview da Imagem no Cadastro de Produto ---
        const productImageInput = adminArea.querySelector('#product_image');
        const imagePlaceholder = adminArea.querySelector('.image-placeholder');

        if (productImageInput && imagePlaceholder) {
            productImageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        // Cria uma tag img se não existir, ou atualiza a existente
                        let img = imagePlaceholder.querySelector('img');
                        if (!img) {
                            img = document.createElement('img');
                            imagePlaceholder.innerHTML = ''; // Limpa o ícone placeholder
                            imagePlaceholder.appendChild(img);
                        }
                        img.src = e.target.result;
                        img.alt = "Preview da imagem selecionada";
                    }
                    reader.readAsDataURL(file);
                } else {
                    // Limpa o preview se o arquivo não for imagem ou for desmarcado
                    imagePlaceholder.innerHTML = '<i class="fas fa-image fa-3x"></i>'; // Volta o ícone
                }
            });
        }

        // --- Funcionalidade Dropdown Filtros (Exemplo Simples) ---
        const filterButton = adminArea.querySelector('.admin-action-bar .dropdown-toggle');
        // Você precisaria de um elemento para ser o dropdown menu, ex: <div class="dropdown-menu">...</div>
        // const filterDropdown = adminArea.querySelector('.filter-dropdown-menu');

        if (filterButton /*&& filterDropdown*/) {
            filterButton.addEventListener('click', () => {
                // filterDropdown.classList.toggle('show'); // Alterna a visibilidade do menu
                console.log("Botão de filtro clicado! (Dropdown menu não implementado no HTML)"); // Placeholder
            });

            // Opcional: Fechar dropdown se clicar fora dele
            // document.addEventListener('click', (event) => {
            //     if (!filterButton.contains(event.target) && !filterDropdown.contains(event.target)) {
            //         filterDropdown.classList.remove('show');
            //     }
            // });
        }

    } // Fim do if (adminArea)

    // --- HANDLER GLOBAL: BOTÕES 'VOLTAR' ---
    // Garante que todos os botões com o atributo `data-history-back` executem
    // um `history.back()` com fallback para `document.referrer` ou home.
    try {
        const backButtons = document.querySelectorAll('[data-history-back]');
        if (backButtons && backButtons.length > 0) {
            backButtons.forEach(btn => {
                // evita duplicar listeners caso o script seja reinjetado
                btn.removeEventListener('click', btn._historyBackHandler);
                const handler = function(e) {
                    e.preventDefault();
                    // Se houver histórico, voltamos uma página
                    if (window.history.length > 1) {
                        window.history.back();
                        return;
                    }
                    // Senão, usamos o referrer se disponível
                    if (document.referrer) {
                        window.location.href = document.referrer;
                        return;
                    }
                    // Fallback final: ir para a home
                    window.location.href = '/';
                };
                btn._historyBackHandler = handler;
                btn.addEventListener('click', handler);
            });
        }
    } catch (err) {
        console.error('Erro ao vincular botões de voltar:', err);
    }

}); // Fim do addEventListener DOMContentLoaded

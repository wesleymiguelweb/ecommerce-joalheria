/**
 * Sistema de Carrinho - Elegance Joias
 * Gerencia adição, remoção e atualização de produtos no carrinho
 */

// Configuração do CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Fallback helper para adicionar ao carrinho local quando backend falha ou redireciona
function fallbackAddToLocalCart(productId) {
    try {
        if (typeof window.addItemToCart === 'function') {
            window.addItemToCart(productId);
            const localCart = JSON.parse(localStorage.getItem('joalheriaCart') || '[]');
            const localCount = localCart.reduce((s, it) => s + (it.quantity || 1), 0);
            updateCartCount(localCount);
            showNotification('Produto adicionado ao carrinho (modo convidado)', 'success');
            return true;
        }
    } catch (err) {
        console.warn('Fallback para LocalStorage falhou:', err);
    }
    return false;
}

/**
 * Adiciona produto ao carrinho
 */
async function addToCart(productId, quantity = 1, productData = null) {
    console.log('addToCart chamado:', { productId, quantity, productData });

    try {
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });

        console.log('Response status:', response.status);

        if (response.redirected) {
            if (fallbackAddToLocalCart(productId)) return true;
            window.location.href = response.url;
            return false;
        }

        // Tentativa de parse JSON com fallback para ajudar no debug
        let data = null;
        try {
            data = await response.json();
        } catch (err) {
            const text = await response.text().catch(() => null);
            console.warn('Resposta não-JSON do servidor ao adicionar ao carrinho:', response.status, text);
            if (response.status === 419) {
                showNotification('Sessão expirada (CSRF). Recarregue a página e faça login.', 'warning');
            } else if (response.status === 401) {
                if (fallbackAddToLocalCart(productId)) return true;
                window.location.href = '/login';
            } else {
                if (fallbackAddToLocalCart(productId)) return true;
                showNotification('Não foi possível adicionar ao carrinho. Tente novamente.', 'warning');
            }
            return false;
        }

        console.log('Response data:', data);

        if (data.success) {
            updateCartCount(data.cart_count);
            showNotification('Produto adicionado ao carrinho!', 'success');
            return true;
        } else {
            // Se o backend recusar, ainda permitimos carrinho local para não quebrar UX guest
            if (fallbackAddToLocalCart(productId)) return true;
            showNotification(data.message || 'Erro ao adicionar produto', 'error');
            return false;
        }
    } catch (error) {
        console.error('Erro:', error);
        if (fallbackAddToLocalCart(productId)) return true;
        showNotification('Erro ao adicionar produto ao carrinho', 'error');
        return false;
    }
}

/**
 * Atualiza quantidade de um produto no carrinho
 */
async function updateCartItem(productId, quantity) {
    try {
        const response = await fetch(`/cart/update/${productId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ quantity })
        });
        let data = null;
        try {
            data = await response.json();
        } catch (err) {
            const text = await response.text().catch(() => null);
            console.warn('Resposta não-JSON em updateCartItem:', response.status, text);
            showNotification('Erro ao atualizar o carrinho', 'error');
            return;
        }

        if (data && data.success) {
            showNotification('Carrinho atualizado!', 'success');
            // Recarregar página do carrinho
            location.reload();
        } else {
            showNotification((data && data.message) || 'Erro ao atualizar', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao atualizar carrinho', 'error');
    }
}

/**
 * Remove produto do carrinho
 */
async function removeFromCart(productId) {
    if (!confirm('Deseja remover este produto do carrinho?')) {
        return;
    }

    try {
        const response = await fetch(`/cart/remove/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        let data = null;
        try {
            data = await response.json();
        } catch (err) {
            const text = await response.text().catch(() => null);
            console.warn('Resposta não-JSON em removeFromCart:', response.status, text);
            showNotification('Erro ao remover produto', 'error');
            return;
        }

        if (data && data.success) {
            showNotification('Produto removido!', 'success');
            location.reload();
        } else {
            showNotification((data && data.message) || 'Erro ao remover', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao remover produto', 'error');
    }
}

/**
 * Aplica cupom de desconto
 */
async function applyCoupon() {
    const couponInput = document.getElementById('coupon-code');
    const code = couponInput?.value.trim();

    if (!code) {
        showNotification('Digite um código de cupom', 'warning');
        return;
    }

    try {
        // Se o carrinho do backend estiver vazio, enviamos também o carrinho local
        const localCart = JSON.parse(localStorage.getItem('joalheriaCart') || '[]');

        const response = await fetch('/cart/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ code, cart: localCart })
        });

        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        let data = null;
        try {
            data = await response.json();
        } catch (err) {
            const text = await response.text().catch(() => null);
            console.warn('Resposta não-JSON em applyCoupon:', response.status, text);
            showNotification('Não foi possível aplicar o cupom. Tente novamente.', 'warning');
            return;
        }

        if (data && data.success) {
            showNotification('Cupom aplicado com sucesso!', 'success');
            location.reload();
        } else {
            showNotification((data && data.message) || 'Cupom inválido', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao aplicar cupom', 'error');
    }
}

/**
 * Valida CEP e calcula frete
 */
async function validateCep() {
    const cepInput = document.getElementById('postal-code');
    const cep = cepInput?.value.replace(/\D/g, '');

    if (!cep || cep.length !== 8) {
        showNotification('Digite um CEP válido', 'warning');
        return;
    }

    try {
        const response = await fetch('/cart/validate-cep', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ cep })
        });

        let data = null;
        try {
            data = await response.json();
        } catch (err) {
            const text = await response.text().catch(() => null);
            console.warn('Resposta não-JSON em validateCep:', response.status, text);
            showNotification('Erro ao validar CEP', 'error');
            return;
        }

        if (data.success) {
            // Preencher campos de endereço
            if (data.address) {
                document.getElementById('address')?.value =
                    `${data.address.logradouro}, ${data.address.bairro}, ${data.address.localidade} - ${data.address.uf}`;
            }

            // Mostrar valor do frete
            if (data.shipping) {
                const shippingElement = document.getElementById('shipping-cost');
                if (shippingElement) {
                    shippingElement.textContent = `R$ ${data.shipping.toFixed(2).replace('.', ',')}`;
                }
            }

            showNotification('CEP validado!', 'success');
        } else {
            showNotification(data.message || 'CEP não encontrado', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao validar CEP', 'error');
    }
}

/**
 * Atualiza contador do carrinho no header
 */
function updateCartCount(count) {
    const cartCounters = document.querySelectorAll('.cart-count, #cart-count');
    cartCounters.forEach(counter => {
        counter.textContent = count || 0;
        if (count > 0) {
            counter.style.display = 'inline-block';
        }
    });
}

/**
 * Mostra notificação ao usuário
 */
function showNotification(message, type = 'info') {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    // Estilos inline
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        max-width: 300px;
    `;

    // Cores por tipo
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    notification.style.backgroundColor = colors[type] || colors.info;

    // Adicionar ao body
    document.body.appendChild(notification);

    // Remover após 3 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Event Listeners quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {

    // Event Delegation: Botões "Adicionar ao Carrinho"
    // Isso funciona para botões criados dinamicamente também
    document.addEventListener('click', async function(e) {
        const button = e.target.closest('.add-to-cart-btn, .add-to-cart-btn-hover, .add-to-cart-btn-listing, .btn-add-cart');

        if (!button) return;

        e.preventDefault();
        e.stopPropagation();

        const productId = button.dataset.productId || button.getAttribute('data-product-id');

        // Buscar quantity-value: primeiro no mesmo container, depois na página toda
        let quantityElement = button.closest('.product-info, .product-card, .controls')?.querySelector('.quantity-value');
        if (!quantityElement) {
            quantityElement = button.parentElement?.querySelector('.quantity-value');
        }
        if (!quantityElement) {
            quantityElement = document.querySelector('.quantity-value');
        }

        const quantity = quantityElement ? parseInt(quantityElement.textContent) : 1;

        if (!productId) {
            showNotification('Erro: ID do produto não encontrado', 'error');
            return;
        }

        await addToCart(productId, quantity);
    }, true); // Use capture phase para garantir que funciona mesmo com stopPropagation

    // Seletores de quantidade (+ e -)
    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const valueElement = this.parentElement.querySelector('.quantity-value');
            if (valueElement) {
                let currentValue = parseInt(valueElement.textContent) || 1;
                valueElement.textContent = currentValue + 1;
            }
        });
    });

    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const valueElement = this.parentElement.querySelector('.quantity-value');
            if (valueElement) {
                let currentValue = parseInt(valueElement.textContent) || 1;
                if (currentValue > 1) {
                    valueElement.textContent = currentValue - 1;
                }
            }
        });
    });

    // Botão aplicar cupom
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', applyCoupon);
    }

    // Botão validar CEP
    const validateCepBtn = document.getElementById('validate-cep-btn');
    if (validateCepBtn) {
        validateCepBtn.addEventListener('click', validateCep);
    }

    // Máscara de CEP
    const cepInput = document.getElementById('postal-code');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5, 8);
            }
            e.target.value = value;
        });
    }
});

// Adicionar estilos de animação
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

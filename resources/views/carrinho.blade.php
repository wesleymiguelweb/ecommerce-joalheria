@extends('layouts.app')

@section('title', 'Carrinho - Elegance Joias')

@section('content')
@php
    $cartItems = session('cart', []);
    $coupon = session('coupon');
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $discount = $coupon['discount'] ?? 0;
    $shipping = $subtotal > 0 ? 15.00 : 0;
    $total = $subtotal - $discount + $shipping;
@endphp

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <button class="btn-back" data-history-back>Voltar</button>
        <nav class="breadcrumb">
            <a href="{{ route('index') }}">Página Inicial</a>
            <span>&gt;</span>
            <span class="current">Carrinho</span>
        </nav>
    </div>

    <h1 class="section-title" style="text-align: left; margin-top: 0; margin-bottom: var(--space-xl);">Seu carrinho</h1>

    @if(empty($cartItems))
        <div class="cart-layout">
            <section class="cart-items" id="cart-items">
                <!-- Carrinho vazio no backend; o JS client-side irá popular a partir do LocalStorage -->
            </section>

            <aside class="cart-summary">
                <h2>Resumo</h2>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="value" id="subtotal">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span id="discount-label">Desconto</span>
                    <span class="value discount" id="discount">-R$ {{ number_format($discount, 2, ',', '.') }}</span>
                </div>

                <hr>

                <div class="shipping-calculator">
                    <label for="postal-code" class="shipping-label">Calcular Frete</label>
                    <div class="cep-form">
                        <input type="text" id="postal-code" placeholder="Digite seu CEP" maxlength="9">
                        <button type="button" id="validate-cep-btn">Calcular</button>
                    </div>
                    <p class="shipping-message" id="shipping-message"></p>
                </div>

                <div class="summary-row shipping-row">
                    <span>Frete</span>
                    <span class="value" id="shipping-cost">R$ {{ number_format($shipping, 2, ',', '.') }}</span>
                </div>
                <hr>

                <div class="summary-row total-row">
                    <span>Total</span>
                    <span class="value" id="total">R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>

                <div class="coupon-form" style="display: flex; gap: 8px; margin-top: 12px;">
                    <input type="text" id="coupon-code" placeholder="Adicionar cupom" value="{{ $coupon['code'] ?? '' }}" style="flex: 1;">
                    <button type="button" id="apply-coupon-btn">Aplicar</button>
                </div>
                @if(session('error'))
                    <p class="coupon-message error" style="margin-top: 6px; color: #c00;">{{ session('error') }}</p>
                @endif
                @if(session('success'))
                    <p class="coupon-message success" style="margin-top: 6px; color: #2e7d32;">{{ session('success') }}</p>
                @endif

                <button class="btn btn-dark btn-checkout" onclick="window.location.href='{{ route('pagamento') }}'" style="margin-top: 16px;">
                    Finalizar Compra &rarr;
                </button>
            </aside>
        </div>
    @else
        <div class="cart-layout" data-backend-cart="true">
            <section class="cart-items" id="cart-items">
                @foreach($cartItems as $item)
                    <div class="cart-item" data-id="{{ $item['id'] }}">
                        <div class="cart-item-info">
                            <div class="cart-item-img">
                                @php
                                    $img = $item['image'] ?? '';
                                    if (!$img) {
                                        $src = '/img/placeholder.svg';
                                    } elseif (strpos($img, '/') === 0 || strpos($img, 'http') === 0) {
                                        $src = $img;
                                    } elseif (strpos($img, 'img/') === 0) {
                                        $src = '/' . $img;
                                    } else {
                                        $src = '/img/' . $img;
                                    }

                                    // Codifica espaços para evitar 404 em imagens com nome/pasta contendo espaços
                                    $src = str_replace(' ', '%20', $src);
                                @endphp
                                <img src="{{ $src }}" alt="{{ $item['name'] }}" onerror="this.src='/img/placeholder.svg'">
                            </div>
                            <div class="cart-item-details">
                                <h4>{{ $item['name'] }}</h4>
                                <p class="cart-item-price">R$ {{ number_format($item['price'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="cart-item-controls">
                            <div class="quantity-selector">
                                <button class="quantity-btn decrease-qty" onclick="changeQty(this, {{ $item['id'] }}, -1)">-</button>
                                <span class="quantity-value">{{ $item['quantity'] }}</span>
                                <button class="quantity-btn increase-qty" onclick="changeQty(this, {{ $item['id'] }}, 1)">+</button>
                            </div>
                            <button class="delete-item-btn" onclick="removeFromCart({{ $item['id'] }})">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 011 1v6a1 1 0 11-2 0V9a1 1 0 011-1zm4 0a1 1 0 011 1v6a1 1 0 11-2 0V9a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </section>

            <aside class="cart-summary">
                <h2>Resumo</h2>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="value" id="subtotal">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span id="discount-label">Desconto</span>
                    <span class="value discount" id="discount">-R$ {{ number_format($discount, 2, ',', '.') }}</span>
                </div>

                <hr>

                <div class="shipping-calculator">
                    <label for="postal-code" class="shipping-label">Calcular Frete</label>
                    <div class="cep-form">
                        <input type="text" id="postal-code" placeholder="Digite seu CEP" maxlength="9">
                        <button type="button" id="validate-cep-btn">Calcular</button>
                    </div>
                    <p class="shipping-message" id="shipping-message"></p>
                </div>

                <div class="summary-row shipping-row">
                    <span>Frete</span>
                    <span class="value" id="shipping-cost">R$ {{ number_format($shipping, 2, ',', '.') }}</span>
                </div>
                <hr>

                <div class="summary-row total-row">
                    <span>Total</span>
                    <span class="value" id="total">R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>

                <div class="coupon-form" style="display: flex; gap: 8px; margin-top: 12px;">
                    <input type="text" id="coupon-code" placeholder="Adicionar cupom" value="{{ $coupon['code'] ?? '' }}" style="flex: 1;">
                    <button type="button" id="apply-coupon-btn">Aplicar</button>
                </div>
                @if(session('error'))
                    <p class="coupon-message error" style="margin-top: 6px; color: #c00;">{{ session('error') }}</p>
                @endif
                @if(session('success'))
                    <p class="coupon-message success" style="margin-top: 6px; color: #2e7d32;">{{ session('success') }}</p>
                @endif

                <button class="btn btn-dark btn-checkout" onclick="window.location.href='{{ route('checkout') }}'" style="margin-top: 16px;">
                    Finalizar Compra &rarr;
                </button>
            </aside>
        </div>
    @endif
</div>

@include('partials.contact')
@endsection

@section('extra-scripts')
<script>
// Ajusta quantidade e sincroniza com o backend
function changeQty(btn, productId, delta) {
    const valueEl = btn.parentElement.querySelector('.quantity-value');
    let qty = parseInt(valueEl.textContent) || 1;
    qty = Math.max(1, qty + delta);
    updateCartItem(productId, qty);
}
</script>
@endsection

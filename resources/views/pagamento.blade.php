@extends('layouts.app')
@section('title', 'Pagamento - Joalheria')
@section('content')

    @if(!auth()->check())
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('index') }}">Página Inicial</a>
                <span>&gt;</span>
                <span class="current">Pagamento</span>
            </nav>

            <div style="text-align: center; padding: 60px 20px;">
                <h1 class="section-title">Faça login para finalizar</h1>
                <p class="text-secondary" style="font-size: 16px; margin-bottom: 30px;">
                    Você precisa estar logado para concluir o pagamento.
                </p>
                <a href="{{ route('login') }}" class="btn btn-dark">Fazer Login</a>
                <span class="auth-divider">ou</span>
                <a href="{{ route('cadastro') }}" class="btn btn-outline">Criar Conta</a>
            </div>
        </div>
    @else
    <div class="container">
        <button class="btn-back" data-history-back style="margin-bottom: 15px;">Voltar</button>
        <nav class="breadcrumb">
            <a href="{{ route('index') }}">Página Inicial</a>
            <span>&gt;</span>
            <a href="{{ route('produto', ['id' => 1]) }}">Anel de...</a>
             <span>&gt;</span>
            <a href="{{ route('carrinho') }}">Carrinho</a>
             <span>&gt;</span>
            <span class="current">Pagamento</span>
        </nav>

        @php
            $cart = $cart ?? session('cart', []);
            $coupon = $coupon ?? session('coupon');
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
            }
            $discount = $coupon['discount'] ?? 0;
            $shipping = $subtotal > 0 ? 15.00 : 0;
            $total = $subtotal - $discount + $shipping;
        @endphp

        <div class="checkout-layout">

            <section class="checkout-summary">
                <h1 class="checkout-title">Resumo</h1>

                <div class="cart-items">
                    @forelse($cart as $item)
                        <article class="cart-item" data-price="{{ $item['price'] }}">
                            <div class="item-details">
                                @php
                                    $img = $item['image'] ?? '';
                                    if (!$img) {
                                        $src = asset('img/placeholder.svg');
                                    } elseif (str_starts_with($img, 'http') || str_starts_with($img, '/')) {
                                        $src = $img;
                                    } else {
                                        $src = asset('img/' . $img);
                                    }
                                @endphp
                                <img src="{{ $src }}" alt="{{ $item['name'] }}">
                                <div>
                                    <h2>{{ $item['name'] }}</h2>
                                    <p class="price">R$ {{ number_format($item['price'], 2, ',', '.') }}</p>
                                    <p>Qtd: {{ $item['quantity'] }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p style="padding: 20px;">Seu carrinho está vazio.</p>
                    @endforelse
                </div>
            </section>

            <aside class="checkout-payment">
                <a href="{{ route('carrinho') }}" class="back-link">&larr; Voltar </a>
                <h1 class="checkout-title payment">Pagamento</h1>

                <div class="cart-summary payment-page">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal" class="value">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span id="discount-label">Desconto</span>
                        <span id="discount" class="discount">-R$ {{ number_format($discount, 2, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Frete</span>
                        <span id="shipping" class="value">R$ {{ number_format($shipping, 2, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <span id="total">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                </div>

                <h3 class="payment-methods-title" style="margin-top: 20px;">Forma de pagamento</h3>
                <form id="payment-form">
                    <div class="payment-methods">
                        <div class="payment-option">
                            <input type="radio" name="paymentMethod" value="credit_card" id="visa-1234" checked>
                            <label for="visa-1234" class="payment-method">
                                <span>Cartão de Crédito</span>
                                <span>**** **** **** 2109</span>
                            </label>
                        </div>

                        <div class="payment-option">
                            <input type="radio" name="paymentMethod" value="pix" id="pix">
                            <label for="pix" class="payment-method pix-option">
                                <span>PIX</span>
                            </label>
                        </div>

                        <div class="payment-option">
                            <input type="radio" name="paymentMethod" value="boleto" id="boleto">
                            <label for="boleto" class="payment-method">
                                <span>Boleto</span>
                            </label>
                        </div>

                        <button type="button" class="payment-method add-card-btn" id="add-card-btn">
                            <span>+</span>
                            <span>Cadastrar novo cartão</span>
                        </button>

                    </div>

                    <button type="button" class="btn btn-dark" id="continue-btn" style="width: 100%; background-color: var(--color-primary); color: var(--color-dark);">Continuar</button>
                </form>

            </aside>
        </div>
    </div>
    @include('partials.contact')
    @endif
@endsection

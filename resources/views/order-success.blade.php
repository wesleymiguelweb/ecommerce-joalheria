@extends('layouts.app')

@section('title', 'Pedido Confirmado - Elegance Joias')

@section('head')
<link rel="stylesheet" href="{{ asset('css/order-success.css') }}">
@endsection

@section('content')
<div class="container order-success-container">
    <div class="order-success-card">
        <div class="success-icon">
            <i class="fa-solid fa-check"></i>
        </div>

        <h1 class="success-title">Pedido Realizado com Sucesso!</h1>

        @if(isset($orderNumber))
            <p class="order-number">
                Número do Pedido: <strong>#{{ $orderNumber }}</strong>
            </p>
        @endif

        <p class="success-message">
            Obrigado pela sua compra! Você receberá um e-mail de confirmação em breve.<br>
            Acompanhe o status do seu pedido na área de perfil.
        </p>

        @if(isset($orderItems) && count($orderItems) > 0)
            <div class="order-products-section">
                <h3>Produtos Comprados</h3>

                <div class="products-grid">
                    @foreach($orderItems as $item)
                        <div class="product-item">
                            @if($item['image'])
                                <img src="{{ asset('img/' . $item['image']) }}" alt="{{ $item['name'] }}">
                            @endif

                            <div class="product-info">
                                <h4>{{ $item['name'] }}</h4>
                                <p>Quantidade: {{ $item['quantity'] }} • R$ {{ number_format($item['price'], 2, ',', '.') }}</p>
                            </div>

                            <a href="{{ route('produto', $item['id']) }}#reviews" class="btn btn-secondary btn-review">
                                <i class="fa-solid fa-star"></i> Avaliar
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="review-notice">
                    <p>
                        <i class="fa-solid fa-info-circle"></i>
                        <strong>Sua opinião é importante!</strong> Avalie os produtos para ajudar outros clientes.
                    </p>
                </div>
            </div>
        @endif

        <div class="action-buttons">
            <a href="{{ route('profile') }}" class="btn btn-dark">
                <i class="fa-solid fa-user"></i> Ver Meus Pedidos
            </a>

            <a href="{{ route('index') }}" class="btn btn-outline">
                <i class="fa-solid fa-home"></i> Voltar para Home
            </a>
        </div>

        <div class="next-steps-section">
            <h4>Próximos Passos</h4>
            <ul>
                <li>Você receberá um e-mail de confirmação com os detalhes do pedido</li>
                <li>Acompanhe o status de entrega através do seu perfil</li>
                <li>O prazo de entrega é de 7 a 15 dias úteis</li>
                <li>Após receber, não esqueça de avaliar os produtos</li>
            </ul>
        </div>
    </div>
</div>
@endsection

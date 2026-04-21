@extends('layouts.app')

@section('title', 'Termos e Condições - Elegance Joias')

@section('content')
<div class="container" style="padding: 40px 0;">
    <nav class="breadcrumb">
        <a href="{{ route('index') }}">Página Inicial</a>
        <span>&gt;</span>
        <span class="current">Termos e Condições</span>
    </nav>

    <section style="max-width: 800px; margin: 40px auto;">
        <h1>Termos e Condições</h1>

            <h2>1. Aceitação dos Termos</h2>
            <p class="info-text">
                Ao acessar e utilizar o site da Elegance Joias, você concorda com estes termos e condições.
                Se não concordar com alguma parte, favor não utilizar o site.
            </p>

            <h2>2. Produtos e Preços</h2>
            <p class="info-text">
                Os preços dos produtos estão sujeitos a alterações sem aviso prévio. A Elegance Joias se
                reserva o direito de aceitar ou recusar qualquer pedido.
            </p>

            <h2>3. Limitação de Responsabilidade</h2>
            <p class="info-text">
                A Elegance Joias não será responsável por danos diretos, indiretos ou consequentes resultantes
                do uso ou impossibilidade de uso do site ou dos serviços.
            </p>

            <h2>4. Alterações nos Termos</h2>
            <p class="info-text">
                Reservamo-nos o direito de modificar estes termos a qualquer momento. As alterações entrarão
                em vigor imediatamente após a publicação no site.
            </p>
        </section>
    </div>
</div>

@include('partials.contact')
@endsection

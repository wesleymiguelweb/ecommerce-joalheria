@extends('layouts.app')

@section('title', 'Política de Privacidade - Elegance Joias')

@section('content')
<div class="container" style="padding: 40px 0;">
    <nav class="breadcrumb">
        <a href="{{ route('index') }}">Página Inicial</a>
        <span>&gt;</span>
        <span class="current">Política de Privacidade</span>
    </nav>

    <section style="max-width: 800px; margin: 40px auto;">
        <h1>Política de Privacidade</h1>

            <h2>1. Coleta de Dados</h2>
            <p class="info-text">
                A Elegance Joias coleta informações pessoais apenas quando você voluntariamente as fornece,
                como ao realizar uma compra ou se registrar em nossa plataforma.
            </p>

            <h2>2. Uso das Informações</h2>
            <p class="info-text">
                Utilizamos suas informações para processar pedidos, fornecer suporte ao cliente e melhorar nossos serviços.
                Nunca compartilhamos suas informações com terceiros sem consentimento.
            </p>

            <h2>3. Segurança</h2>
            <p class="info-text">
                Implementamos medidas de segurança padrão da indústria para proteger suas informações pessoais
                contra acesso não autorizado.
            </p>

            <h2>4. Contato</h2>
            <p class="info-text">
                Se tiver dúvidas sobre nossa política de privacidade, entre em contato conosco em
                privacidade@elegancejoias.com.br
            </p>
        </section>
    </div>
</div>

@include('partials.contact')
@endsection

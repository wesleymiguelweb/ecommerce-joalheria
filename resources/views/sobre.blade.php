@extends('layouts.app')

@section('title', 'Sobre - Elegance Joias')

@section('content')
<div class="container" style="padding: 40px 0;">
    <nav class="breadcrumb">
        <a href="{{ route('index') }}">Página Inicial</a>
        <span>&gt;</span>
        <span class="current">Sobre</span>
    </nav>

    <section style="max-width: 800px; margin: 40px auto;">
        <h1>Sobre a Elegance Joias</h1>
            <p style="font-size: 16px; line-height: 1.8; color: #666; margin: 20px 0;">
                Bem-vindo à Elegance Joias, sua melhor escolha em joaleria de luxo e elegância. Desde nossa fundação,
                nos comprometemos em oferecer as mais belas peças de joalheria para todos os momentos especiais da sua vida.
            </p>
            <p style="font-size: 16px; line-height: 1.8; color: #666; margin: 20px 0;">
                Com mais de duas décadas de experiência, nossa missão é proporcionar qualidade incomparável, design inovador
                e atendimento excepcional. Cada joia é cuidadosamente selecionada ou confeccionada para garantir a satisfação
                e a felicidade de nossos clientes.
            </p>
            <p style="font-size: 16px; line-height: 1.8; color: #666; margin: 20px 0;">
                Acreditamos que toda pessoa merece usar uma joia que reflita sua personalidade, elegância e estilo de vida.
                Por isso, oferecemos uma ampla variedade de produtos, desde clássicos atemporais até as tendências mais atuais
                em joaleria.
            </p>
        </section>
    </div>
</div>

@include('partials.contact')
@endsection

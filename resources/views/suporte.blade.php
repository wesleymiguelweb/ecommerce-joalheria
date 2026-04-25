@extends('layouts.app')

@section('title', 'Suporte - Elegance Joias')

@section('content')
<div class="container" style="padding: 40px 0;">
    <nav class="breadcrumb">
        <a href="{{ route('index') }}">Página Inicial</a>
        <span>&gt;</span>
        <span class="current">Suporte</span>
    </nav>

    <section style="max-width: 800px; margin: 40px auto;">
        <h1>Central de Suporte</h1>
            <p style="font-size: 16px; line-height: 1.8; color: #666; margin: 20px 0;">
                Precisando de ajuda? Estamos aqui para atender suas dúvidas e resolver seus problemas com rapidez e eficiência.
            </p>

            <h2 style="margin-top: 40px;">Como Podemos Ajudar?</h2>
            <ul style="font-size: 16px; line-height: 2; color: #666;">
                <li>📧 <strong>Email:</strong> wesley.miguel7@outlook.com</li>
                <li>📞 <strong>Telefone:</strong> (11) 998102527</li>
                <li>💬 <strong>Chat:</strong> Disponível de seg. a sex., 9h-18h</li>
            </ul>

            <h2 style="margin-top: 40px;">Perguntas Frequentes</h2>
            <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-top: 20px;">
                <h3 style="color: #333;">Qual é o prazo de entrega?</h3>
                <p style="color: #666;">Geralmente entregamos em 5 a 7 dias úteis para São Paulo e região.</p>
            </div>

            <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-top: 20px;">
                <h3 style="color: #333;">Posso fazer devolução?</h3>
                <p style="color: #666;">Sim! Aceitamos devoluções dentro de 30 dias após a compra.</p>
            </div>
        </section>
    </div>
</div>

@include('partials.contact')
@endsection

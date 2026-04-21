@extends('layouts.app')
@section('title', isset($page_titles[$page]) ? $page_titles[$page] : 'Informações - Joalheria')
@section('content')

    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('index') }}">Página Inicial</a>
            <span>&gt;</span>
            <span class="current">{{ isset($page_titles[$page]) ? $page_titles[$page] : 'Informações' }}</span>
        </nav>

        <div class="info-page">
            @if ($page === 'sobre')
                <h1>Sobre Nós</h1>
                <section class="info-content">
                    <h2>Nossa História</h2>
                    <p>A Joalheria foi fundada com o objetivo de oferecer peças únicas e de qualidade para todos os momentos especiais da sua vida.</p>

                    <h2>Nossa Missão</h2>
                    <p>Proporcionar elegância, qualidade e autenticidade através de joias cuidadosamente selecionadas e produzidas.</p>

                    <h2>Nossos Valores</h2>
                    <ul>
                        <li>Qualidade em primeiro lugar</li>
                        <li>Atendimento ao cliente excepcional</li>
                        <li>Sustentabilidade e ética</li>
                        <li>Inovação contínua</li>
                    </ul>
                </section>

            @elseif ($page === 'termos')
                <h1>Termos e Condições</h1>
                <section class="info-content">
                    <h2>1. Aceitação dos Termos</h2>
                    <p>Ao utilizar este site, você aceita e concorda com todos os termos e condições aqui descritos.</p>

                    <h2>2. Uso do Site</h2>
                    <p>Você concorda em usar este site apenas para fins legais e de maneira que não infrinja direitos de terceiros ou restrinja seu uso e gozo.</p>

                    <h2>3. Propriedade Intelectual</h2>
                    <p>Todos os conteúdos, imagens e textos são propriedade intelectual da Joalheria, protegidos por direitos autorais.</p>

                    <h2>4. Limitação de Responsabilidade</h2>
                    <p>A Joalheria não se responsabiliza por danos indiretos ou consequentes decorrentes do uso do site.</p>
                </section>

            @elseif ($page === 'privacidade')
                <h1>Política de Privacidade</h1>
                <section class="info-content">
                    <h2>1. Coleta de Dados</h2>
                    <p>Coletamos informações pessoais apenas quando você as fornece voluntariamente ao se registrar ou fazer uma compra.</p>

                    <h2>2. Uso de Dados</h2>
                    <p>Seus dados são utilizados apenas para processar pedidos, melhorar nossos serviços e comunicações relacionadas à sua compra.</p>

                    <h2>3. Proteção de Dados</h2>
                    <p>Implementamos medidas de segurança para proteger suas informações contra acesso não autorizado.</p>

                    <h2>4. Cookies</h2>
                    <p>Utilizamos cookies para melhorar sua experiência no site. Você pode desativar cookies nas configurações do seu navegador.</p>

                    <h2>5. Compartilhamento de Dados</h2>
                    <p>Não compartilhamos suas informações com terceiros sem seu consentimento, exceto quando exigido por lei.</p>
                </section>

            @elseif ($page === 'suporte')
                <h1>Suporte ao Cliente</h1>
                <section class="info-content">
                    <h2>Como Entrar em Contato</h2>
                    <p>Estamos aqui para ajudar! Entre em contato conosco de várias maneiras:</p>

                    <h2>Email</h2>
                    <p><strong>suporte@joalheria.com.br</strong></p>
                    <p>Respondemos a todos os emails em até 24 horas.</p>

                    <h2>Telefone</h2>
                    <p><strong>(11) 9999-9999</strong></p>
                    <p>Disponível de segunda a sexta, das 9h às 18h.</p>

                    <h2>Chat ao Vivo</h2>
                    <p>Disponível durante o horário comercial para respostas imediatas.</p>

                    <h2>Dúvidas Frequentes</h2>
                    <ul>
                        <li><strong>Como rastrear meu pedido?</strong> - Você receberá um código de rastreamento por email após o envio.</li>
                        <li><strong>Qual é a política de devoluções?</strong> - Aceitamos devoluções em até 30 dias após a compra.</li>
                        <li><strong>Como faço uma reclamação?</strong> - Entre em contato através do email de suporte com detalhes da reclamação.</li>
                    </ul>
                </section>

            @else
                <h1>Página Não Encontrada</h1>
                <p><a href="{{ route('index') }}">Voltar para Página Inicial</a></p>
            @endif
        </div>
    </div>

@include('partials.contact')
@endsection

@section('extra-styles')
@section('extra-styles')
    <link rel="stylesheet" href="{{ asset('css/atomic/templates/_info-pages.css') }}">
@endsection

@extends('layouts.app')

@section('title', 'Métricas - Estágio')

@section('content')
<div class="container" style="padding: 40px 0;">
    <nav class="breadcrumb">
        <a href="{{ route('index') }}">Página Inicial</a>
        <span>&gt;</span>
        <span class="current">Métricas nos Marketplaces</span>
    </nav>

    <section style="margin: 40px auto; text-align: center;">
        <h1 style="margin-bottom: 20px;">Métricas em Marketing de Performance nos Marketplaces</h1>
        <p style="font-size: 16px; line-height: 1.8; color: #666; margin-bottom: 20px; max-width: 800px; margin-left: auto; margin-right: auto;">
            Abaixo estão os resultados obtidos durante o estágio,são métricas de desempenho em anúncios de destaque orgânico e escalados em diferentes plataformas de marketplace com mídia paga em um curto periodo de tempo.
        </p>

        <div style="background-color: #fff3cd; border-left: 6px solid #ffeeba; padding: 20px; margin: 0 auto 40px auto; max-width: 800px; text-align: left; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <strong style="color: #856404; display: block; margin-bottom: 5px; font-size: 16px;">Aviso Importante:</strong>
            <span style="color: #856404; font-size: 15px;">As métricas apresentadas abaixo são resultados <strong>reais</strong> obtidos através de estratégias aplicadas nos marketplaces, especificamente no <strong>segmento automotivo</strong>. Elas estão sendo exibidas aqui com o propósito de demonstração prática no portfólio.</span>
        </div>

        <!-- Container para as imagens das métricas -->
        <div style="display: flex; flex-direction: column; gap: 40px; align-items: center;">
            
            <!-- Exemplo de como colocar as imagens: substitua o "src" pelo caminho correto das suas imagens -->
            <div style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; max-width: 900px; width: 100%;">
     
                <img src="{{ asset('img/metricas/metricasAB.png') }}" alt="Métricas Mercado Livre" style="width: 100%; display: block; object-fit: cover;">
                <div style="padding: 40px; background-color: #f9f9f9; color: #999;">
                    <i>Estratégias em CRO de Teste A/B </i>
                </div>
            </div>

            <div style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; max-width: 900px; width: 100%;">
               <img src="{{ asset('img/metricas/metricasABM.png') }}" alt="Métricas Mercado Livre" style="width: 100%; display: block; object-fit: cover;">
                <div style="padding: 40px; background-color: #f9f9f9; color: #999;">
                    <i>Variações do mesmo Sku em contas diferentes</i>
                </div>
            </div>

            <div style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; max-width: 900px; width: 100%;">
               <img src="{{ asset('img/metricas/metricasABML.png') }}" alt="Métricas Mercado Livre" style="width: 100%; display: block; object-fit: cover;">
                <div style="padding: 40px; background-color: #f9f9f9; color: #999;">
                    <i>Melhora da taxa de conversão do mesmo produto</i>
                </div>
            </div>

            <div style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; max-width: 900px; width: 100%;">
               <img src="{{ asset('img/metricas/tagMV.png') }}" alt="Métricas Mercado Livre" style="width: 100%; display: block; object-fit: cover;">
                <div style="padding: 40px; background-color: #f9f9f9; color: #999;">
                    <i>Case de Sucesso em Growth</i>
                </div>
            </div>

            <div style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; max-width: 900px; width: 100%;">
               <img src="{{ asset('img/metricas/CoifaManopla.png') }}" alt="Métricas Mercado Livre" style="width: 100%; display: block; object-fit: cover;">
                <div style="padding: 40px; background-color: #f9f9f9; color: #999;">
                    <i>Experiência do Usuário</i>
                </div>
            </div>

            <div style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; max-width: 900px; width: 100%;">
               <img src="{{ asset('img/metricas/Manopla.png') }}" alt="Métricas Mercado Livre" style="width: 100%; display: block; object-fit: cover;">
                <div style="padding: 40px; background-color: #f9f9f9; color: #999;">
                    <i></i>
                </div>
            </div>


            <div style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; max-width: 900px; width: 100%;">
               <img src="{{ asset('img/metricas/VL.png') }}" alt="Métricas Mercado Livre" style="width: 100%; display: block; object-fit: cover;">
                <div style="padding: 40px; background-color: #f9f9f9; color: #999;">
                    <i>Design orientado a metricas de aquisição e melhora no CTR</i>
                </div>
            </div>


        </div>
        

    </section>
</div>

@include('partials.contact')
@endsection

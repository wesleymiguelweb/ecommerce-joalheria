@extends('layouts.app')

@section('title', 'Métricas - Elegance Joias')

@section('content')
<div class="container" style="padding: 40px 0;">
    <nav class="breadcrumb">
        <a href="{{ route('index') }}">Página Inicial</a>
        <span>&gt;</span>
        <span class="current">Métricas de Marketplaces</span>
    </nav>

    <section style="margin: 40px auto; text-align: center;">
        <h1 style="margin-bottom: 20px;">Métricas de Marketplaces</h1>
        <p style="font-size: 16px; line-height: 1.8; color: #666; margin-bottom: 20px; max-width: 800px; margin-left: auto; margin-right: auto;">
            Abaixo estão os resultados e métricas de desempenho das nossas vendas em diferentes plataformas de marketplace.
        </p>

        <div style="background-color: #fff3cd; border-left: 6px solid #ffeeba; padding: 20px; margin: 0 auto 40px auto; max-width: 800px; text-align: left; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <strong style="color: #856404; display: block; margin-bottom: 5px; font-size: 16px;">Aviso Importante:</strong>
            <span style="color: #856404; font-size: 15px;">As métricas apresentadas abaixo são resultados <strong>reais</strong> obtidos através de estratégias aplicadas ao mercado de marketplaces, especificamente no <strong>segmento automotivo</strong>. Elas estão sendo exibidas aqui com o propósito de demonstração no portfólio.</span>
        </div>

        <!-- Container para as imagens das métricas -->
        <div style="display: flex; flex-direction: column; gap: 40px; align-items: center;">
            
            <!-- Exemplo de como colocar as imagens: substitua o "src" pelo caminho correto das suas imagens -->
            <div style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; max-width: 900px; width: 100%;">
                <!-- Remova o comentário e adicione a imagem real -->
                <!-- <img src="{{ asset('img/metrica-mercadolivre.jpg') }}" alt="Métricas Mercado Livre" style="width: 100%; display: block; object-fit: cover;"> -->
                <div style="padding: 40px; background-color: #f9f9f9; color: #999;">
                    <i>Insira sua primeira imagem de métricas aqui (ex: img/metricas1.jpg)</i>
                </div>
            </div>

            <div style="box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; max-width: 900px; width: 100%;">
                <!-- Remova o comentário e adicione a imagem real -->
                <!-- <img src="{{ asset('img/metrica-shopee.jpg') }}" alt="Métricas Shopee" style="width: 100%; display: block; object-fit: cover;"> -->
                <div style="padding: 40px; background-color: #f9f9f9; color: #999;">
                    <i>Insira sua segunda imagem de métricas aqui (ex: img/metricas2.jpg)</i>
                </div>
            </div>

        </div>
        
        <div style="margin-top: 40px;">
            <p style="color: #666;">Para adicionar suas imagens reais: salve-as na pasta <strong>public/img/</strong> e atualize o arquivo <strong>resources/views/metricas.blade.php</strong> para apontar para o nome correto delas.</p>
        </div>

    </section>
</div>

@include('partials.contact')
@endsection

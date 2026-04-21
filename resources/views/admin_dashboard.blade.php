@extends('layouts.admin')

@section('title', 'Painel ADM - Dashboard')

@section('breadcrumb', 'Dashboard')

@section('content')
        <div class="admin-card">
            <h2>Painel Principal</h2>
            <p class="subtitle">Bem-vindo ao administrador da loja. Selecione uma opção para gerenciar sua loja.</p>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total de Produtos</h3>
                    <p>{{ $totalProducts ?? 0 }}</p>
                </div>
                <div class="stat-card">
                    <h3>Usuários Cadastrados</h3>
                    <p>{{ $totalUsers ?? 0 }}</p>
                </div>
                <div class="stat-card alert">
                    <h3>Produtos com Baixo Estoque</h3>
                    <p>{{ $lowStockProducts ?? 0 }}</p>
                </div>
                <div class="stat-card">
                    <h3>Avaliações</h3>
                    <p>{{ $totalReviews ?? 0 }}</p>
                </div>
            </div>

            <div class="dashboard-navigation">

                <a href="{{ route('adm.produto') }}" class="dashboard-link-card">
                    <i class="fas fa-boxes fa-2x"></i> <span>Produtos em Estoque</span>
                    <p>Visualizar e gerenciar produtos existentes.</p>
                </a>

                <a href="{{ route('adm.usuarios') }}" class="dashboard-link-card">
                    <i class="fas fa-users fa-2x"></i> <span>Usuários Cadastrados</span>
                    <p>Verificar e administrar contas de usuários.</p>
                </a>

                <a href="{{ route('adm.produto.criar') }}" class="dashboard-link-card">
                     <i class="fas fa-plus-circle fa-2x"></i> <span>Cadastrar Novo Produto</span>
                    <p>Adicionar novos itens ao catálogo da loja.</p>
                </a>

                <a href="{{ route('adm.coupons') }}" class="dashboard-link-card">
                    <i class="fas fa-ticket-alt fa-2x"></i> <span>Gerenciar Cupons</span>
                    <p>Criar e administrar cupons de desconto.</p>
                </a>

                <a href="{{ route('adm.reviews') }}" class="dashboard-link-card">
                    <i class="fas fa-comments fa-2x"></i> <span>Avaliações</span>
                    <p>Aprovar ou rejeitar comentários de produtos.</p>
                </a>

                <a href="{{ route('adm.orders') }}" class="dashboard-link-card">
                    <i class="fas fa-shopping-cart fa-2x"></i> <span>Pedidos Realizados</span>
                    <p>Gerenciar compras e acompanhar entrega.</p>
                </a>

                </div>
        </div>
@endsection

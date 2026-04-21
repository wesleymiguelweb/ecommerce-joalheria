@extends('layouts.admin')

@section('title', 'Painel ADM - Produtos em Estoque')

@section('breadcrumb', 'Produtos em Estoque')

@section('content')
<div class="admin-card">
            <h2>Em estoque</h2>
            <p class="subtitle">Produtos em Estoque (Total: {{ $products->total() }})</p>

            @if($message = session('success'))
                <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    {{ $message }}
                </div>
            @endif

            @if($message = session('error'))
                <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    {{ $message }}
                </div>
            @endif

            <nav class="admin-tabs">
                <a href="{{ route('adm.produto') }}" class="active">Em estoque</a>
                <a href="{{ route('adm.usuarios') }}">Usuários</a>
                <a href="{{ route('adm.orders') }}">Pedidos</a>
                <a href="{{ route('adm.coupons') }}">Cupons</a>
                <a href="{{ route('adm.reviews') }}">Avaliações</a>
                <a href="{{ route('adm.produto.criar') }}" class="btn btn-dark" style="margin-left: auto; padding: 8px 16px; font-size: 0.95em;">+ Novo Produto</a>
            </nav>

            <div class="admin-action-bar">
                <form method="GET" action="{{ route('adm.produto') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; width: 100%;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar produto..."
                           style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; flex: 1; min-width: 200px;">

                    <select name="category" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">Todas Categorias</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="brand" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">Todas Marcas</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>

                    <label style="display: flex; align-items: center; gap: 5px; padding: 8px;">
                        <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}>
                        <span>Estoque Baixo</span>
                    </label>

                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>

                    @if(request()->hasAny(['search', 'category', 'brand', 'low_stock']))
                        <a href="{{ route('adm.produto') }}" class="btn btn-outline" style="padding: 8px 12px;">
                            <i class="fas fa-times"></i> Limpar
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Marca</th>
                            <th>ID</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $product->category }}">
                                        {{ ucfirst($product->category) }}
                                    </span>
                                </td>
                                <td>{{ $product->brand ?? '-' }}</td>
                                <td>{{ $product->id }}</td>
                                <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                @php($threshold = $product->min_stock ?? 5)
                                <td>
                                    <span class="stock-badge {{ $product->stock <= $threshold ? 'stock-low' : 'stock-ok' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>{{ $product->created_at->format('d.m.Y') }} <span class="time">{{ $product->created_at->format('H:i') }}</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('adm.produto.editar', $product->id) }}" class="btn btn-sm btn-secondary" style="padding: 6px 12px; font-size: 0.875em;">Editar</a>
                                        <form action="{{ route('adm.produto.delete', $product->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" style="padding: 6px 12px; font-size: 0.875em;" onclick="return confirm('Tem certeza?')">Deletar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="no-products">
                                    <p>Nenhum produto encontrado. <a href="{{ route('adm.produto.criar') }}">Criar novo produto</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                {{ $products->links('vendor.pagination.simple') }}
            @endif
        </div>
@endsection

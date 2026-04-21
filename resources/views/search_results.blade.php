@extends('layouts.app')

@section('title', 'Resultados da Pesquisa - Elegance Joias')

@section('content')
<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 10px;">Resultados da Pesquisa</h1>
    @if($searchTerm)
        <p style="color: #666; margin-bottom: 30px;">Mostrando resultados para: <strong>"{{ $searchTerm }}"</strong></p>
    @endif

    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        <!-- Filtros Laterais -->
        <aside style="flex: 0 0 250px; min-width: 250px;">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <h3 style="margin-bottom: 20px;">Filtros</h3>

                <form method="GET" action="{{ route('search') }}">
                    <input type="hidden" name="q" value="{{ $searchTerm }}">

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 10px;">Categoria</label>
                        <select name="category" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Todas</option>
                            <option value="feminino" {{ request('category') === 'feminino' ? 'selected' : '' }}>Feminino</option>
                            <option value="masculino" {{ request('category') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 10px;">Cor</label>
                        <select name="color" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="">Todas</option>
                            <option value="ouro" {{ request('color') === 'ouro' ? 'selected' : '' }}>Ouro</option>
                            <option value="prata" {{ request('color') === 'prata' ? 'selected' : '' }}>Prata</option>
                            <option value="neutro" {{ request('color') === 'neutro' ? 'selected' : '' }}>Neutro</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 10px;">Preço</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="number" name="min_price" placeholder="Mín" value="{{ request('min_price') }}"
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Máx" value="{{ request('max_price') }}"
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark" style="width: 100%; padding: 10px; border-radius: 5px;">
                        Aplicar Filtros
                    </button>

                    @if(request()->hasAny(['category', 'color', 'min_price', 'max_price']))
                        <a href="{{ route('search', ['q' => $searchTerm]) }}" class="btn btn-outline"
                           style="width: 100%; padding: 10px; border-radius: 5px; margin-top: 10px; display: block; text-align: center;">
                            Limpar Filtros
                        </a>
                    @endif
                </form>
            </div>
        </aside>

        <!-- Grade de Produtos -->
        <div style="flex: 1;">
            <p style="margin-bottom: 20px; color: #666;">
                {{ $products->total() }} produto(s) encontrado(s)
            </p>

            <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                @forelse($products as $product)
                    @php
                        $pimg = $product->image ?? '';
                        if (!$pimg) {
                            $pimgSrc = '/img/placeholder.svg';
                        } elseif (strpos($pimg, '/') === 0 || strpos($pimg, 'http') === 0) {
                            $pimgSrc = $pimg;
                        } elseif (strpos($pimg, 'img/') === 0) {
                            $pimgSrc = '/' . $pimg;
                        } else {
                            $pimgSrc = '/img/' . $pimg;
                        }
                    @endphp
                    <a href="{{ route('produto', ['id' => $product->id]) }}" class="product-card" style="text-decoration: none; color: inherit;">
                        <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s;">
                            <img src="{{ $pimgSrc }}"
                                 alt="{{ $product->name }}"
                                 style="width: 100%; height: 250px; object-fit: cover;"
                                 onerror="this.src='/img/placeholder.svg'">
                            <div style="padding: 15px;">
                                <h3 style="font-size: 16px; margin-bottom: 8px;">{{ $product->name }}</h3>
                                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">{{ Str::limit($product->description, 60) }}</p>
                                <p style="font-size: 20px; font-weight: bold; color: #c9a55c;">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </p>
                                @if($product->stock <= 0)
                                    <span style="color: #dc3545; font-size: 14px;">Indisponível</span>
                                @elseif($product->isLowStock())
                                    <span style="color: #ffc107; font-size: 14px;">Últimas unidades</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <p style="font-size: 18px; color: #666;">Nenhum produto encontrado.</p>
                        <a href="{{ route('index') }}" class="btn btn-outline" style="margin-top: 20px; display: inline-block;">
                            Voltar para início
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Paginação -->
            @if($products->hasPages())
                <div style="margin-top: 40px; display: flex; justify-content: center;">
                    {{ $products->links('vendor.pagination.simple') }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.product-card:hover > div {
    transform: translateY(-5px);
}
</style>
@endsection

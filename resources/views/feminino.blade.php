@extends('layouts.app')

@section('title', 'Coleção Feminina - Elegance Joias')

@section('content')

    <div class="listing-layout">
        <aside class="listing-sidebar">
                <button class="btn-back" data-history-back style="margin-bottom: 15px; width: 100%;">Voltar</button>
                <nav class="breadcrumb">
                    <a href="{{ route('index') }}">Página Inicial</a>
                    <span>&gt;</span>
                    <span class="current">Feminino</span>
                </nav>

                <div class="filter-group">
                    <h3 class="filter-title">Tipo de Produto</h3>
                    <div class="filter-options" id="category-filters">
                        <a href="#" class="filter-item" data-category="todos"><span>Todos</span> <span>&gt;</span></a>
                        <a href="#" class="filter-item" data-category="colar"><span>Colar</span> <span>&gt;</span></a>
                        <a href="#" class="filter-item" data-category="brinco"><span>Brinco</span> <span>&gt;</span></a>
                        <a href="#" class="filter-item" data-category="bracelete"><span>Bracelete</span> <span>&gt;</span></a>
                        <a href="#" class="filter-item" data-category="aliança"><span>Aliança</span> <span>&gt;</span></a>
                        <a href="#" class="filter-item" data-category="corrente"><span>Corrente</span> <span>&gt;</span></a>
                        <a href="#" class="filter-item" data-category="pulseira"><span>Pulseira</span> <span>&gt;</span></a>
                    </div>
                </div>

               <div class="filter-group">
                    <h3 class="filter-title">Preço</h3>
                    <input type="range" class="price-slider" id="price-slider-input" min="0" max="10000" value="10000" step="100">
                    <div class="price-range">
                        <span>R$0</span>
                        <span id="price-slider-value">R$10000</span>
                    </div>
                </div>

                <div class="filter-group">
                    <h3 class="filter-title">Marca</h3>
                    <div class="filter-options" id="brand-filters">
                        <button class="filter-item" data-brand="todos"><span>Todas</span> <span>&gt;</span></button>
                        <button class="filter-item" data-brand="GUCCI"><span>GUCCI</span> <span>&gt;</span></button>
                        <button class="filter-item" data-brand="PRADA"><span>PRADA</span> <span>&gt;</span></button>
                        <button class="filter-item" data-brand="VERSACE"><span>VERSACE</span> <span>&gt;</span></button>
                        <button class="filter-item" data-brand="ZARA"><span>ZARA</span> <span>&gt;</span></button>
                        <button class="filter-item" data-brand="CALVIN KLEIN"><span>CALVIN KLEIN</span> <span>&gt;</span></button>
                    </div>
                </div>

                <button class="btn btn-dark" id="apply-filters" style="width: 100%;">Aplicar Filtros</button>
            </aside>

            <section class="listing-products">
                <div class="listing-header">
                    <h1>Feminino
                        @if($selectedBrand ?? false)
                            <span class="filter-info">- {{ $selectedBrand }}</span>
                            <a href="{{ route('feminino') }}" class="filter-clear">Limpar filtro</a>
                        @endif
                    </h1>
                    <div class="sort-by">
                        <span id="filter-counter">Carregando produtos...</span>
                        <select id="sort-select" class="sort-select">
                            <option value="popular">Mais popular</option>
                            <option value="newest">Mais recentes</option>
                            <option value="price-asc">Menor preço</option>
                            <option value="price-desc">Maior preço</option>
                            <option value="name-asc">A até Z</option>
                            <option value="name-desc">Z até A</option>
                        </select>
                    </div>
                </div>

                <div class="product-grid listing" id="product-listing">
                    @forelse($products as $product)
                        @php
                            $nameLower = mb_strtolower($product->name);
                            $type = 'outros';
                            if (str_contains($nameLower, 'anel')) { $type = 'anel'; }
                            elseif (str_contains($nameLower, 'colar')) { $type = 'colar'; }
                            elseif (str_contains($nameLower, 'brinco')) { $type = 'brinco'; }
                            elseif (str_contains($nameLower, 'pulseira')) { $type = 'pulseira'; }
                        @endphp
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
                        <div class="product-card" data-productid="{{ $product->id }}" data-price="{{ $product->price }}" data-color="{{ $product->color ?? 'neutro' }}" data-type="{{ $type }}" data-brand="{{ $product->brand }}">
                            <a href="{{ route('produto', ['id' => $product->id]) }}" class="product-card-link">
                                <img src="{{ $pimgSrc }}"
                                     alt="{{ $product->name }}"
                                     onerror="this.src='/img/placeholder.svg'">
                                <h3>{{ $product->name }}</h3>
                                <p class="price">
                                    <span class="sale">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                </p>
                                <p class="stock">
                                    @if($product->stock > 0)
                                        Estoque: {{ $product->stock }}
                                    @else
                                        <span style="color: #dc3545;">Indisponível</span>
                                    @endif
                                </p>
                            </a>
                            @if($product->stock > 0)
                            <button class="btn btn-dark add-to-cart-btn-listing"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-product-price="{{ $product->price }}"
                                    data-product-img="{{ $pimgSrc }}">
                                Adicionar ao Carrinho
                            </button>
                            @endif
                        </div>
                    @empty
                        <p style="grid-column: 1 / -1; text-align: center;">Nenhum produto disponível nesta categoria.</p>
                    @endforelse
                </div>

                <div class="pagination-container" id="pagination-container" style="display: none;">
                    <div class="pagination">
                        <button class="pagination-btn" id="prev-page" disabled>Anterior</button>
                        <div class="pagination-info">
                            <span id="current-page">1</span> de <span id="total-pages">1</span>
                        </div>
                        <button class="pagination-btn" id="next-page">Próximo</button>
                    </div>
                    <p class="pagination-text"><span id="results-count">0</span> resultados encontrados</p>
                </div>
            </section>
        </div>
    </main>

@include('partials.contact')
@endsection

@section('extra-scripts')
<script src="{{ asset('js/filters-api.js') }}"></script>
@endsection

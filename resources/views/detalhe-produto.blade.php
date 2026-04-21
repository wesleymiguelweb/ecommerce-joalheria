@extends('layouts.app')

@section('title', 'Detalhes do Produto - Elegance Joias')

@section('content')

    <div class="container">
        <button class="btn-back" data-history-back style="margin-bottom: 15px;">Voltar</button>
        <nav class="breadcrumb">
            <a href="{{ route('index') }}">Página Inicial</a>
            <span>&gt;</span>
            <a href="{{ route('feminino') }}">Feminino</a>
            <span>&gt;</span>
            <span class="current">{{ $product->name }}</span>
        </nav>

        <section class="product-details-layout">
            <div class="product-gallery">
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
                <div class="main-image">
                    <img src="{{ $pimgSrc }}"
                         alt="{{ $product->name }}"
                         onerror="this.src='/img/placeholder.svg'">
                </div>
                <div class="thumbnail-images">
                    <img src="{{ $pimgSrc }}"
                         alt="{{ $product->name }}"
                         onerror="this.src='/img/placeholder.svg'">
                </div>
            </div>
            <div class="product-info" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}" data-product-image="{{ $pimgSrc }}">
                <h1>{{ $product->name }}</h1>
                <div class="rating-price">
                    <div class="rating">
                        <span>4.5/5</span>
                    </div>
                    <p class="info-price">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                </div>
                <div class="stock-info">
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <p class="stock-details">
                            <strong>Estoque:</strong> <span class="stock-value">{{ $product->stock }} unidade(s)</span>
                        </p>
                    @else
                        <p class="stock-details">
                            <strong>Status:</strong> <span class="availability-status">
                                @if($product->stock > 0)
                                    <span class="text-success">✓ Disponível</span>
                                @else
                                    <span class="text-danger">✗ Indisponível</span>
                                @endif
                            </span>
                        </p>
                    @endif
                </div>
                <p class="description">
                    {{ $product->description ?? 'Descrição não disponível para este produto.' }}
                </p>
                <div class="controls">
                    <div class="quantity-selector">
                        <button class="qty-btn qty-minus" type="button">-</button>
                        <span class="quantity-value">1</span>
                        <button class="qty-btn qty-plus" type="button">+</button>
                    </div>
                    @if($product->stock > 0)
                        <button class="btn btn-dark add-to-cart-btn"
                                type="button"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-price="{{ $product->price }}"
                                data-product-img="{{ asset('img/' . $product->image) }}">
                            <i class="fas fa-shopping-cart"></i> Adicionar ao carrinho
                        </button>
                    @else
                        <button class="btn btn-dark"
                                type="button"
                                disabled
                                style="opacity: 0.6; cursor: not-allowed;">
                            Indisponível
                        </button>
                    @endif
                </div>
            </div>
        </section>

        <section class="product-tabs">
            <nav class="tabs-nav">
                <span class="tab-link active" data-tab="detalhes">Detalhes</span>
                <span class="tab-link" data-tab="comentarios">Avaliações ({{ $product->approvedReviews->count() }})</span>
                <span class="tab-link" data-tab="faqs">FAQs</span>
            </nav>
            <div class="tabs-content">
                <div class="tab-pane active" id="detalhes">
                    <h3 style="margin-bottom: 15px;">Especificações do Produto</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div>
                            <strong>Categoria:</strong> {{ ucfirst($product->category) }}
                        </div>
                        @if($product->brand)
                        <div>
                            <strong>Marca:</strong> {{ $product->brand }}
                        </div>
                        @endif
                        @if($product->color)
                        <div>
                            <strong>Cor:</strong> {{ ucfirst($product->color) }}
                        </div>
                        @endif
                        <div>
                            <strong>Código:</strong> #{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <h4>Descrição</h4>
                        <p>{{ $product->text ?? $product->description ?? 'Produto de alta qualidade da nossa coleção exclusiva.' }}</p>
                    </div>
                </div>
                <div class="tab-pane" id="comentarios">
                    <div class="comments-header">
                        <h3>Avaliações dos Clientes</h3>
                        @auth
                            <button class="btn btn-dark" id="open-comment-modal" style="border-radius: 5px; padding: 10px 20px;">
                                Deixar uma avaliação
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-dark" style="border-radius: 5px; padding: 10px 20px; text-decoration: none;">
                                Fazer login para avaliar
                            </a>
                        @endauth
                    </div>

                    @if($product->approvedReviews->count() > 0)
                        <div class="comments-grid" id="reviews-container">
                            @foreach($product->approvedReviews as $review)
                                <div class="comment-card">
                                    <div class="user-info">
                                        <div class="user">
                                            <span>{{ $review->user->name }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 18px; height: 18px; color: #4caf50;">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="{{ $i <= $review->rating ? '#fbbf24' : '#e5e7eb' }}" style="width: 16px; height: 16px; display: inline-block;">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="comment-body">"{{ $review->comment }}"</p>
                                    <p class="comment-date">Postado em {{ $review->created_at->format('d/m/Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px; color: #666;">
                            <p>Nenhuma avaliação ainda. Seja o primeiro a avaliar!</p>
                        </div>
                    @endif
                </div>
                <div class="tab-pane" id="faqs">
                    <h3 style="margin-bottom: 20px;">Perguntas Frequentes</h3>
                    <div class="faq-list">
                        <div class="faq-item" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                            <h4 style="margin-bottom: 10px; color: #333;">Como funciona o frete?</h4>
                            <p style="color: #666;">O frete é calculado automaticamente no carrinho com base no seu CEP. Oferecemos opções de entrega padrão e expressa.</p>
                        </div>
                        <div class="faq-item" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                            <h4 style="margin-bottom: 10px; color: #333;">Qual o prazo de entrega?</h4>
                            <p style="color: #666;">O prazo varia de acordo com sua localização, geralmente entre 5 a 15 dias úteis para entrega padrão.</p>
                        </div>
                        <div class="faq-item" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                            <h4 style="margin-bottom: 10px; color: #333;">Posso trocar ou devolver?</h4>
                            <p style="color: #666;">Sim! Você tem até 7 dias após o recebimento para solicitar troca ou devolução, desde que o produto esteja em perfeito estado.</p>
                        </div>
                        <div class="faq-item" style="margin-bottom: 20px;">
                            <h4 style="margin-bottom: 10px; color: #333;">O produto tem garantia?</h4>
                            <p style="color: #666;">Todos os nossos produtos têm garantia de 90 dias contra defeitos de fabricação.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- Modal de Comentário -->
    <div id="comment-modal-overlay" class="modal-overlay" style="display: none;">
        <div class="modal-container" style="max-width: 600px; background: white; border-radius: 12px; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0; font-size: 1.5rem;">Deixar uma avaliação</h2>
                <button id="close-comment-modal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form id="comment-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Sua avaliação *</label>
                    <div class="rating-input" style="display: flex; gap: 0.5rem; font-size: 24px;">
                        <span class="star-rating" data-value="1" style="cursor: pointer; color: #ddd;">⭐</span>
                        <span class="star-rating" data-value="2" style="cursor: pointer; color: #ddd;">⭐</span>
                        <span class="star-rating" data-value="3" style="cursor: pointer; color: #ddd;">⭐</span>
                        <span class="star-rating" data-value="4" style="cursor: pointer; color: #ddd;">⭐</span>
                        <span class="star-rating" data-value="5" style="cursor: pointer; color: #ddd;">⭐</span>
                    </div>
                    <input type="hidden" name="rating" id="rating-value" required>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="comment-text" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Seu comentário *</label>
                    <textarea name="comment" id="comment-text" required rows="4"
                              placeholder="Conte-nos o que você achou do produto (mínimo 10 caracteres)..."
                              style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    <small style="color: #666;">Mínimo 10 caracteres, máximo 1000 caracteres</small>
                </div>

                <div id="review-message" style="margin-bottom: 1rem; padding: 10px; border-radius: 4px; display: none;"></div>

                <button type="submit" class="btn btn-dark" id="submit-review-btn" style="width: 100%;">
                    Enviar Avaliação
                </button>
            </form>
        </div>
    </div>

    <script>
    // Sistema de Tabs
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabLinks.forEach(link => {
            link.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');

                // Remove active de todas as tabs
                tabLinks.forEach(l => l.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));

                // Adiciona active na tab clicada
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });

        // Modal de Avaliação
        const modal = document.getElementById('comment-modal-overlay');
        const openBtn = document.getElementById('open-comment-modal');
        const closeBtn = document.getElementById('close-comment-modal');
        const form = document.getElementById('comment-form');
        const stars = document.querySelectorAll('.star-rating');
        const ratingInput = document.getElementById('rating-value');
        let selectedRating = 0;

        // Abrir modal
        if (openBtn) {
            openBtn.addEventListener('click', function() {
                modal.style.display = 'flex';
            });
        }

        // Fechar modal
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }

        // Fechar ao clicar fora
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Sistema de estrelas
        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-value'));
                ratingInput.value = selectedRating;

                // Atualizar visual das estrelas
                stars.forEach((s, index) => {
                    if (index < selectedRating) {
                        s.style.color = '#fbbf24';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });

            // Hover effect
            star.addEventListener('mouseenter', function() {
                const hoverValue = parseInt(this.getAttribute('data-value'));
                stars.forEach((s, index) => {
                    if (index < hoverValue) {
                        s.style.color = '#fbbf24';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });

        // Restaurar estrelas selecionadas ao sair do hover
        document.querySelector('.rating-input').addEventListener('mouseleave', function() {
            stars.forEach((s, index) => {
                if (index < selectedRating) {
                    s.style.color = '#fbbf24';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });

        // Submit da avaliação
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const messageDiv = document.getElementById('review-message');
            const submitBtn = document.getElementById('submit-review-btn');

            // Validação
            if (!ratingInput.value) {
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#fee';
                messageDiv.style.color = '#c00';
                messageDiv.textContent = 'Por favor, selecione uma avaliação com estrelas.';
                return;
            }

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            try {
                const response = await fetch('{{ route("reviews.store", $product->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include',
                    body: JSON.stringify(data)
                });
                const responseText = await response.text();
                let result = null;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    // Mantém null se não for JSON
                }

                if (response.status === 401) {
                    messageDiv.style.display = 'block';
                    messageDiv.style.backgroundColor = '#fee';
                    messageDiv.style.color = '#c00';
                    messageDiv.textContent = 'Faça login para enviar sua avaliação.';
                    return;
                }

                if (!response.ok) {
                    const validationMessage = result?.errors
                        ? Object.values(result.errors).flat().join(' ')
                        : (result?.message || 'Erro ao enviar avaliação.');

                    messageDiv.style.display = 'block';
                    messageDiv.style.backgroundColor = '#fee';
                    messageDiv.style.color = '#c00';
                    messageDiv.textContent = validationMessage;
                    return;
                }

                if (result?.success) {
                    messageDiv.style.display = 'block';
                    messageDiv.style.backgroundColor = '#d4edda';
                    messageDiv.style.color = '#155724';
                    messageDiv.textContent = result.message || 'Avaliação enviada com sucesso! Aguarde aprovação.';

                    // Limpar formulário
                    form.reset();
                    selectedRating = 0;
                    stars.forEach(s => s.style.color = '#ddd');

                    // Fechar modal após 2 segundos
                    setTimeout(() => {
                        modal.style.display = 'none';
                        messageDiv.style.display = 'none';
                    }, 2000);
                } else {
                    messageDiv.style.display = 'block';
                    messageDiv.style.backgroundColor = '#fee';
                    messageDiv.style.color = '#c00';
                    messageDiv.textContent = (result && result.message) ? result.message : 'Erro ao enviar avaliação.';
                }
            } catch (error) {
                console.error('Erro:', error);
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#fee';
                messageDiv.style.color = '#c00';
                messageDiv.textContent = 'Erro ao enviar avaliação. Tente novamente.';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Avaliação';
            }
        });
    });
    </script>

    <style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .tab-pane {
        display: none;
        padding: 20px;
        animation: fadeIn 0.3s ease-in;
    }

    .tab-pane.active {
        display: block;
    }

    .tab-link {
        cursor: pointer;
        padding: 10px 20px;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .tab-link.active {
        border-bottom-color: #c9a55c;
        font-weight: 600;
    }

    .tab-link:hover {
        color: #c9a55c;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .comments-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .comment-card {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .comment-card .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .comment-card .user {
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
    }

    .comment-card .rating {
        margin-bottom: 10px;
    }

    .comment-body {
        margin: 10px 0;
        line-height: 1.6;
    }

    .comment-date {
        font-size: 0.9em;
        color: #666;
    }
    </style>

@include('partials.contact')
@endsection

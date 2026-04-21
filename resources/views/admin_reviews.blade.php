@extends('layouts.admin')

@section('title', 'Painel ADM - Avaliações de Produtos')

@section('breadcrumb', 'Avaliações')

@section('content')
<div class="admin-card">
    <h2>Avaliações de Produtos</h2>
    @php
        // Garantir que contagens estejam definidas mesmo se a consulta falhar
        $total_all = $total_all ?? ($reviews->total() ?? 0);
        $total_pending = $total_pending ?? ($reviews->where('approved', false)->count() ?? 0);
        $total_approved = $total_approved ?? ($reviews->where('approved', true)->count() ?? 0);

        $countLabel = 'totais';
        $total_count = $total_all;
        if (isset($status) && $status === 'pending') {
            $countLabel = 'pendentes';
            $total_count = $total_pending;
        } elseif (isset($status) && $status === 'approved') {
            $countLabel = 'aprovadas';
            $total_count = $total_approved;
        }
    @endphp
    <p class="subtitle">Total de avaliações {{ $countLabel }}: {{ $total_count }}</p>

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

    <div class="admin-action-bar" style="display:flex; gap:10px; align-items:center; flex-wrap: wrap;">
        <form method="GET" action="{{ route('adm.reviews') }}" style="display:flex; gap:10px; align-items:center; flex-wrap: wrap; width:100%;">
            <select name="status" style="padding:8px 12px; border:1px solid #ddd; border-radius:4px;">
                <option value="pending" {{ request('status','all')==='pending' ? 'selected' : '' }}>Pendentes</option>
                <option value="approved" {{ request('status','all')==='approved' ? 'selected' : '' }}>Aprovadas</option>
                <option value="all" {{ request('status','all')==='all' ? 'selected' : '' }}>Todas</option>
            </select>

            <select name="rating" style="padding:8px 12px; border:1px solid #ddd; border-radius:4px;">
                <option value="">Todas as notas</option>
                @for($i=5;$i>=1;$i--)
                    <option value="{{ $i }}" {{ (string)request('rating')===(string)$i ? 'selected' : '' }}>{{ str_repeat('⭐', $i) }}</option>
                @endfor
            </select>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Produto ou usuário..." style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 260px;" />

            <button type="submit" class="btn btn-dark">Filtrar</button>
            @if(request()->hasAny(['status','rating','search']) && (request('status')||request('rating')||request('search')))
                <a href="{{ route('adm.reviews') }}" class="btn btn-secondary">Limpar</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Usuário</th>
                    <th>Avaliação</th>
                    <th>Comentário</th>
                    <th>Data</th>

                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr data-rating="{{ $review->rating }}" data-product="{{ strtolower($review->product->name ?? '') }}" data-user="{{ strtolower($review->user->name ?? '') }}">
                        <td>
                            <a href="{{ route('produto', $review->product_id) }}" target="_blank" style="color: #333; text-decoration: none;">
                                {{ Str::limit($review->product->name ?? 'N/A', 30) }}
                            </a>
                        </td>
                        <td>{{ $review->user->name ?? 'Usuário deletado' }}</td>
                        <td>
                            <div style="display: flex; gap: 2px;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="#fbbf24">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="#d1d5db">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                        </td>
                        <td style="max-width: 300px;">
                            <div style="max-height: 60px; overflow: auto; word-break: break-word;">
                                {{ $review->comment }}
                            </div>
                        </td>
                        <td style="font-size: 0.85em;">{{ $review->created_at->format('d/m/Y H:i') }}</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="no-products">
                            <p>Nenhuma avaliação encontrada.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($reviews, 'hasPages') && $reviews->hasPages())
        <div style="margin-top: 20px;">
            {{ $reviews->links('vendor.pagination.simple') }}
        </div>
    @endif
</div>

<script>
function filterByRating(rating) {
    const rows = document.querySelectorAll('tbody tr[data-rating]');
    rows.forEach(row => {
        if (rating === 'all') {
            row.style.display = '';
        } else {
            row.style.display = row.dataset.rating == rating ? '' : 'none';
        }
    });
}

// Filtro por texto (produto e usuário)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-filter');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr[data-product]');

            rows.forEach(row => {
                const product = row.dataset.product;
                const user = row.dataset.user;

                if (product.includes(searchTerm) || user.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>

@endsection

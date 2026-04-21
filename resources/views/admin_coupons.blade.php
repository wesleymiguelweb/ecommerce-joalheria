@extends('layouts.admin')

@section('title', 'Painel ADM - Cupons de Desconto')

@section('breadcrumb', 'Cupons')

@section('content')
<div class="admin-card">
    <h2>Cupons de Desconto</h2>
    <p class="subtitle">Total de cupons: {{ $coupons->total() }}</p>

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
        <a href="{{ route('adm.produto') }}">Em estoque</a>
        <a href="{{ route('adm.usuarios') }}">Usuários</a>
        <a href="{{ route('adm.orders') }}">Pedidos</a>
        <a href="{{ route('adm.coupons') }}" class="active">Cupons</a>
        <a href="{{ route('adm.reviews') }}">Avaliações</a>
        <a href="{{ route('adm.coupons.create') }}" class="btn btn-dark" style="margin-left: auto; padding: 8px 16px; font-size: 0.95em; margin-bottom: 12px;">+ Novo Cupom</a>
    </nav>

    <div class="admin-action-bar" style="margin-bottom: 20px;">
        <form method="GET" action="{{ route('adm.coupons') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; width: 100%;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por código..."
                   style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; flex: 1; min-width: 200px;">

            <select name="type" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Todos os Tipos</option>
                <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Porcentagem</option>
                <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Valor Fixo</option>
            </select>

            <select name="active" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Todos os Status</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Ativos</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inativos</option>
            </select>

            <select name="validity" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Todas Validades</option>
                <option value="valid" {{ request('validity') == 'valid' ? 'selected' : '' }}>Válidos</option>
                <option value="expired" {{ request('validity') == 'expired' ? 'selected' : '' }}>Expirados</option>
            </select>

            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-filter"></i> Filtrar
            </button>

            @if(request()->hasAny(['search', 'type', 'active', 'validity']))
                <a href="{{ route('adm.coupons') }}" class="btn btn-outline" style="padding: 8px 12px;">
                    <i class="fas fa-times"></i> Limpar
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Compra Mínima</th>
                    <th>Uso</th>
                    <th>Validade</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td><strong>{{ $coupon->code }}</strong></td>
                        <td>
                            @if($coupon->type === 'percentage')
                                <span class="badge" style="background-color: #4CAF50; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85em;">Percentual</span>
                            @else
                                <span class="badge" style="background-color: #2196F3; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85em;">Fixo</span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->type === 'percentage')
                                {{ $coupon->value }}%
                            @else
                                R$ {{ number_format($coupon->value, 2, ',', '.') }}
                            @endif
                        </td>
                        <td>
                            @if($coupon->min_purchase)
                                R$ {{ number_format($coupon->min_purchase, 2, ',', '.') }}
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                        <td>
                            {{ $coupon->usage_count ?? 0 }}
                            @if($coupon->usage_limit)
                                / {{ $coupon->usage_limit }}
                            @endif
                        </td>
                        <td style="font-size: 0.85em;">
                            @if($coupon->valid_from)
                                {{ \Carbon\Carbon::parse($coupon->valid_from)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                            até
                            @if($coupon->valid_until)
                                {{ \Carbon\Carbon::parse($coupon->valid_until)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($coupon->active)
                                <span class="badge" style="background-color: #4CAF50; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.85em;">Ativo</span>
                            @else
                                <span class="badge" style="background-color: #999; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.85em;">Inativo</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('adm.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-secondary" style="padding: 6px 12px; font-size: 0.875em;">Editar</a>
                                <form action="{{ route('adm.coupons.destroy', $coupon->id) }}" method="POST" class="inline-form">
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
                            <p>Nenhum cupom cadastrado. <a href="{{ route('adm.coupons.create') }}">Criar novo cupom</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($coupons->hasPages())
        <div style="margin-top: 20px;">
            {{ $coupons->links('vendor.pagination.simple') }}
        </div>
    @endif
</div>
@endsection

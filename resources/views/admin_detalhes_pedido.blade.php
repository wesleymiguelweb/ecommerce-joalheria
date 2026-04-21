@extends('layouts.admin')

@section('title', 'Painel ADM - Detalhes do Pedido')

@section('breadcrumb', 'Detalhes do Pedido')

@section('content')
<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Pedido #{{ $order->order_number }}</h2>
            <p class="subtitle">Realizado em {{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
        </div>
        <a href="{{ route('adm.orders') }}" class="btn btn-secondary">← Voltar</a>
    </div>

    <!-- Dados do Cliente -->
    <div style="background-color: #f5f5f5; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 15px 0;">Informações do Cliente</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <strong>Nome:</strong>
                <p style="margin: 5px 0 0 0;">{{ $order->user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <strong>Email:</strong>
                <p style="margin: 5px 0 0 0;"><a href="mailto:{{ $order->user->email ?? '' }}">{{ $order->user->email ?? 'N/A' }}</a></p>
            </div>
            <div>
                <strong>Método de Pagamento:</strong>
                <p style="margin: 5px 0 0 0;">{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'Não informado')) }}</p>
            </div>
        </div>
    </div>

    <!-- Endereço de Entrega -->
    <div style="background-color: #f5f5f5; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 15px 0;">Endereço de Entrega</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <strong>Endereço:</strong>
                <p style="margin: 5px 0 0 0;">{{ $order->shipping_address ?? 'Não informado' }}</p>
            </div>
            <div>
                <strong>CEP:</strong>
                <p style="margin: 5px 0 0 0;">{{ $order->postal_code ?? 'Não informado' }}</p>
            </div>
            <div>
                <strong>Observações:</strong>
                <p style="margin: 5px 0 0 0;">{{ $order->customer_notes ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Itens do Pedido -->
    <div style="background-color: #f5f5f5; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 15px 0;">Itens do Pedido</h3>
        <div class="table-responsive">
            <table class="admin-table" style="background-color: white;">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr>
                            <td>
                                <a href="{{ route('produto', $item->product_id) }}" target="_blank" style="color: #333; text-decoration: none;">
                                    {{ $item->product->name ?? 'Produto removido' }}
                                </a>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                            <td><strong>R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="no-products">Nenhum item encontrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumo Financeiro -->
    <div style="background-color: #f5f5f5; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 15px 0;">Resumo Financeiro</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div style="border-right: 1px solid #ddd; padding-right: 15px;">
                <p style="margin: 0 0 5px 0; color: #666;">Subtotal</p>
                <p style="margin: 0; font-size: 1.2em; font-weight: bold;">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</p>
            </div>
            @if($order->discount_amount > 0)
                <div style="border-right: 1px solid #ddd; padding-right: 15px;">
                    <p style="margin: 0 0 5px 0; color: #4CAF50;">Desconto {{ $order->coupon_code ? '(' . $order->coupon_code . ')' : '' }}</p>
                    <p style="margin: 0; font-size: 1.2em; font-weight: bold; color: #4CAF50;">-R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</p>
                </div>
            @endif
            <div style="border-right: 1px solid #ddd; padding-right: 15px;">
                <p style="margin: 0 0 5px 0; color: #666;">Frete</p>
                <p style="margin: 0; font-size: 1.2em; font-weight: bold;">R$ {{ number_format($order->shipping, 2, ',', '.') }}</p>
            </div>
            <div>
                <p style="margin: 0 0 5px 0; color: #666;">Total</p>
                <p style="margin: 0; font-size: 1.2em; font-weight: bold; color: #2196F3;">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Status do Pedido -->
    <div style="background-color: #f5f5f5; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 15px 0;">Status do Pedido</h3>
        @php
            $statusLabels = [
                'pending' => ['text' => 'Pendente', 'color' => '#ff9800', 'description' => 'Aguardando processamento'],
                'processing' => ['text' => 'Em Processamento', 'color' => '#2196F3', 'description' => 'Sendo preparado para envio'],
                'completed' => ['text' => 'Entregue', 'color' => '#4CAF50', 'description' => 'Pedido entregue com sucesso'],
                'cancelled' => ['text' => 'Cancelado', 'color' => '#f44336', 'description' => 'Pedido foi cancelado'],
            ];
            $statusInfo = $statusLabels[$order->status] ?? ['text' => ucfirst($order->status), 'color' => '#666', 'description' => 'Status desconhecido'];
        @endphp
        <div style="padding: 10px; background-color: white; border-left: 4px solid {{ $statusInfo['color'] }}; border-radius: 4px;">
            <span class="badge" style="background-color: {{ $statusInfo['color'] }}; color: white; padding: 6px 12px; border-radius: 12px; font-size: 0.9em;">
                {{ $statusInfo['text'] }}
            </span>
            <p style="margin: 10px 0 0 0; color: #666;">{{ $statusInfo['description'] }}</p>
        </div>
    </div>

</div>

@endsection

@extends('layouts.app')

@section('title', 'Detalhes do Pedido')

@section('content')
<div class="container" style="max-width: 960px; padding: 30px 20px;">
    <a href="{{ route('orders.index') }}" class="btn btn-secondary" style="margin-bottom: 16px;">← Voltar</a>

    <div style="background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
                <h1 style="margin: 0;">Pedido #{{ $order->order_number }}</h1>
                <p style="color: #666; margin: 4px 0 0 0;">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @php
                $statusLabels = [
                    'pending' => ['Pendente', '#ff9800'],
                    'processing' => ['Em Processamento', '#2196F3'],
                    'completed' => ['Concluído', '#4CAF50'],
                    'cancelled' => ['Cancelado', '#f44336'],
                ];
                $info = $statusLabels[$order->status] ?? [$order->status, '#666'];
            @endphp
            <span style="padding: 8px 12px; border-radius: 12px; color: #fff; background: {{ $info[1] }}; font-weight: 600;">{{ $info[0] }}</span>
        </div>

        <hr style="margin: 16px 0; border-color: #eee;">

        <h3 style="margin-bottom: 12px;">Itens do Pedido</h3>
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid #eee;">
                        <th style="padding: 10px 8px;">Produto</th>
                        <th style="padding: 10px 8px;">Qtd</th>
                        <th style="padding: 10px 8px;">Preço</th>
                        <th style="padding: 10px 8px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td style="padding: 10px 8px;">
                                {{ $item->product->name ?? 'Produto removido' }}
                            </td>
                            <td style="padding: 10px 8px;">{{ $item->quantity }}</td>
                            <td style="padding: 10px 8px;">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                            <td style="padding: 10px 8px; font-weight: 600;">R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; margin-top: 16px;">
            <div style="background: #f9f9f9; border: 1px solid #eee; border-radius: 8px; padding: 12px;">
                <h4 style="margin: 0 0 8px 0;">Endereço de Entrega</h4>
                <p style="margin: 0; color: #444;">{{ $order->shipping_address }}</p>
                <p style="margin: 6px 0 0 0; color: #666;">CEP: {{ $order->postal_code }}</p>
            </div>
            <div style="background: #f9f9f9; border: 1px solid #eee; border-radius: 8px; padding: 12px;">
                <h4 style="margin: 0 0 8px 0;">Pagamento</h4>
                <p style="margin: 0; color: #444;">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                @if($order->coupon_code)
                    <p style="margin: 6px 0 0 0; color: #666;">Cupom: <strong>{{ $order->coupon_code }}</strong></p>
                @endif
            </div>
            <div style="background: #f9f9f9; border: 1px solid #eee; border-radius: 8px; padding: 12px;">
                <h4 style="margin: 0 0 8px 0;">Resumo</h4>
                <p style="margin: 0; color: #444;">Subtotal: <strong>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</strong></p>
                <p style="margin: 4px 0; color: #444;">Desconto: <strong>- R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</strong></p>
                <p style="margin: 4px 0; color: #444;">Frete: <strong>R$ {{ number_format($order->shipping, 2, ',', '.') }}</strong></p>
                <p style="margin: 6px 0 0 0; color: #111; font-weight: 700;">Total: R$ {{ number_format($order->total, 2, ',', '.') }}</p>
            </div>
        </div>

        @if($order->customer_notes)
            <div style="background: #fff8e1; border: 1px solid #ffe9a7; color: #8a6d3b; border-radius: 8px; padding: 12px; margin-top: 16px;">
                <strong>Observações:</strong> {{ $order->customer_notes }}
            </div>
        @endif
    </div>
</div>
@endsection

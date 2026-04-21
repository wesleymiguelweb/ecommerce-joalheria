@extends('layouts.app')

@section('title', 'Meus Pedidos - Elegance Joias')

@section('content')
<div class="container" style="max-width: 1000px; padding: 30px 20px;">
    <h1 style="margin-bottom: 10px;">Meus Pedidos</h1>
    <p style="color: #666; margin-bottom: 25px;">Acompanhe o status e os detalhes das suas compras.</p>

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; padding: 12px; margin-bottom: 16px;">
            {{ session('error') }}
        </div>
    @endif

    @if($orders->isEmpty())
        <div style="background: #f9f9f9; border: 1px dashed #ddd; border-radius: 8px; padding: 20px; text-align: center;">
            <p style="margin: 0 0 10px 0;">Você ainda não possui pedidos.</p>
            <a href="{{ route('index') }}" class="btn btn-dark">Ir para a loja</a>
        </div>
    @else
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid #eee;">
                        <th style="padding: 10px 8px;">Pedido</th>
                        <th style="padding: 10px 8px;">Data</th>
                        <th style="padding: 10px 8px;">Total</th>
                        <th style="padding: 10px 8px;">Status</th>
                        <th style="padding: 10px 8px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td style="padding: 10px 8px; font-weight: 600;">#{{ $order->order_number }}</td>
                            <td style="padding: 10px 8px; color: #666;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td style="padding: 10px 8px; font-weight: 600;">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                            <td style="padding: 10px 8px;">
                                @php
                                    $statusLabels = [
                                        'pending' => ['Pendente', '#ff9800'],
                                        'processing' => ['Em Processamento', '#2196F3'],
                                        'completed' => ['Concluído', '#4CAF50'],
                                        'cancelled' => ['Cancelado', '#f44336'],
                                    ];
                                    $info = $statusLabels[$order->status] ?? [$order->status, '#666'];
                                @endphp
                                <span style="display: inline-block; padding: 6px 10px; border-radius: 12px; color: #fff; background: {{ $info[1] }}; font-size: 0.85em;">{{ $info[0] }}</span>
                            </td>
                            <td style="padding: 10px 8px;">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary" style="padding: 8px 12px;">Ver detalhes</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div style="margin-top: 16px;">
                {{ $orders->links('vendor.pagination.simple') }}
            </div>
        @endif
    @endif
</div>
@endsection

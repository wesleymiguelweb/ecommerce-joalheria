<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Processa um pagamento de forma mock (para desenvolvimento/testes)
    public function process(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'payment_method' => 'required|string',
            // campos de cartão em modo mock
            'card_number' => 'nullable|string',
            'card_holder' => 'nullable|string',
            'card_expiry' => 'nullable|string',
            'card_cvv' => 'nullable|string',
        ]);

        $order = Order::findOrFail($data['order_id']);

        // Apenas o dono do pedido pode processar
        if ($order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Acesso negado ao pedido'], 403);
        }

        // Modo mock: se método for cartão, simulamos autorização
        if (in_array($data['payment_method'], ['credit_card', 'debit_card'])) {
            // Simular checagens básicas — integração real aconteceria aqui
            $order->status = 'paid';
            $order->payment_confirmed_at = now();
            $order->save();

            // Após confirmação do pagamento, limpar carrinho e cupom da sessão
            session()->forget(['cart', 'coupon']);

            return response()->json([
                'success' => true,
                'message' => 'Pagamento simulado com sucesso',
                'order_id' => $order->id,
                'redirect' => route('order.success')
            ]);
        }

        // Para pix/boleto, geramos instruções mock e marcamos como pending_payment
        if (in_array($data['payment_method'], ['pix', 'boleto'])) {
            $order->status = 'pending_payment';
            $order->save();

            $instructions = [];
            if ($data['payment_method'] === 'pix') {
                $instructions = [
                    'type' => 'pix',
                    'code' => '00020126360014BR.GOV.BCB.PIX0114+5561999999995204000053039865405100.005802BR5925Joalheria Exemplo6009Brasilia61080540900062070503***6304ABCD'
                ];
            } else {
                $instructions = [
                    'type' => 'boleto',
                    'due_date' => now()->addDays(3)->toDateString(),
                    'line' => '23790.00009 12345.678901 23456.789012 3 12340000005000'
                ];
            }

            return response()->json(['success' => true, 'message' => 'Instruções geradas', 'instructions' => $instructions, 'order_id' => $order->id]);
        }

        return response()->json(['success' => false, 'message' => 'Método de pagamento não suportado'], 400);
    }

    // Webhook mock: aceita notificações externas para marcar pedido como pago
    public function webhook(Request $request)
    {
        $payload = $request->all();

        // Exemplo simples: receber order_id e status
        if (!isset($payload['order_id']) || !isset($payload['status'])) {
            return response()->json(['success' => false, 'message' => 'Payload inválido'], 400);
        }

        $order = Order::find($payload['order_id']);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pedido não encontrado'], 404);
        }

        if ($payload['status'] === 'paid') {
            $order->status = 'paid';
            $order->payment_confirmed_at = now();
            $order->save();
        }

        return response()->json(['success' => true]);
    }
}

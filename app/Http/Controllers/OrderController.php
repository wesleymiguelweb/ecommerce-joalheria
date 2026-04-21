<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string|max:500',
            'postal_code' => 'required|string|regex:/^\d{5}-?\d{3}$/',
            'payment_method' => 'required|in:credit_card,debit_card,pix,boleto',
            'customer_notes' => 'nullable|string|max:1000',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Carrinho vazio',
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Calcular valores
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $shipping = 15.00; // Frete fixo
            $discountAmount = 0;
            $couponCode = null;

            // Aplicar cupom se existir
            $coupon = session()->get('coupon');
            if ($coupon) {
                $discountAmount = $coupon['discount'];
                $couponCode = $coupon['code'];

                // Incrementar uso do cupom
                $couponModel = Coupon::where('code', $couponCode)->first();
                if ($couponModel) {
                    $couponModel->increment('usage_count');
                }
            }

            $total = $subtotal + $shipping - $discountAmount;

            // Criar pedido
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'discount' => 0,
                'discount_amount' => $discountAmount,
                'shipping' => $shipping,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'customer_notes' => $validated['customer_notes'] ?? null,
                'shipping_address' => $validated['shipping_address'],
                'postal_code' => preg_replace('/[^0-9]/', '', $validated['postal_code']),
                'coupon_code' => $couponCode,
            ]);

            // Criar itens do pedido e atualizar estoque
            $orderItems = [];
            foreach ($cart as $item) {
                $product = Product::find($item['id']);

                if (!$product || $product->stock < $item['quantity']) {
                    throw new \Exception('Produto ' . $item['name'] . ' sem estoque suficiente');
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Guardar dados para página de sucesso
                $orderItems[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ];

                // Reduzir estoque
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // Guardar dados do pedido na sessão para página de sucesso
            session()->flash('order_success', [
                'order_number' => $order->order_number,
                'order_id' => $order->id,
                'items' => $orderItems,
            ]);

            // OBS: não limpar o carrinho aqui — o frontend primeiro processa o pagamento
            // e somente após confirmação (ex: cartão) o carrinho deve ser removido.

            return response()->json([
                'success' => true,
                'message' => 'Pedido realizado com sucesso!',
                'order_number' => $order->order_number,
                'order_id' => $order->id,
                'redirect' => route('order.success'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pedido: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with('items.product')->where('user_id', Auth::id())->findOrFail($id);
        return view('order_details', compact('order'));
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders', compact('orders'));
    }

    public function success()
    {
        $orderData = session('order_success');

        if (!$orderData) {
            return redirect()->route('index')->with('error', 'Pedido não encontrado');
        }

        return view('order-success', [
            'orderNumber' => $orderData['order_number'],
            'orderId' => $orderData['order_id'],
            'orderItems' => $orderData['items'],
        ]);
    }
}

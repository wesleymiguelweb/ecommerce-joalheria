<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Verificar estoque
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Estoque insuficiente. Disponível: ' . $product->stock,
            ], 400);
        }

        // Obter carrinho da sessão
        $cart = session()->get('cart', []);

        $productId = $product->id;

        if (isset($cart[$productId])) {
            // Atualizar quantidade
            $newQuantity = $cart[$productId]['quantity'] + $validated['quantity'];

            if ($newQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estoque insuficiente. Disponível: ' . $product->stock,
                ], 400);
            }

            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            // Adicionar novo item
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'image' => $product->image,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho!',
            'cart_count' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    public function update(Request $request, $productId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($productId);

        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Estoque insuficiente',
            ], 400);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Carrinho atualizado!',
        ]);
    }

    public function remove($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produto removido do carrinho!',
        ]);
    }

    public function clear()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Carrinho limpo!',
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Carrinho vazio',
            ], 400);
        }

        // Calcular subtotal — se o carrinho de sessão estiver vazio, aceitarmos um carrinho enviado no body (fluxo guest)
        $subtotal = 0;

        // Se não houver itens na sessão, e o cliente mandou um 'cart' no request, usamos ele
        $requestCart = $request->input('cart');
        if (empty($cart) && is_array($requestCart) && count($requestCart) > 0) {
            foreach ($requestCart as $item) {
                $price = isset($item['price']) ? floatval($item['price']) : 0;
                $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
                $subtotal += $price * $quantity;
            }
        } else {
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
        }

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Cupom não encontrado',
            ], 404);
        }

        if (!$coupon->isValid($subtotal)) {
            $message = 'Cupom inválido';

            if ($coupon->min_purchase && $subtotal < $coupon->min_purchase) {
                $message = 'Compra mínima de R$ ' . number_format($coupon->min_purchase, 2, ',', '.');
            }

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 400);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        session()->put('coupon', [
            'code' => $coupon->code,
            'discount' => $discount,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'message' => 'Cupom aplicado com sucesso!',
        ]);
    }

    public function validateCep(Request $request)
    {
        $request->validate([
            'cep' => 'required|string|regex:/^\d{5}-?\d{3}$/',
        ]);

        $cep = preg_replace('/[^0-9]/', '', $request->cep);

        // Integração com API ViaCEP (com timeout e tratamento de falha)
        try {
            $response = Http::timeout(5)->get("https://viacep.com.br/ws/{$cep}/json/");
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar CEP. Tente novamente em instantes.',
            ], 500);
        }

        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar CEP. Tente novamente em instantes.',
            ], 500);
        }

        $data = $response->json();

        if (!$data || isset($data['erro'])) {
            return response()->json([
                'success' => false,
                'message' => 'CEP não encontrado',
            ], 404);
        }

        // Calcular frete (simulação simples)
        $shipping = 15.00; // Frete fixo para simplificar

        return response()->json([
            'success' => true,
            'address' => $data,
            'shipping' => $shipping,
        ]);
    }
}

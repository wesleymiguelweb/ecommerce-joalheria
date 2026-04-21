@extends('layouts.app')

@section('title', 'Finalizar Compra - Elegance Joias')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 1200px;">
    @if(!auth()->check())
        <div style="text-align: center; padding: 60px 20px;">
            <h1>Acesso Negado</h1>
            <p style="margin: 20px 0;">Para finalizar a compra, você precisa estar logado.</p>
            <a href="{{ route('login') }}" class="btn btn-dark">Fazer Login</a>
            <span style="margin: 0 10px;">ou</span>
            <a href="{{ route('cadastro') }}" class="btn btn-outline">Criar Conta</a>
        </div>
    @else
        <h1 style="margin-bottom: 10px;">Finalizar Compra</h1>
        <p style="color: #666; margin-bottom: 30px;">Revise seus dados e conclua o pagamento.</p>

        @php
            $cart = session('cart', []);
            $subtotal = 0;
            foreach($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $shipping = 15.00;
            $coupon = session('coupon');
            $discount = $coupon['discount'] ?? 0;
            $total = $subtotal + $shipping - $discount;
        @endphp

        @if(empty($cart))
            <div style="text-align: center; padding: 40px;">
                <h2>Seu carrinho está vazio</h2>
                <p style="margin: 20px 0;">Adicione produtos antes de finalizar a compra.</p>
                <a href="{{ route('index') }}" class="btn btn-dark">Continuar Comprando</a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
                <!-- Formulário de Entrega -->
                <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h2 style="margin-bottom: 20px;">Informações de Entrega</h2>

                    <form id="checkout-form" method="POST" action="{{ route('orders.store') }}">
                        @csrf

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">CEP *</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" id="postal-code" name="postal_code"
                                       maxlength="9" required placeholder="00000-000"
                                       style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <button type="button" id="validate-cep-btn" class="btn btn-secondary"
                                        style="padding: 10px 20px;">Buscar</button>
                            </div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Endereço Completo *</label>
                            <textarea id="address" name="shipping_address" rows="3" required
                                      placeholder="Rua, número, complemento, bairro, cidade"
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                        </div>

                        <h3 style="margin: 30px 0 15px;">Método de Pagamento</h3>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="padding: 15px; border: 2px solid #eee; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="payment_method" value="credit_card" required>
                                <span>Cartão de Crédito</span>
                            </label>
                            <label style="padding: 15px; border: 2px solid #eee; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="payment_method" value="debit_card">
                                <span>Cartão de Débito</span>
                            </label>
                            <label style="padding: 15px; border: 2px solid #eee; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="payment_method" value="pix">
                                <span>PIX</span>
                            </label>
                            <label style="padding: 15px; border: 2px solid #eee; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="payment_method" value="boleto">
                                <span>Boleto</span>
                            </label>
                        </div>

                        <div style="margin-top: 30px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Observações (Opcional)</label>
                            <textarea id="notes" name="customer_notes" rows="2"
                                      placeholder="Adicione qualquer observação importante..."
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                        </div>

                        <div id="card-fields" style="margin-top:20px; display:none;">
                            <h4>Dados do Cartão</h4>
                            <div style="margin-bottom:10px;">
                                <label>Número do cartão</label>
                                <input type="text" id="card_number" name="card_number" class="form-control" placeholder="0000 0000 0000 0000" />
                            </div>
                            <div style="display:flex; gap:10px;">
                                <div style="flex:2;">
                                    <label>Nome no cartão</label>
                                    <input type="text" id="card_holder" name="card_holder" class="form-control" />
                                </div>
                                <div style="flex:1;">
                                    <label>Validade (MM/AA)</label>
                                    <input type="text" id="card_expiry" name="card_expiry" class="form-control" placeholder="MM/AA" />
                                </div>
                                <div style="flex:1;">
                                    <label>CVV</label>
                                    <input type="text" id="card_cvv" name="card_cvv" class="form-control" placeholder="123" />
                                </div>
                            </div>
                        </div>

                        <div id="payment-result" style="margin-top:20px;"></div>

                        <button type="submit" class="btn btn-dark"
                                style="width: 100%; padding: 15px; margin-top: 30px; font-size: 16px;">
                            Finalizar Compra
                        </button>
                    </form>
                </div>

                <!-- Resumo do Pedido -->
                <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); height: fit-content;">
                    <h2 style="margin-bottom: 20px;">Resumo do Pedido</h2>

                    <div style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
                        @foreach($cart as $item)
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                                <span>{{ $item['name'] }} (x{{ $item['quantity'] }})</span>
                                <span>R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Subtotal:</span>
                            <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>

                        @if($coupon)
                            <div style="display: flex; justify-content: space-between; color: #28a745;">
                                <span>Desconto ({{ $coupon['code'] }}):</span>
                                <span>- R$ {{ number_format($discount, 2, ',', '.') }}</span>
                            </div>
                        @endif

                        <div style="display: flex; justify-content: space-between;">
                            <span>Frete:</span>
                            <span id="shipping-cost">R$ {{ number_format($shipping, 2, ',', '.') }}</span>
                        </div>

                        <div style="display: flex; justify-content: space-between; border-top: 2px solid #eee; padding-top: 10px; margin-top: 10px; font-size: 18px; font-weight: bold;">
                            <span>Total:</span>
                            <span id="total-amount">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    @if(!$coupon)
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Cupom de Desconto</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" id="coupon-code" placeholder="Código do cupom"
                                       style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <button type="button" id="apply-coupon-btn" class="btn btn-secondary"
                                        style="padding: 10px 15px;">Aplicar</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <style>
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    text-align: center;
                    text-decoration: none;
                    transition: background 0.3s ease;
                }

                .btn-dark {
                    background: #333;
                    color: white;
                }

                .btn-dark:hover {
                    background: #555;
                }

                .btn-outline {
                    background: transparent;
                    color: #333;
                    border: 2px solid #333;
                }

                .btn-outline:hover {
                    background: #f0f0f0;
                }

                .btn-secondary {
                    background: #c9a55c;
                    color: white;
                }

                .btn-secondary:hover {
                    background: #b8944d;
                }

                @media (max-width: 768px) {
                    [style*="grid-template-columns"] {
                        grid-template-columns: 1fr !important;
                    }
                }
            </style>
        @endif
    @endif
</div>

<script>
async function postJSON(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(body)
    });
    return res.json();
}

document.getElementById('checkout-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    try {
        // 1) Criar o pedido
        const createResult = await postJSON('{{ route('orders.store') }}', data);

        if (!createResult.success) {
            alert('Erro ao criar pedido: ' + (createResult.message || '')); return;
        }

        const orderId = createResult.order_id;
        const paymentMethod = data.payment_method;

        // 2) Processar pagamento de forma mock via endpoint
        const paymentPayload = { order_id: orderId, payment_method: paymentMethod };

        // Se cartão, enviar também campos de cartão
        if (paymentMethod === 'credit_card' || paymentMethod === 'debit_card') {
            paymentPayload.card_number = data.card_number || '';
            paymentPayload.card_holder = data.card_holder || '';
            paymentPayload.card_expiry = data.card_expiry || '';
            paymentPayload.card_cvv = data.card_cvv || '';
        }

        const payResult = await postJSON('{{ route('payment.process') }}', paymentPayload);

        if (!payResult.success) {
            alert('Erro no pagamento: ' + (payResult.message || ''));
            return;
        }

        // Se retorno com instruções (pix/boleto)
        if (payResult.instructions) {
            let html = '<h3>Instruções de Pagamento</h3>';
            if (payResult.instructions.type === 'pix') {
                html += '<p>PIX gerado:</p><pre>' + (payResult.instructions.code || '') + '</pre>';
            } else if (payResult.instructions.type === 'boleto') {
                html += '<p>Boleto vence em: ' + (payResult.instructions.due_date || '') + '</p>';
                html += '<pre>' + (payResult.instructions.line || '') + '</pre>';
            }
            html += '<p>Seu pedido foi criado e está aguardando pagamento. Ao confirmar o pagamento, o pedido será atualizado.</p>';
            document.getElementById('payment-result').innerHTML = html;
            // Não redirecionar automaticamente para permitir que cliente copie instruções
            return;
        }

        // Se pagamento simulado em cartão foi processado => redirecionar ao sucesso
        if (payResult.success) {
            window.location.href = payResult.redirect || createResult.redirect || '{{ route('order.success') }}';
            return;
        }

    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao processar pedido/pagamento');
    }
});
</script>
<script>
// Mostrar/ocultar campos de cartão conforme método selecionado
document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        const cardFields = document.getElementById('card-fields');
        if (this.value === 'credit_card' || this.value === 'debit_card') {
            cardFields.style.display = 'block';
        } else {
            cardFields.style.display = 'none';
        }
    });
});

// Inicializar visibilidade (caso já haja um selecionado)
(function(){
    const selected = document.querySelector('input[name="payment_method"]:checked');
    if (selected) {
        const ev = new Event('change');
        selected.dispatchEvent(ev);
    }
})();
</script>
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $query = Coupon::query();

        // Filtro por código
        if (request('search')) {
            $query->where('code', 'LIKE', '%' . request('search') . '%');
        }

        // Filtro por tipo
        if (request('type')) {
            $query->where('type', request('type'));
        }

        // Filtro por status
        if (request('active') !== null && request('active') !== '') {
            $query->where('active', request('active'));
        }

        // Filtro por validade
        if (request('validity') === 'valid') {
            $query->where('active', true)
                  ->where(function($q) {
                      $q->whereNull('valid_until')
                        ->orWhere('valid_until', '>=', now());
                  });
        } elseif (request('validity') === 'expired') {
            $query->where('valid_until', '<', now());
        }

        // Ordenação
        $sortBy = request('sort', 'created_at');
        $sortOrder = request('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $coupons = $query->paginate(15)->appends(request()->query());

        return view('admin_coupons', compact('coupons'));
    }

    public function create()
    {
        return view('admin_criar_cupom');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'active' => 'nullable|boolean',
        ]);

        // Garantir que active é boolean
        $validated['active'] = isset($validated['active']) ? (bool)$validated['active'] : true;
        $validated['usage_count'] = 0;

        try {
            Coupon::create($validated);
            return redirect()->route('adm.coupons')->with('success', 'Cupom criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar cupom: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin_editar_cupom', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'active' => 'nullable|boolean',
        ]);

        // Garantir que active é boolean
        $validated['active'] = isset($validated['active']) ? (bool)$validated['active'] : false;

        try {
            $coupon->update($validated);
            return redirect()->route('adm.coupons')->with('success', 'Cupom atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar cupom: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('adm.coupons')->with('success', 'Cupom deletado com sucesso!');
    }

    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'cart_total' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Cupom não encontrado',
            ], 404);
        }

        if (!$coupon->isValid($request->cart_total)) {
            return response()->json([
                'success' => false,
                'message' => 'Cupom inválido ou expirado',
            ], 400);
        }

        $discount = $coupon->calculateDiscount($request->cart_total);

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'message' => 'Cupom aplicado com sucesso!',
        ]);
    }
}

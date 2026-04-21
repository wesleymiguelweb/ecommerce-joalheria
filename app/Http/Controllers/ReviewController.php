<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Você precisa estar logado para comentar',
            ], 401);
        }

        $product = Product::findOrFail($productId);

        // Verificar se já comentou
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Você já comentou este produto',
            ], 400);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        try {
            $review = Review::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
                'approved' => false, // Requer aprovação
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comentário enviado! Aguardando aprovação do administrador.',
                'review' => $review,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar comentário: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['approved' => true]);

        return back()->with('success', 'Comentário aprovado!');
    }

    public function reject($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Comentário rejeitado!');
    }

    public function index()
    {
        $status = request('status', 'all');
        $rating = request('rating');
        $search = request('search');

        // Tentar acessar o banco e construir a query. Em caso de erro, retornar uma paginação vazia e mensagem.
        try {
            // Contagens para o painel
            $total_all = Review::count();
            $total_pending = Review::where('approved', false)->count();
            $total_approved = Review::where('approved', true)->count();

            $query = Review::with(['user', 'product'])
                ->orderBy('created_at', 'desc');

            if ($status === 'pending') {
                $query->where('approved', false);
            } elseif ($status === 'approved') {
                $query->where('approved', true);
            }

            if (!empty($rating) && in_array((int)$rating, [1,2,3,4,5], true)) {
                $query->where('rating', (int)$rating);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('product', function ($qp) use ($search) {
                        $qp->where('name', 'like', "%{$search}%");
                    })->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            }

            $reviews = $query->paginate(20)->appends(request()->query());

            return view('admin_reviews', compact('reviews', 'status', 'rating', 'search', 'total_all', 'total_pending', 'total_approved'));
        } catch (\Exception $e) {
            // Problema ao consultar o DB — retornar paginator vazio para evitar erros no view
            $empty = [];
            $reviews = new LengthAwarePaginator($empty, 0, 20);
            $total_all = $total_pending = $total_approved = 0;
            return view('admin_reviews', compact('reviews', 'status', 'rating', 'search', 'total_all', 'total_pending', 'total_approved'))
                ->with('error', 'Erro ao consultar o banco de dados: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        // Buscar reviews aprovadas com dados do usuário (fallback: últimas não-aprovadas se não houver aprovadas)
        $reviews = Review::with('user')
            ->where('approved', true)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Se não houver aprovadas, mostra as mais recentes para não zerar o bloco de feedback
        if ($reviews->isEmpty()) {
            $reviews = Review::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }

        // Buscar marcas únicas dos produtos
        $brands = Product::whereNotNull('brand')
            ->distinct()
            ->pluck('brand')
            ->filter()
            ->values()
            ->toArray();

        return view('index', compact('products', 'reviews', 'brands'));
    }

    public function feminino()
    {
        // Buscar produtos iniciais para renderização (será substituído por API)
        $products = Product::where('category', 'feminino')->limit(12)->get();
        return view('feminino', compact('products'));
    }

    public function masculino()
    {
        // Buscar produtos iniciais para renderização (será substituído por API)
        $products = Product::where('category', 'masculino')->limit(12)->get();
        return view('masculino', compact('products'));
    }

    public function show($id)
    {
        $product = Product::with('approvedReviews.user')->findOrFail($id);
        return view('detalhe-produto', compact('product'));
    }

    public function search()
    {
        $query = Product::query();
        $searchTerm = request('q');

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('brand', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filtros adicionais
        if (request('category')) {
            $query->where('category', request('category'));
        }
        if (request('brand')) {
            $query->where('brand', request('brand'));
        }
        if (request('color')) {
            $query->where('color', request('color'));
        }
        if (request('min_price')) {
            $query->where('price', '>=', request('min_price'));
        }
        if (request('max_price')) {
            $query->where('price', '<=', request('max_price'));
        }

        $products = $query->paginate(12)->appends(request()->query());

        return view('search_results', compact('products', 'searchTerm'));
    }


    /**
     * API - Retorna produtos filtrados com opções avançadas
     * GET /api/products-filter
     */
    public function filterProducts()
    {
        $query = Product::query();

        // Filtro de categoria
        $category = request('category');
        if ($category && $category !== 'todos') {
            $query->where('category', $category);
        }

        // Filtro de tipo (anel, colar, etc)
        $type = request('type');
        if ($type && $type !== 'todos') {
            $query->whereRaw("LOWER(name) LIKE ?", ["%{$type}%"]);
        }

        // Filtro de marca
        $brand = request('brand');
        if ($brand && $brand !== 'todos') {
            $query->where('brand', $brand);
        }

        // Filtro de cor
        $color = request('color');
        if ($color) {
            $query->where('color', $color);
        }

        // Filtro de preço
        $maxPrice = request('max_price');
        if ($maxPrice) {
            $query->where('price', '<=', floatval($maxPrice));
        }

        $minPrice = request('min_price');
        if ($minPrice) {
            $query->where('price', '>=', floatval($minPrice));
        }

        // Busca por texto
        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereRaw("LOWER(name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("LOWER(description) LIKE ?", ["%{$search}%"]);
            });
        }

        // Ordenação
        $sort = request('sort', 'popular');
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price-asc':
                $query->orderBy('price', 'asc')
                      ->orderBy('name', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('price', 'desc')
                      ->orderBy('name', 'asc');
                break;
            case 'name-asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name-desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popular':
            default:
                $query->orderBy('stock', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }

        // Paginação
        $perPage = request('per_page', 12);
        $page = request('page', 1);

        // Contar total antes de paginar
        $total = $query->count();

        // Aplicar paginação
        $products = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();

        return response()->json([
            'success' => true,
            'products' => $products,
            'total' => $total
        ]);
    }
}

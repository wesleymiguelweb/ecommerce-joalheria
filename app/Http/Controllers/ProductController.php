<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;

class ProductController extends Controller
{
    public function index()
    {
        // Demo data for portfolio - no DB required
        $demoProducts = [
            (object)[
                'id' => 1,
                'name' => 'Corrente Masculina',
                'description' => 'Corrente elegante com design clássico.',
                'price' => 850.00,
                'stock' => 5,
                'min_stock' => 3,
                'image' => 'anelCriativo.png',
                'category' => 'masculino',
                'brand' => 'Joalheria Luxo',
                'color' => 'ouro'
            ],
            (object)[
                'id' => 2,
                'name' => 'Corrente Prata Masculina',
                'description' => 'Corrente robusta em prata esterlina.',
                'price' => 420.00,
                'stock' => 12,
                'min_stock' => 5,
                'image' => 'Masculino/Correntes Masculinas/corrente_bolinhas_1_mm_60_cm_19823_1_7a54fbb5479dfc82ec470e9a6df7a016-1024-1024.webp',
                'category' => 'masculino',
                'brand' => 'Silver King',
                'color' => 'prata'
            ],
            (object)[
                'id' => 3,
                'name' => 'Relógio Masculino',
                'description' => 'Relógio clássico e sofisticado.',
                'price' => 1280.00,
                'stock' => 8,
                'min_stock' => 2,
                'image' => 'Masculino/Relógios Masculinos/NIM011372_1.jpg',
                'category' => 'masculino',
                'brand' => 'Elegance',
                'color' => 'prata'
            ]
        ];
        $products = collect($demoProducts);
        // Buscar reviews aprovadas com dados do usuário (fallback: últimas não-aprovadas se não houver aprovadas)
        $demoReviews = [
            (object)['id' => 1, 'comment' => 'Produto incrível! Superou minhas expectativas.', 'rating' => 5, 'approved' => true, 'user' => (object)['name' => 'Maria Silva']],
            (object)['id' => 2, 'comment' => 'Excelente qualidade prata.', 'rating' => 5, 'approved' => true, 'user' => (object)['name' => 'João Santos']],
            (object)['id' => 3, 'comment' => 'Entrega rápida e embalagem perfeita!', 'rating' => 5, 'approved' => true, 'user' => (object)['name' => 'Ana Costa']]
        ];
        $reviews = collect($demoReviews);

        // Buscar marcas únicas dos produtos
        $brands = ['Joalheria Luxo', 'Silver King', 'Elegance'];

        return view('index', compact('products', 'reviews', 'brands'));
    }

    public function feminino()
    {
        $demoProducts = collect([
            (object)[
                'id' => 4,
                'name' => 'Colar Prata Feminino',
                'description' => 'Colar delicado em prata com pingente.',
                'price' => 320.00,
                'stock' => 15,
                'min_stock' => 5,
                'image' => 'Feminino/Colares Femininos/colar1.jpg',
                'category' => 'feminino',
                'brand' => 'Elegance',
                'color' => 'prata'
            ],
            (object)[
                'id' => 5,
                'name' => 'Bracelete Ouro',
                'description' => 'Bracelete largo em ouro 18k.',
                'price' => 950.00,
                'stock' => 3,
                'min_stock' => 2,
                'image' => 'Feminino/Braceletes Femininos/bracelete1.jpg',
                'category' => 'feminino',
                'brand' => 'Joalheria Luxo',
                'color' => 'ouro'
            ]
        ]);
        $products = $demoProducts;
        return view('feminino', compact('products'));
    }

    public function masculino()
    {
        $demoProducts = collect([
            (object)[
                'id' => 6,
                'name' => 'Pulseira Prata Masculina',
                'description' => 'Pulseira robusta em prata.',
                'price' => 280.00,
                'stock' => 10,
                'min_stock' => 3,
                'image' => 'Masculino/Pulseira Masculina/pulseira1.jpg',
                'category' => 'masculino',
                'brand' => 'Silver King',
                'color' => 'prata'
            ],
            (object)[
                'id' => 7,
                'name' => 'Relógio Ouro Masculino',
                'description' => 'Relógio clássico em ouro.',
                'price' => 1250.00,
                'stock' => 4,
                'min_stock' => 2,
                'image' => 'Masculino/Relógios Masculinos/relogio1.jpg',
                'category' => 'masculino',
                'brand' => 'Luxo Time',
                'color' => 'ouro'
            ]
        ]);
        $products = $demoProducts;
        return view('masculino', compact('products'));
    }

    public function show($id)
    {
        // Demo products to search from
        $demoProducts = collect([
            (object)['id' => 1, 'name' => 'Corrente Masculina', 'description' => 'Corrente elegante com design clássico.', 'price' => 850.00, 'stock' => 5, 'min_stock' => 3, 'image' => 'anelCriativo.png', 'category' => 'masculino', 'brand' => 'Joalheria Luxo', 'color' => 'ouro'],
            (object)['id' => 2, 'name' => 'Corrente Prata Masculina', 'description' => 'Corrente robusta em prata esterlina.', 'price' => 420.00, 'stock' => 12, 'min_stock' => 5, 'image' => 'Masculino/Correntes Masculinas/corrente_bolinhas_1_mm_60_cm_19823_1_7a54fbb5479dfc82ec470e9a6df7a016-1024-1024.webp', 'category' => 'masculino', 'brand' => 'Silver King', 'color' => 'prata'],
            (object)['id' => 3, 'name' => 'Relógio Masculino', 'description' => 'Relógio clássico e sofisticado.', 'price' => 1280.00, 'stock' => 8, 'min_stock' => 2, 'image' => 'Masculino/Relógios Masculinos/NIM011372_1.jpg', 'category' => 'masculino', 'brand' => 'Elegance', 'color' => 'prata'],
            (object)['id' => 4, 'name' => 'Colar Prata Feminino', 'description' => 'Colar delicado em prata com pingente.', 'price' => 320.00, 'stock' => 15, 'min_stock' => 5, 'image' => 'Feminino/Colares Femininos/colar1.jpg', 'category' => 'feminino', 'brand' => 'Elegance', 'color' => 'prata'],
            (object)['id' => 5, 'name' => 'Bracelete Ouro', 'description' => 'Bracelete largo em ouro 18k.', 'price' => 950.00, 'stock' => 3, 'min_stock' => 2, 'image' => 'Feminino/Braceletes Femininos/bracelete1.jpg', 'category' => 'feminino', 'brand' => 'Joalheria Luxo', 'color' => 'ouro'],
            (object)['id' => 6, 'name' => 'Pulseira Prata Masculina', 'description' => 'Pulseira robusta em prata.', 'price' => 280.00, 'stock' => 10, 'min_stock' => 3, 'image' => 'Masculino/Pulseira Masculina/pulseira1.jpg', 'category' => 'masculino', 'brand' => 'Silver King', 'color' => 'prata'],
            (object)['id' => 7, 'name' => 'Relógio Ouro Masculino', 'description' => 'Relógio clássico em ouro.', 'price' => 1250.00, 'stock' => 4, 'min_stock' => 2, 'image' => 'Masculino/Relógios Masculinos/relogio1.jpg', 'category' => 'masculino', 'brand' => 'Luxo Time', 'color' => 'ouro'],
            (object)['id' => 8, 'name' => 'Anel Prata', 'description' => 'Anel de prata.', 'price' => 250.00, 'stock' => 8, 'min_stock' => 2, 'image' => 'Feminino/Alianças Feminas/anel1.jpg', 'category' => 'feminino', 'brand' => 'Elegance', 'color' => 'prata'],
            (object)['id' => 9, 'name' => 'Pulseira Ouro', 'description' => 'Pulseira de Ouro.', 'price' => 450.00, 'stock' => 8, 'min_stock' => 2, 'image' => 'Masculino/Pulseira Masculina/pulseira1.jpg', 'category' => 'masculino', 'brand' => 'Luxo Time', 'color' => 'ouro']
        ]);

        $product = $demoProducts->firstWhere('id', (int)$id);

        if (!$product) {
            $product = (object)[
                'id' => $id,
                'name' => 'Brincos Prata Feminino',
                'description' => 'Brincos delicados em prata com zircônia. Produto de alta qualidade com design exclusivo.',
                'price' => 180.00,
                'stock' => 8,
                'min_stock' => 2,
                'image' => 'Feminino/Brincos Feminos/brincos1.jpg',
                'category' => 'feminino',
                'brand' => 'Elegance',
                'color' => 'prata'
            ];
        }

        $product->approvedReviews = collect([
            (object)['comment' => 'Perfeitos! Muito delicados.', 'rating' => 5, 'user' => (object)['name' => 'Maria Silva'], 'created_at' => now()],
            (object)['comment' => 'Qualidade excelente pela prata.', 'rating' => 5, 'user' => (object)['name' => 'Ana Costa'], 'created_at' => now()]
        ]);

        return view('detalhe-produto', compact('product'));
    }

    public function search()
    {
        // Demo search results for portfolio
        $demoProducts = collect([
            (object)['id' => 8, 'name' => 'Anel Prata', 'price' => 250.00, 'image' => 'Feminino/Alianças Feminas/anel1.jpg'],
            (object)['id' => 9, 'name' => 'Pulseira Ouro', 'price' => 450.00, 'image' => 'Masculino/Pulseira Masculina/pulseira1.jpg']
        ]);
        $products = new \Illuminate\Pagination\LengthAwarePaginator($demoProducts, 20, 9, 1);
        $searchTerm = request('q', 'prata');
        $searchTerm = request('q');

        // Retornar demo view mesmo sem query real
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
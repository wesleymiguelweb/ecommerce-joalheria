<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalAdmins = User::where('is_admin', true)->count();
        $lowStockProducts = Product::whereRaw('stock <= COALESCE(min_stock, 5)')->count();
        $pendingReviews = \App\Models\Review::where('approved', false)->count();
        $totalReviews = \App\Models\Review::count();

        return view('admin_dashboard', compact('totalProducts', 'totalUsers', 'totalAdmins', 'lowStockProducts', 'pendingReviews', 'totalReviews'));
    }

    public function products()
    {
        $query = Product::query();

        // Filtro por categoria
        if (request('category')) {
            $query->where('category', request('category'));
        }

        // Filtro por marca
        if (request('brand')) {
            $query->where('brand', request('brand'));
        }

        // Filtro por estoque baixo
        if (request('low_stock') === '1') {
            $query->whereRaw('stock <= COALESCE(min_stock, 5)');
        }

        // Busca por nome
        if (request('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }

        // Ordenação
        $sortBy = request('sort', 'created_at');
        $sortOrder = request('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(10)->appends(request()->query());

        // Buscar categorias e marcas únicas para os filtros
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();
        $brands = Product::distinct()->pluck('brand')->filter()->sort()->values();

        return view('admin_produtos', compact('products', 'categories', 'brands'));
    }

    public function createProduct()
    {
        return view('admin_cadastrar_produto');
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'text' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:feminino,masculino',
            'brand' => 'nullable|string|max:100',
            'color' => 'required|string',
            'new_color' => 'required_if:color,__nova__|nullable|string|max:50',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'image_url' => 'nullable|url',
        ], [
            'name.required' => 'Nome do produto é obrigatório',
            'description.required' => 'Descrição é obrigatória',
            'price.required' => 'Preço é obrigatório',
            'category.required' => 'Categoria é obrigatória',
            'color.required' => 'Cor é obrigatória',
            'new_color.required_if' => 'Digite o nome da nova cor',
            'stock.required' => 'Estoque é obrigatório',
            'image.image' => 'O arquivo deve ser uma imagem',
            'image.max' => 'A imagem não pode ser maior que 2MB',
        ]);

        try {
            // Processar cor
            if ($validated['color'] === '__nova__') {
                $validated['color'] = strtolower(trim($validated['new_color']));
            }
            unset($validated['new_color']);

            // Upload de imagem
            if ($request->hasFile('image')) {
                // Garante que o diretório existe
                $imgPath = public_path('img');
                if (!file_exists($imgPath)) {
                    mkdir($imgPath, 0755, true);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($imgPath, $imageName);
                $validated['image'] = $imageName;
            } elseif ($request->filled('image_url')) {
                // Usar URL fornecida
                $validated['image'] = $request->image_url;
            } else {
                // Auto-atribuir imagem baseada no nome do produto
                $validated['image'] = $this->autoAssignImage($validated['name'], $validated['category']);
            }

            Product::create($validated);
            return redirect()->route('adm.produto')->with('success', 'Produto criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar produto: ' . $e->getMessage());
        }
    }

    /**
     * Auto-atribui uma imagem baseada no tipo de produto
     */
    private function autoAssignImage($name, $category)
    {
        $nameLower = mb_strtolower($name);

        // Verifica o tipo de produto pelo nome
        if (str_contains($nameLower, 'anel')) {
            return 'anel-safira-azul.webp';
        } elseif (str_contains($nameLower, 'colar')) {
            return 'colar-corrente-fina.webp';
        } elseif (str_contains($nameLower, 'brinco')) {
            return 'anel-safira-azul.webp'; // Fallback
        } elseif (str_contains($nameLower, 'pulseira')) {
            return 'anel-ouro-rosa.webp'; // Fallback
        } elseif (str_contains($nameLower, 'relógio') || str_contains($nameLower, 'relogio')) {
            return 'relogio1.png';
        } elseif (str_contains($nameLower, 'corrente')) {
            return 'colar-corrente-fina.webp';
        }

        // Imagem padrão baseada na categoria
        return $category === 'feminino' ? 'anel-safira-azul.webp' : 'relogio1.png';
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        return view('admin_editar_produto', compact('product'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'text' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:feminino,masculino',
            'brand' => 'nullable|string|max:100',
            'color' => 'required|string',
            'new_color' => 'required_if:color,__nova__|nullable|string|max:50',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'image_url' => 'nullable|url',
        ]);

        try {
            // Processar cor
            if ($validated['color'] === '__nova__') {
                $validated['color'] = strtolower(trim($validated['new_color']));
            }
            unset($validated['new_color']);

            // Upload de nova imagem
            if ($request->hasFile('image')) {
                // Remove imagem antiga se não for padrão
                if ($product->image && file_exists(public_path('img/' . $product->image))) {
                    $defaultImages = ['anel-safira-azul.webp', 'colar-corrente-fina.webp', 'relogio1.png', 'anel-ouro-rosa.webp'];
                    if (!in_array($product->image, $defaultImages)) {
                        @unlink(public_path('img/' . $product->image));
                    }
                }

                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('img'), $imageName);
                $validated['image'] = $imageName;
            } elseif ($request->filled('image_url')) {
                $validated['image'] = $request->image_url;
            }

            $product->update($validated);
            return redirect()->route('adm.produto')->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return redirect()->route('adm.produto')->with('success', 'Produto deletado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao deletar produto: ' . $e->getMessage());
        }
    }

    public function users()
    {
        // Query para usuários comuns
        $usersQuery = User::where('is_admin', false);

        // Query para administradores
        $adminsQuery = User::where('is_admin', true);

        // Filtros aplicáveis a ambos
        if (request('search')) {
            $search = request('search');
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%');
            });
            $adminsQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%');
            });
        }

        // Filtro por data de registro
        if (request('date_from')) {
            $usersQuery->whereDate('created_at', '>=', request('date_from'));
            $adminsQuery->whereDate('created_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $usersQuery->whereDate('created_at', '<=', request('date_to'));
            $adminsQuery->whereDate('created_at', '<=', request('date_to'));
        }

        // Ordenação
        $sortBy = request('sort', 'created_at');
        $sortOrder = request('order', 'desc');
        $usersQuery->orderBy($sortBy, $sortOrder);
        $adminsQuery->orderBy($sortBy, $sortOrder);

        $users = $usersQuery->paginate(10, ['*'], 'users')->appends(request()->query());
        $admins = $adminsQuery->paginate(10, ['*'], 'admins')->appends(request()->query());

        return view('admin_usuarios', compact('users', 'admins'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin_editar_usuario', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'is_admin' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = bcrypt($validated['password']);
            }

            if (isset($validated['is_admin'])) {
                $user->is_admin = $validated['is_admin'];
            }

            // Processar upload de avatar
            if ($request->hasFile('avatar')) {
                // Deletar avatar antigo se existir
                if ($user->avatar && file_exists(public_path($user->avatar))) {
                    unlink(public_path($user->avatar));
                }

                $avatar = $request->file('avatar');
                $avatarName = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('img/avatars'), $avatarName);
                $user->avatar = 'img/avatars/' . $avatarName;
            }

            $user->save();

            return redirect()->route('adm.usuarios')->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar usuário: ' . $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        try {
            if ($id === Auth::id()) {
                return back()->with('error', 'Você não pode deletar sua própria conta!');
            }

            $user = User::findOrFail($id);
            $user->delete();
            return redirect()->route('adm.usuarios')->with('success', 'Usuário deletado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao deletar usuário: ' . $e->getMessage());
        }
    }

    public function createAdmin()
    {
        return view('admin_criar_usuario');
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'is_admin' => 'nullable|boolean',
        ]);

        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'is_admin' => isset($validated['is_admin']) ? $validated['is_admin'] : false,
            ]);

            return redirect()->route('adm.usuarios')->with('success', 'Usuário criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar usuário: ' . $e->getMessage());
        }
    }

    public function orders()
    {
        $query = \App\Models\Order::with('user', 'items.product');

        // Filtro por status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filtro por busca (número do pedido ou nome do cliente)
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('email', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Filtro por data
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        // Ordenação
        $sortBy = request('sort', 'created_at');
        $sortOrder = request('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(15)->appends(request()->query());

        return view('admin_pedidos', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = \App\Models\Order::with('user', 'items.product')->findOrFail($id);
        return view('admin_detalhes_pedido', compact('order'));
    }
}

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReviewController;

// Rotas Públicas
Route::get('/', [ProductController::class, 'index'])->name('index');
Route::get('/feminino', [ProductController::class, 'feminino'])->name('feminino');
Route::get('/masculino', [ProductController::class, 'masculino'])->name('masculino');
Route::get('/produto/{id?}', [ProductController::class, 'show'])->name('produto');
Route::get('/pesquisa', [ProductController::class, 'search'])->name('search');
// Reviews (controller valida login e permissões)
Route::post('/produtos/{id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

// Rotas de Autenticação
Route::controller(AuthController::class)->middleware('guest')->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.store');

    Route::get('/cadastro', 'showRegister')->name('cadastro');
    Route::post('/cadastro', 'register')->name('cadastro.store');
});

# Logout (protegido)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rotas de Carrinho (acesso livre — usa sessão e fallback para localStorage)
Route::get('/carrinho', function () {
    $cart = session()->get('cart', []);
    $coupon = session()->get('coupon');
    return view('carrinho', compact('cart', 'coupon'));
})->name('carrinho');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
Route::post('/cart/validate-cep', [CartController::class, 'validateCep'])->name('cart.validateCep');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::get('/checkout', function () {
        $cart = session()->get('cart', []);
        $coupon = session()->get('coupon');
        return view('checkout', compact('cart', 'coupon'));
    })->name('checkout');

    // Rotas de Pedidos
    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pedidos', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/pedido-sucesso', [OrderController::class, 'success'])->name('order.success');

    // Pagamentos (mock / integração)
    Route::post('/payment/process', [\App\Http\Controllers\PaymentController::class, 'process'])->name('payment.process');
    Route::post('/payment/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');

    // Perfil do usuário
    Route::get('/perfil', [AuthController::class, 'profile'])->name('profile');
    Route::put('/perfil', [AuthController::class, 'updateProfile'])->name('profile.update');
});

// Rotas Admin (Protegidas por autenticação e middleware admin)
Route::prefix('adm')->middleware(['auth', 'admin'])->name('adm.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Produtos
    Route::get('/produtos', [AdminController::class, 'products'])->name('produto');
    Route::get('/produtos/criar', [AdminController::class, 'createProduct'])->name('produto.criar');
    Route::post('/produtos', [AdminController::class, 'storeProduct'])->name('produto.store');
    Route::get('/produtos/{id}/editar', [AdminController::class, 'editProduct'])->name('produto.editar');
    Route::put('/produtos/{id}', [AdminController::class, 'updateProduct'])->name('produto.update');
    Route::delete('/produtos/{id}', [AdminController::class, 'deleteProduct'])->name('produto.delete');

    // Usuários
    Route::get('/usuarios', [AdminController::class, 'users'])->name('usuarios');
    Route::get('/usuarios/{id}/editar', [AdminController::class, 'editUser'])->name('usuarios.editar');
    Route::put('/usuarios/{id}', [AdminController::class, 'updateUser'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [AdminController::class, 'deleteUser'])->name('usuarios.delete');

    // Criar novo usuário/admin
    Route::get('/usuarios/criar', [AdminController::class, 'createAdmin'])->name('usuarios.criar');
    Route::post('/usuarios', [AdminController::class, 'storeAdmin'])->name('usuarios.store');

    // Cupons
    Route::get('/cupons', [CouponController::class, 'index'])->name('coupons');
    Route::get('/cupons/criar', [CouponController::class, 'create'])->name('coupons.create');
    Route::post('/cupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::get('/cupons/{id}/editar', [CouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/cupons/{id}', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('/cupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');

    // Pedidos
    Route::get('/pedidos', [AdminController::class, 'orders'])->name('orders');
    Route::get('/pedidos/{id}', [AdminController::class, 'showOrder'])->name('orders.show');

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews/{id}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{id}', [ReviewController::class, 'reject'])->name('reviews.reject');
});

// Rotas de Informação
Route::get('/sobre', function () {
    return view('sobre');
})->name('sobre');

// Página de pagamento (pública; mostrará aviso se não logado)
Route::get('/pagamento', function () {
    $cart = session()->get('cart', []);
    $coupon = session()->get('coupon');
    return view('pagamento', compact('cart', 'coupon'));
})->name('pagamento');

Route::get('/suporte', function () {
    return view('suporte');
})->name('suporte');

Route::get('/termos', function () {
    return view('termos');
})->name('termos');

Route::get('/privacidade', function () {
    return view('privacidade');
})->name('privacidade');

// API Routes para produtos
Route::get('/api/products-filter', [ProductController::class, 'filterProducts']);

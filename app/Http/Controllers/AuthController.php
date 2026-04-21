<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('index');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        // Validar entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email é obrigatório',
            'email.email' => 'Email inválido',
            'password.required' => 'Senha é obrigatória',
            'password.min' => 'Senha deve ter no mínimo 6 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Guardar carrinho antes de fazer login (antes da sessão ser regenerada)
        $cartBeforeLogin = session()->get('cart', []);
        $couponBeforeLogin = session()->get('coupon');

        // Tentar autenticar
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $user = Auth::user();

            // Restaurar carrinho após login bem-sucedido
            if (!empty($cartBeforeLogin)) {
                session()->put('cart', $cartBeforeLogin);
            }

            if ($couponBeforeLogin) {
                session()->put('coupon', $couponBeforeLogin);
            }

            // Redirecionar admin para dashboard, cliente para home
            $redirect = $user->is_admin ? route('adm.dashboard') : route('index');

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'redirect' => $redirect
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email ou senha incorretos'
        ], 401);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('index');
        }
        return view('cadastro');
    }

    public function register(Request $request)
    {
        // Validar entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Nome é obrigatório',
            'name.min' => 'Nome deve ter no mínimo 3 caracteres',
            'email.required' => 'Email é obrigatório',
            'email.email' => 'Email inválido',
            'email.unique' => 'Este email já está registrado',
            'password.required' => 'Senha é obrigatória',
            'password.min' => 'Senha deve ter no mínimo 6 caracteres',
            'password.confirmed' => 'Senhas não conferem',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Guardar carrinho antes de criar sessão nova
        $cartBeforeRegister = session()->get('cart', []);
        $couponBeforeRegister = session()->get('coupon');

        // Criar novo usuário
        try {
            // Cadastro público sempre cria clientes (nunca admin)
            // Apenas admins podem criar outros admins via painel administrativo
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => false, // Sempre cliente no cadastro público
            ]);

            // Autenticar automaticamente
            Auth::login($user);

            // Restaurar carrinho após registro bem-sucedido
            if (!empty($cartBeforeRegister)) {
                session()->put('cart', $cartBeforeRegister);
            }

            if ($couponBeforeRegister) {
                session()->put('coupon', $couponBeforeRegister);
            }

            // Cliente sempre vai para home após cadastro
            return response()->json([
                'success' => true,
                'message' => 'Cadastro realizado com sucesso!',
                'redirect' => route('index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar conta. Tente novamente.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index')->with('success', 'Desconectado com sucesso!');
    }

    public function profile()
    {
        $user = Auth::user();
        $orders = $user->orders()->orderBy('created_at', 'desc')->take(5)->get();
        return view('profile', compact('user', 'orders'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:6|confirmed',
        ], [
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'Email é obrigatório',
            'email.unique' => 'Este email já está em uso',
            'current_password.required_with' => 'Senha atual é obrigatória para alterar a senha',
            'password.min' => 'Nova senha deve ter no mínimo 6 caracteres',
            'password.confirmed' => 'Senhas não conferem',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verificar senha atual se estiver tentando mudar a senha
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Senha atual incorreta'])->withInput();
            }
            $user->password = Hash::make($request->password);
        }

        // Nota: funcionalidade de avatar removida — não processamos arquivos aqui.

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}

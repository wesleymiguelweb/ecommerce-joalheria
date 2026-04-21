@extends('layouts.app')

@section('title', 'Login - Elegance Joias')

@section('content')
<div class="container">
        <button class="btn-back" data-history-back style="margin-bottom: 15px;">Voltar</button>
        <nav class="breadcrumb">
            <a href="{{ route('index') }}">Página Inicial</a>
            <span>&gt;</span>
            <span class="current">Login</span>
        </nav>

        <div class="auth-page-container">
            <form class="auth-form" id="loginForm">
                <h1>Entrar na Sua Conta</h1>
                <div class="auth-field">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" class="input-field" placeholder="seu@email.com" required>
                </div>
                <div class="auth-field">
                    <label for="login-password">Senha</label>
                    <div style="position: relative;">
                        <input type="password" id="login-password" class="input-field" placeholder="••••••" required>
                        <button type="button" onclick="togglePassword('login-password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; font-size: 1.2rem;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <p class="auth-link">Não tem conta? <a href="{{ route('cadastro') }}">Cadastre-se aqui!</a></p>
                <button type="submit" class="btn btn-dark" style="width: 100%; margin-top: 20px;">Entrar</button>
            </form>
    </div>
</div>

@include('partials.contact')

<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection

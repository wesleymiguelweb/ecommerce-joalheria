@extends('layouts.app')

@section('title', 'Cadastro - Elegance Joias')

@section('content')
<div class="container">

    <button class="btn-back" data-history-back style="margin-bottom: 15px;">Voltar</button>
    <nav class="breadcrumb">
        <a href="{{ route('index') }}">Página Inicial</a>
        <span>&gt;</span>
        <span class="current">Cadastro</span>
    </nav>

    <div class="auth-page-container">
        <form class="auth-form" id="registerForm">
            <h1>Criar Conta</h1>
            <div class="auth-field">
                <label for="register-name">Nome Completo</label>
                <input type="text" id="register-name" placeholder="Seu nome" class="input-field" required>
            </div>
            <div class="auth-field">
                <label for="register-email">Email</label>
                <input type="email" id="register-email" placeholder="seu@email.com" class="input-field" required>
            </div>
            <div class="auth-field">
                <label for="register-password">Senha</label>
                <div style="position: relative;">
                    <input type="password" id="register-password" placeholder="••••••" class="input-field" required>
                    <button type="button" onclick="togglePassword('register-password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; font-size: 1.2rem;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
                <div class="auth-field">
                    <label for="register-password-confirm">Confirmar Senha</label>
                    <div style="position: relative;">
                        <input type="password" id="register-password-confirm" placeholder="••••••" class="input-field" required>
                        <button type="button" onclick="togglePassword('register-password-confirm', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; font-size: 1.2rem;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <p class="auth-link">Já tem conta? <a href="{{ route('login') }}">Faça login!</a></p>
                <button type="submit" class="btn btn-dark" style="width: 100%; margin-top: 20px;">Cadastrar</button>
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

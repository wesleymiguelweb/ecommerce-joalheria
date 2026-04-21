@extends('layouts.admin')

@section('title', 'Painel ADM - Editar Usuário')

@section('breadcrumb', 'Editar Usuário')

@section('content')

<div class="admin-card">
    <h2>Editar Usuário</h2>
    <p class="subtitle">Atualize as informações do usuário: <strong>{{ $user->name }}</strong></p>

    @if ($errors->any())
        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            <strong>Erro ao validar formulário:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($message = session('error'))
        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            {{ $message }}
        </div>
    @endif

    <form action="{{ route('adm.usuarios.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf
        @method('PUT')

        <div class="form-fields" style="max-width: 600px;">
            <!-- Campo de avatar removido do formulário de edição de usuário -->

            <div class="form-group">
                <label for="name">Nome Completo *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Ex: João Silva">
                @error('name')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Ex: joao@exemplo.com">
                @error('email')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="password">Nova Senha (deixe em branco para manter a atual)</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres">
                    <button type="button" onclick="togglePassword('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; font-size: 1.2rem;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Nova Senha</label>
                <div style="position: relative;">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repita a senha">
                    <button type="button" onclick="togglePassword('password_confirmation', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; font-size: 1.2rem;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" id="is_admin" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                    <span>Este usuário é administrador</span>
                </label>
                <small style="color: #666; display: block; margin-top: 0.25rem;">Administradores têm acesso total ao painel</small>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('adm.usuarios') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </form>

    <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #ddd;">
        <h3 style="color: #666; font-size: 0.9em; margin-bottom: 0.5rem;">INFORMAÇÕES DO USUÁRIO</h3>
        <p style="color: #999; font-size: 0.85em;">ID: {{ $user->id }}</p>
        <p style="color: #999; font-size: 0.85em;">Cadastrado em: {{ $user->created_at->format('d/m/Y H:i') }}</p>
        <p style="color: #999; font-size: 0.85em;">Última atualização: {{ $user->updated_at->format('d/m/Y H:i') }}</p>
    </div>
</div>

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

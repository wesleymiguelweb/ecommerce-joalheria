@extends('layouts.app')

@section('title', 'Meu Perfil - Elegance Joias')

@section('content')
<div class="container" style="padding: 40px 20px;">
    <div class="profile-container" style="max-width: 800px; margin: 0 auto;">
        <h1 style="margin-bottom: 30px;">Meu Perfil</h1>

        @if(session('success'))
            <div class="alert alert-success" style="padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 20px;">Informações Pessoais</h2>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Seção de avatar removida do perfil -->

                <hr style="margin: 30px 0;">

                <div style="margin-bottom: 20px;">
                    <label for="name" style="display: block; margin-bottom: 5px; font-weight: 500;">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 5px; font-weight: 500;">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <hr style="margin: 30px 0;">

                <h3 style="margin-bottom: 15px;">Alterar Senha</h3>
                <p style="color: #666; margin-bottom: 20px;">Deixe em branco se não quiser alterar a senha</p>

                <div style="margin-bottom: 20px;">
                    <label for="current_password" style="display: block; margin-bottom: 5px; font-weight: 500;">Senha Atual</label>
                    <input type="password" id="current_password" name="current_password"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; margin-bottom: 5px; font-weight: 500;">Nova Senha</label>
                    <input type="password" id="password" name="password"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="password_confirmation" style="display: block; margin-bottom: 5px; font-weight: 500;">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <button type="submit" class="btn btn-dark" style="padding: 12px 30px; border-radius: 5px;">
                    Atualizar Perfil
                </button>
            </form>
        </div>

        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-top: 30px;">
            <h2 style="margin-bottom: 20px;">Meus Pedidos Recentes</h2>

            @forelse($orders as $order)
                <div style="border-bottom: 1px solid #eee; padding: 15px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>Pedido #{{ $order->order_number }}</strong>
                            <p style="color: #666; margin: 5px 0;">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 18px; font-weight: bold; margin: 0;">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                            <span style="padding: 5px 10px; border-radius: 15px; font-size: 12px;
                                  @if($order->status === 'completed') background: #d4edda; color: #155724;
                                  @elseif($order->status === 'pending') background: #fff3cd; color: #856404;
                                  @else background: #d1ecf1; color: #0c5460; @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('orders.show', $order->id) }}" style="color: #007bff; text-decoration: none; font-size: 14px;">
                        Ver detalhes →
                    </a>
                </div>
            @empty
                <p style="text-align: center; color: #666; padding: 20px;">Você ainda não fez nenhum pedido.</p>
            @endforelse

            @if($orders->count() > 0)
                <a href="{{ route('orders.index') }}" class="btn btn-outline" style="margin-top: 20px; display: inline-block;">
                    Ver Todos os Pedidos
                </a>
            @endif
        </div>
    </div>
</div>

@endsection

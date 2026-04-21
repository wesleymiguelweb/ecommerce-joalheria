@extends('layouts.admin')

@section('title', 'Painel ADM - Cadastrar Produto')

@section('breadcrumb', 'Cadastrar Produto')

@section('content')

<div class="admin-card">
            <h2>Cadastrar produtos</h2>
            <p class="subtitle">Adicione um novo produto ao catálogo</p>

            @if(session('success'))
                <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    <strong>✓ Sucesso!</strong> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    <strong>✗ Erro!</strong> {{ session('error') }}
                </div>
            @endif

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

            <nav class="admin-nav">
                <a href="{{ route('adm.produto') }}">Em estoque</a>
                <a href="{{ route('adm.usuarios') }}">Usuários</a>
                <a href="{{ route('adm.produto.criar') }}" class="active">Cadastrar Produtos</a>
            </nav>
            <form action="{{ route('adm.produto.store') }}" method="POST" enctype="multipart/form-data" class="admin-form product-form-layout">
                @csrf

                <div class="form-fields">
                    <div class="form-group">
                        <label for="name">Nome do Produto *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Ex: Anel de Ouro">
                        @error('name')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição *</label>
                        <textarea id="description" name="description" required placeholder="Descreva o produto...">{{ old('description') }}</textarea>
                        @error('description')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="text">Detalhes Adicionais</label>
                        <textarea id="text" name="text" placeholder="Informações extras...">{{ old('text') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Preço (R$) *</label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" required placeholder="Ex: 500.00">
                        @error('price')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="category">Categoria *</label>
                        <select id="category" name="category" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="feminino" {{ old('category') === 'feminino' ? 'selected' : '' }}>Feminino</option>
                            <option value="masculino" {{ old('category') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                        </select>
                        @error('category')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="brand">Marca</label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand') }}" placeholder="Ex: VERSACE, GUCCI, PRADA">
                    </div>
                    <div class="form-group">
                        <label for="color">Cor *</label>
                        <select id="color" name="color" required onchange="toggleNewColorInput(this)">
                            <option value="">Selecione uma cor</option>
                            @php
                                $existingColors = \App\Models\Product::select('color')->distinct()->whereNotNull('color')->pluck('color')->toArray();
                            @endphp
                            @foreach($existingColors as $color)
                                <option value="{{ $color }}" {{ old('color') === $color ? 'selected' : '' }}>{{ ucfirst($color) }}</option>
                            @endforeach
                            <option value="__nova__" {{ old('color') === '__nova__' ? 'selected' : '' }}>+ Adicionar Nova Cor</option>
                        </select>
                        <input type="text" id="new_color" name="new_color" value="{{ old('new_color') }}"
                               placeholder="Digite o nome da nova cor"
                               style="display: {{ old('color') === '__nova__' ? 'block' : 'none' }}; margin-top: 10px;">
                        @error('color')<span class="error-text">{{ $message }}</span>@enderror
                        @error('new_color')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="stock">Quantidade em Estoque *</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock') }}" required placeholder="Ex: 10" min="0">
                        @error('stock')<span class="error-text">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="min_stock">Estoque Mínimo (Baixo saldo)</label>
                        <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock') }}" placeholder="Ex: 5" min="0">
                        <small class="form-help-text">Quando o estoque for menor ou igual a este valor, o produto aparece como baixo saldo.</small>
                        @error('min_stock')<span class="error-text">{{ $message }}</span>@enderror
                    </div>

                </div>

                <div class="image-upload-area">
                     <div class="image-placeholder" id="image-preview">
                         <i class="fas fa-image fa-3x"></i>
                         <p class="form-help-text">As imagens serão geradas automaticamente com base no tipo de produto</p>
                         <img id="preview-img" style="display: none; max-width: 100%; max-height: 200px; border-radius: 8px; margin-top: 10px;" alt="Preview">
                     </div>
                     <label for="product_image" class="btn btn-dark">Carregar Imagem (Opcional)</label>
                     <input type="file" id="product_image" name="image" accept="image/*" style="display: none;" onchange="previewImage(event)">
                     @error('image')<span class="error-text" style="color: red; font-size: 0.9em; display: block; margin-top: 5px;">{{ $message }}</span>@enderror
                </div>

                <div class="form-actions">
                     <button type="submit" class="btn btn-primary">Salvar Produto</button>
                     <a href="{{ route('adm.produto') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

@endsection

@section('extra-scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview-img');
    const icon = document.querySelector('.image-placeholder i');
    const text = document.querySelector('.image-placeholder .form-help-text');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            icon.style.display = 'none';
            text.textContent = 'Imagem selecionada: ' + file.name;
        }
        reader.readAsDataURL(file);
    }
}

function toggleNewColorInput(select) {
    const newColorInput = document.getElementById('new_color');
    if (select.value === '__nova__') {
        newColorInput.style.display = 'block';
        newColorInput.required = true;
    } else {
        newColorInput.style.display = 'none';
        newColorInput.required = false;
        newColorInput.value = '';
    }
}
</script>
@endsection

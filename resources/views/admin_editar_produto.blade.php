@extends('layouts.admin')

@section('title', 'Painel ADM - Editar Produto')

@section('breadcrumb', 'Editar Produto')

@section('content')

<div class="admin-card">
            <h2>Editar Produto</h2>
            <p class="subtitle">Atualize as informações do produto: <strong>{{ $product->name }}</strong></p>

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

            <form action="{{ route('adm.produto.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="admin-form product-form-layout">
                @csrf
                @method('PUT')

                <div class="form-fields">
                    <div class="form-group">
                        <label for="name">Nome do Produto *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required placeholder="Ex: Anel de Ouro">
                        @error('name')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição *</label>
                        <textarea id="description" name="description" required placeholder="Descreva o produto...">{{ old('description', $product->description) }}</textarea>
                        @error('description')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="text">Detalhes Adicionais</label>
                        <textarea id="text" name="text" placeholder="Informações extras...">{{ old('text', $product->text) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Preço (R$) *</label>
                        <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" required placeholder="Ex: 500.00">
                        @error('price')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="category">Categoria *</label>
                        <select id="category" name="category" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="feminino" {{ old('category', $product->category) === 'feminino' ? 'selected' : '' }}>Feminino</option>
                            <option value="masculino" {{ old('category', $product->category) === 'masculino' ? 'selected' : '' }}>Masculino</option>
                        </select>
                        @error('category')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="brand">Marca</label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="Ex: VERSACE, GUCCI, PRADA">
                    </div>
                    <div class="form-group">
                        <label for="color">Cor *</label>
                        <select id="color" name="color" required onchange="toggleNewColorInputEdit(this)">
                            <option value="">Selecione uma cor</option>
                            @php
                                $existingColors = \App\Models\Product::select('color')->distinct()->whereNotNull('color')->pluck('color')->toArray();
                            @endphp
                            @foreach($existingColors as $color)
                                <option value="{{ $color }}" {{ old('color', $product->color) === $color ? 'selected' : '' }}>{{ ucfirst($color) }}</option>
                            @endforeach
                            <option value="__nova__" {{ old('color') === '__nova__' ? 'selected' : '' }}>+ Adicionar Nova Cor</option>
                        </select>
                        <input type="text" id="new_color_edit" name="new_color" value="{{ old('new_color') }}"
                               placeholder="Digite o nome da nova cor"
                               style="display: {{ old('color') === '__nova__' ? 'block' : 'none' }}; margin-top: 10px;">
                        @error('color')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
                        @error('new_color')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="stock">Quantidade em Estoque *</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required placeholder="Ex: 10" min="0">
                        @error('stock')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="min_stock">Estoque Mínimo (Baixo saldo)</label>
                        <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" placeholder="Ex: 5" min="0">
                        <small style="color: #666; display: block; margin-top: 0.25rem;">Quando o estoque for menor ou igual a este valor, o produto aparece como baixo saldo.</small>
                        @error('min_stock')<span style="color: #d32f2f; font-size: 0.9em;">{{ $message }}</span>@enderror
                    </div>

                </div>

                <div class="image-upload-area">
                     <div class="image-placeholder" id="image-preview">
                         @if($product->image)
                             <img id="preview-img" src="{{ asset('img/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                         @else
                             <i class="fas fa-image fa-3x"></i>
                             <p class="form-help-text" style="margin-top: 10px;">Clique para escolher uma imagem</p>
                             <img id="preview-img" style="display: none; max-width: 100%; max-height: 200px; border-radius: 8px; margin-top: 10px;" alt="Preview">
                         @endif
                     </div>
                     <label for="product_image" class="btn btn-dark">Carregar Imagem (Opcional)</label>
                     <input type="file" id="product_image" name="image" accept="image/*" style="display: none;" onchange="previewImage(event)">
                     @error('image')<span style="color: #d32f2f; font-size: 0.9em; display: block; margin-top: 5px;">{{ $message }}</span>@enderror
                </div>

                <div class="form-actions">
                     <button type="submit" class="btn btn-primary">Atualizar Produto</button>
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
        const placeholder = document.querySelector('.image-placeholder i');
        const placeholderText = document.querySelector('.form-help-text');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';

                // Hide placeholder icon and text if they exist
                if (placeholder) placeholder.style.display = 'none';
                if (placeholderText) placeholderText.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }

    function toggleNewColorInputEdit(select) {
        const newColorInput = document.getElementById('new_color_edit');
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

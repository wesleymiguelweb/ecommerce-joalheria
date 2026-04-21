@extends('layouts.admin')

@section('title', 'Painel ADM - Editar Cupom')

@section('breadcrumb', 'Editar Cupom')

@section('content')
<div class="admin-card">
    <h2>Editar Cupom</h2>
    <p class="subtitle">Atualize as informações do cupom: <strong>{{ $coupon->code }}</strong></p>

    @if($errors->any())
        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            <strong>Erro ao validar formulário:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('adm.coupons.update', $coupon->id) }}" method="POST" class="admin-form">
        @csrf
        @method('PUT')

        <div class="form-fields" style="max-width: 700px;">
            <div class="form-group">
                <label for="code">Código do Cupom *</label>
                <input type="text" id="code" name="code" value="{{ old('code', $coupon->code) }}" required placeholder="Ex: PRIMEIRACOMPRA" style="text-transform: uppercase;">
                <small style="color: #666; display: block; margin-top: 0.25rem;">Use letras maiúsculas sem espaços</small>
                @error('code')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="type">Tipo de Desconto *</label>
                <select id="type" name="type" required onchange="updateValueLabel(this.value)">
                    <option value="">Selecione o tipo</option>
                    <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentual (%)</option>
                    <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Valor Fixo (R$)</option>
                </select>
                @error('type')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="value" id="value-label">Valor do Desconto *</label>
                <input type="number" id="value" name="value" value="{{ old('value', $coupon->value) }}" step="0.01" min="0" required placeholder="Ex: 10">
                <small id="value-help" style="color: #666; display: block; margin-top: 0.25rem;">Selecione o tipo de desconto primeiro</small>
                @error('value')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="min_purchase">Compra Mínima (R$)</label>
                <input type="number" id="min_purchase" name="min_purchase" value="{{ old('min_purchase', $coupon->min_purchase) }}" step="0.01" min="0" placeholder="Ex: 100.00">
                <small style="color: #666; display: block; margin-top: 0.25rem;">Deixe vazio para sem mínimo</small>
                @error('min_purchase')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="usage_limit">Limite de Uso</label>
                <input type="number" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1" placeholder="Ex: 100">
                <small style="color: #666; display: block; margin-top: 0.25rem;">Deixe vazio para uso ilimitado</small>
                @error('usage_limit')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="valid_from">Válido A Partir De</label>
                <input type="date" id="valid_from" name="valid_from" value="{{ old('valid_from', $coupon->valid_from ? \Carbon\Carbon::parse($coupon->valid_from)->format('Y-m-d') : '') }}">
                <small style="color: #666; display: block; margin-top: 0.25rem;">Deixe vazio para iniciar imediatamente</small>
                @error('valid_from')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="valid_until">Válido Até</label>
                <input type="date" id="valid_until" name="valid_until" value="{{ old('valid_until', $coupon->valid_until ? \Carbon\Carbon::parse($coupon->valid_until)->format('Y-m-d') : '') }}">
                <small style="color: #666; display: block; margin-top: 0.25rem;">Deixe vazio para sem expiração</small>
                @error('valid_until')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" id="active" name="active" value="1" {{ old('active', $coupon->active) ? 'checked' : '' }}>
                    <span>Cupom ativo</span>
                </label>
                <small style="color: #666; display: block; margin-top: 0.25rem;">Apenas cupons ativos podem ser utilizados</small>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('adm.coupons') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </form>

    <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #ddd;">
        <h3 style="color: #666; font-size: 0.9em; margin-bottom: 0.5rem;">ESTATÍSTICAS DO CUPOM</h3>
        <p style="color: #999; font-size: 0.85em;">Total de usos: {{ $coupon->usage_count ?? 0 }}</p>
        <p style="color: #999; font-size: 0.85em;">Criado em: {{ $coupon->created_at->format('d/m/Y H:i') }}</p>
        <p style="color: #999; font-size: 0.85em;">Última atualização: {{ $coupon->updated_at->format('d/m/Y H:i') }}</p>
    </div>
</div>

@section('extra-scripts')
<script>
function updateValueLabel(type) {
    const label = document.getElementById('value-label');
    const help = document.getElementById('value-help');
    const input = document.getElementById('value');

    if (type === 'percentage') {
        label.textContent = 'Percentual de Desconto (%) *';
        help.textContent = 'Digite o percentual de desconto (ex: 10 para 10%)';
        input.placeholder = 'Ex: 10';
        input.max = '100';
    } else if (type === 'fixed') {
        label.textContent = 'Valor Fixo de Desconto (R$) *';
        help.textContent = 'Digite o valor fixo de desconto em reais';
        input.placeholder = 'Ex: 50.00';
        input.removeAttribute('max');
    } else {
        label.textContent = 'Valor do Desconto *';
        help.textContent = 'Selecione o tipo de desconto primeiro';
        input.placeholder = 'Ex: 10';
    }
}

// Converte para uppercase automaticamente
document.getElementById('code').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Inicializar label com valor atual
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    if (typeSelect.value) {
        updateValueLabel(typeSelect.value);
    }
});
</script>
@endsection

@endsection

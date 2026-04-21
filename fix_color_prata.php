<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Corrigindo cores dos produtos com 'prata' no nome ===\n\n";

// Buscar produtos com "prata" no nome
$products = DB::table('products')
    ->where(function($query) {
        $query->where('name', 'like', '%prata%')
              ->orWhere('name', 'like', '%Prata%');
    })
    ->get(['id', 'name', 'color']);

if ($products->isEmpty()) {
    echo "Nenhum produto encontrado com 'prata' no nome.\n";
    exit;
}

echo "Produtos encontrados:\n";
echo str_repeat("-", 80) . "\n";
printf("%-5s | %-50s | %-15s\n", "ID", "Nome", "Cor Atual");
echo str_repeat("-", 80) . "\n";

foreach ($products as $product) {
    printf("%-5d | %-50s | %-15s\n",
        $product->id,
        substr($product->name, 0, 50),
        $product->color ?? 'NULL'
    );
}

echo "\n";

// Atualizar produtos que não têm cor "prata"
$needUpdate = $products->filter(function($product) {
    return $product->color !== 'prata';
});

if ($needUpdate->isEmpty()) {
    echo "Todos os produtos já estão com a cor 'prata' correta!\n";
    exit;
}

echo "Produtos que serão atualizados para cor 'prata': {$needUpdate->count()}\n";
echo "Deseja continuar? (pressione Enter para confirmar ou Ctrl+C para cancelar)\n";
// Comentar linha abaixo para execução automática
// fgets(STDIN);

$updated = 0;
foreach ($needUpdate as $product) {
    DB::table('products')
        ->where('id', $product->id)
        ->update(['color' => 'prata']);

    $updated++;
    echo "✓ Produto #{$product->id} - '{$product->name}' atualizado para cor 'prata'\n";
}

echo "\n=== Concluído! ===\n";
echo "Total de produtos atualizados: {$updated}\n";

// Verificar resultado
echo "\nVerificando resultado final:\n";
echo str_repeat("-", 80) . "\n";
printf("%-5s | %-50s | %-15s\n", "ID", "Nome", "Cor Atual");
echo str_repeat("-", 80) . "\n";

$updatedProducts = DB::table('products')
    ->where(function($query) {
        $query->where('name', 'like', '%prata%')
              ->orWhere('name', 'like', '%Prata%');
    })
    ->get(['id', 'name', 'color']);

foreach ($updatedProducts as $product) {
    printf("%-5d | %-50s | %-15s\n",
        $product->id,
        substr($product->name, 0, 50),
        $product->color ?? 'NULL'
    );
}

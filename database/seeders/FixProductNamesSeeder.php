<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class FixProductNamesSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $counter = 0;

        foreach ($products as $product) {
            // Pular se o nome já está OK
            if (strlen($product->name) > 15 && !preg_match('/\b[A-F]{1,3}\b/', $product->name)) {
                continue;
            }

            // Determinar tipo baseado na imagem
            $image = strtolower($product->image);
            $name = $this->generateNameFromImage($image, $product->brand, $product->category);

            $product->update([
                'name' => $name,
                'description' => $name,
                'text' => "Lindíssima peça de {$product->brand}, confeccionada com materiais de alta qualidade. Ideal para presentear ou usar no dia a dia.",
                'color' => 'prata',
            ]);

            $counter++;
        }

        $this->command->info("Total de produtos renomeados: {$counter}");
    }

    private function generateNameFromImage(string $image, string $brand, string $category): string
    {
        $category = ucfirst(strtolower($category));

        // Detectar tipo de produto
        if (str_contains($image, 'anel')) {
            $types = ['Anel Solitário', 'Anel de Compromisso', 'Anel Delicado', 'Anel Trabalhado', 'Anel Clássico'];
            $type = $types[crc32($image) % count($types)];
        } elseif (str_contains($image, 'colar')) {
            $types = ['Colar Delicado', 'Colar Pendente', 'Colar Clássico', 'Colar Moderno', 'Colar Elegante'];
            $type = $types[crc32($image) % count($types)];
        } elseif (str_contains($image, 'brinco')) {
            $types = ['Brinco Argola', 'Brinco Gota', 'Brinco Solitário', 'Brinco Clássico', 'Brinco Moderno'];
            $type = $types[crc32($image) % count($types)];
        } elseif (str_contains($image, 'pulseira')) {
            $types = ['Pulseira Delicada', 'Pulseira Cartier', 'Pulseira Grumet', 'Pulseira Clássica', 'Pulseira Moderna'];
            $type = $types[crc32($image) % count($types)];
        } elseif (str_contains($image, 'bracelete')) {
            $types = ['Bracelete Aberto', 'Bracelete Liso', 'Bracelete Trabalhado', 'Bracelete Clássico', 'Bracelete Moderno'];
            $type = $types[crc32($image) % count($types)];
        } elseif (str_contains($image, 'corrente')) {
            $types = ['Corrente Veneziana', 'Corrente Cartier', 'Corrente Grumet', 'Corrente Lacraia', 'Corrente Pipoca'];
            $type = $types[crc32($image) % count($types)];
        } else {
            $types = ['Joia Elegante', 'Joia Clássica', 'Joia Moderna', 'Joia Delicada', 'Joia Sofisticada'];
            $type = $types[crc32($image) % count($types)];
        }

        // Adicionar variações
        $materials = ['Prata 925', 'Ouro', 'Folheado', 'Premium'];
        $material = $materials[crc32($image . $brand) % count($materials)];

        return "{$brand} - {$type} {$material} {$category}";
    }
}

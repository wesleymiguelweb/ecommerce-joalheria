<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class ProductFromImagesSeeder extends Seeder
{
    public function run(): void
    {
        $basePath = public_path('img');
        if (!is_dir($basePath)) {
            $this->command?->warn('Pasta public/img não encontrada; seed ignorado.');
            return;
        }

        $brands = ['VERSACE', 'ZARA', 'GUCCI', 'PRADA', 'CALVIN KLEIN'];
        $basePrices = [149.90, 179.90, 199.90, 229.90, 249.90, 279.90, 299.90];

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
        $total = 0;

        /** @var SplFileInfo $file */
        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }

            $path = $file->getPathname();
            $relative = ltrim(str_replace($basePath, '', $path), DIRECTORY_SEPARATOR);
            $relativeWeb = str_replace(DIRECTORY_SEPARATOR, '/', $relative);

            $segments = explode('/', $relativeWeb);
            $mainCategory = $segments[0] ?? null;
            if (!in_array($mainCategory, ['Feminino', 'Masculino'], true)) {
                continue; // ignora outros diretórios
            }

            $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $name = $this->humanizeName($filename);

            $color = $this->detectColor($filename);
            $brand = $brands[crc32($relativeWeb) % count($brands)];
            $price = $basePrices[crc32($filename) % count($basePrices)];
            $stock = 20 + (crc32($filename) % 15); // 20..34
            $minStock = 5;

            Product::updateOrCreate(
                ['image' => $relativeWeb],
                [
                    'name' => $name,
                    'description' => $name,
                    'text' => $name,
                    'price' => $price,
                    'category' => $mainCategory,
                    'brand' => $brand,
                    'color' => $color,
                    'stock' => $stock,
                    'min_stock' => $minStock,
                ]
            );

            $total++;
        }

        $this->command?->info("Produtos criados/atualizados a partir de imagens: {$total}");
    }

    private function humanizeName(string $raw): string
    {
        // Remover extensão se houver
        $clean = preg_replace('/\.(jpg|jpeg|png|gif|webp)$/i', '', $raw);

        // Substituir underscores e hífens por espaços
        $clean = preg_replace('/[_-]+/', ' ', $clean);

        // Remover números e caracteres hexadecimais isolados
        $clean = preg_replace('/\b[0-9a-f]{1,2}\b/i', '', $clean ?? '');

        // Remover números em geral
        $clean = preg_replace('/\d+/', '', $clean ?? '');

        // Remover múltiplos espaços
        $clean = preg_replace('/\s+/', ' ', $clean ?? '');

        // Limpar espaços nas extremidades
        $clean = trim($clean ?? '');

        // Se ficou vazio, usar nome padrão
        if ($clean === '' || strlen($clean) < 3) {
            return 'Joia Elegance';
        }

        return ucwords(strtolower($clean));
    }

    private function detectColor(string $raw): string
    {
        $lower = strtolower($raw);
        if (str_contains($lower, 'ouro')) {
            return 'ouro';
        }
        if (str_contains($lower, 'prata') || str_contains($lower, 'silver')) {
            return 'prata';
        }
        if (str_contains($lower, 'preto') || str_contains($lower, 'black')) {
            return 'preto';
        }
        return 'neutro';
    }
}

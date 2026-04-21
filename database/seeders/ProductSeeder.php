<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Colar Elegance Ouro',
                'description' => 'Colar delicado banhado a ouro para compor looks sofisticados.',
                'text' => 'Design minimalista com banho dourado e fecho seguro.',
                'price' => 499.90,
                'category' => 'feminino',
                'brand' => 'GUCCI',
                'color' => 'ouro',
                'image' => 'Feminino/Colares Femininos/040039-000c2b75a0d2513baf17588152003518-1024-1024.webp',
                'stock' => 25,
                'min_stock' => 5,
            ],
            [
                'name' => 'Brinco Prata Shine',
                'description' => 'Brincos femininos com acabamento em prata polida.',
                'text' => 'Ideal para uso diário, leve e hipoalergênico.',
                'price' => 189.90,
                'category' => 'feminino',
                'brand' => 'PRADA',
                'color' => 'prata',
                'image' => 'Feminino/Brincos Femininos/030028-ad56ead040bde0509c17611335868546-1024-1024.webp',
                'stock' => 40,
                'min_stock' => 8,
            ],
            [
                'name' => 'Bracelete Ouro Minimal',
                'description' => 'Bracelete feminino ajustável com banho de ouro.',
                'text' => 'Acabamento liso e confortável para uso prolongado.',
                'price' => 259.90,
                'category' => 'feminino',
                'brand' => 'VERSACE',
                'color' => 'ouro',
                'image' => 'Feminino/Braceletes Femininos/020001-5b8e5767724b99cd4717579675323444-1024-1024.webp',
                'stock' => 30,
                'min_stock' => 6,
            ],
            [
                'name' => 'Aliança Classic Prata',
                'description' => 'Aliança feminina clássica em prata com brilho espelhado.',
                'text' => 'Modelo atemporal para celebrações e uso diário.',
                'price' => 149.90,
                'category' => 'feminino',
                'brand' => 'ZARA',
                'color' => 'prata',
                'image' => 'Feminino/Alianças Feminas/011088-5e36ca9cdde85a509917617696870135-1024-1024.webp',
                'stock' => 50,
                'min_stock' => 10,
            ],
            [
                'name' => 'Corrente Delicada Prata',
                'description' => 'Corrente feminina leve e versátil para combinar com pingentes.',
                'text' => 'Fecho seguro e banho de prata resistente à oxidação.',
                'price' => 129.90,
                'category' => 'feminino',
                'brand' => 'CALVIN KLEIN',
                'color' => 'prata',
                'image' => 'Feminino/Correntes Femininas/050067-541b4e1a7bf741519a17597719807957-1024-1024.webp',
                'stock' => 60,
                'min_stock' => 12,
            ],
            [
                'name' => 'Pulseira Cartier Ouro',
                'description' => 'Pulseira estilo Cartier com banho dourado e elos alongados.',
                'text' => 'Peça coringa para compor camadas com outros acessórios.',
                'price' => 279.90,
                'category' => 'feminino',
                'brand' => 'GUCCI',
                'color' => 'ouro',
                'image' => 'Feminino/Braceletes Femininos/bracelete_bali_cartier_22047_1_28dbde5bd3b4e0a7704913d2fd5c170e-c9263be1fb1c4b56d317515741349904-1024-1024.webp',
                'stock' => 28,
                'min_stock' => 6,
            ],
            [
                'name' => 'Corrente Cartier Prata',
                'description' => 'Corrente masculina estilo cartier com elos marcantes.',
                'text' => 'Construção robusta e banho prateado de alta durabilidade.',
                'price' => 219.90,
                'category' => 'masculino',
                'brand' => 'CALVIN KLEIN',
                'color' => 'prata',
                'image' => 'Masculino/Correntes Masculinas/corrente_bolinha_c_bola_oval_45_cm_22557_1_e3921f59fa5072816505400ecbe35094-da2a93777fd9f35eb517518246212134-1024-1024.webp',
                'stock' => 35,
                'min_stock' => 7,
            ],
            [
                'name' => 'Pulseira Couro Preto',
                'description' => 'Pulseira masculina em couro preto com fecho seguro.',
                'text' => 'Combina com jeans e alfaiataria para um visual urbano.',
                'price' => 149.90,
                'category' => 'masculino',
                'brand' => 'PRADA',
                'color' => 'preto',
                'image' => 'Masculino/Pulseira Masculina/pulseira_couro_2_linhas_larga_fina_preta_4191_1_6ad064a62c8db7c3a89a27fe01c862d8-87cf11d3c77687d40317522444420825-1024-1024.webp',
                'stock' => 45,
                'min_stock' => 9,
            ],
            [
                'name' => 'Aliança Fosca Prata',
                'description' => 'Aliança masculina com acabamento fosco e bordas polidas.',
                'text' => 'Conforto interno anatômico e visual moderno.',
                'price' => 169.90,
                'category' => 'masculino',
                'brand' => 'ZARA',
                'color' => 'prata',
                'image' => 'Masculino/Alianças Masculinas/011170-82fbf90e66aafef47217618346884958-1024-1024.webp',
                'stock' => 55,
                'min_stock' => 10,
            ],
            [
                'name' => 'Relógio Chrono Steel',
                'description' => 'Relógio masculino com pulseira de aço e mostrador escuro.',
                'text' => 'Função cronógrafo e resistência à água para uso diário.',
                'price' => 899.90,
                'category' => 'masculino',
                'brand' => 'GUCCI',
                'color' => 'prata',
                'image' => 'Masculino/Relógios Masculinos/NIM021016_1.jpg',
                'stock' => 15,
                'min_stock' => 3,
            ],
            [
                'name' => 'Pulseira Grumet Prata',
                'description' => 'Pulseira grumet masculina com elos largos e visual marcante.',
                'text' => 'Modelo clássico para complementar produções casuais.',
                'price' => 199.90,
                'category' => 'masculino',
                'brand' => 'VERSACE',
                'color' => 'prata',
                'image' => 'Masculino/Pulseira Masculina/pulseira_grumet_5_5_mm_3999_1_5c75d4fc59baba6d7b549549c0e8a1fd-379889e2049cd14a6b17518435890405-1024-1024.webp',
                'stock' => 32,
                'min_stock' => 6,
            ],
            [
                'name' => 'Corrente Corda Ouro',
                'description' => 'Corrente masculina estilo corda com banho dourado.',
                'text' => 'Versátil para uso solo ou com pingente.',
                'price' => 249.90,
                'category' => 'masculino',
                'brand' => 'GUCCI',
                'color' => 'ouro',
                'image' => 'Masculino/Correntes Masculinas/corrente_cabelo_de_anjo_40_cm_3031_1_d4ac222559b7143d88b2f89e2f2bb3e4-1fab73392f8b35bb4d17518233038000-1024-1024.webp',
                'stock' => 26,
                'min_stock' => 5,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}

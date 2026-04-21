<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuários para as avaliações se não existirem
        $users = [
            ['name' => 'Ana Garcia', 'email' => 'ana.garcia@example.com'],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@example.com'],
            ['name' => 'Sofia Lima', 'email' => 'sofia.lima@example.com'],
            ['name' => 'João Silva', 'email' => 'joao.silva@example.com'],
            ['name' => 'Beatriz Costa', 'email' => 'beatriz.costa@example.com'],
            ['name' => 'Lucas Oliveira', 'email' => 'lucas.oliveira@example.com'],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password123'),
                    'is_admin' => false,
                ]
            );
            $createdUsers[] = $user;
        }

        // Buscar produtos aleatórios
        $products = Product::inRandomOrder()->limit(6)->get();

        if ($products->count() < 6) {
            $this->command->warn('Não há produtos suficientes no banco. Execute ProductSeeder primeiro.');
            return;
        }

        // Avaliações
        $reviews = [
            [
                'rating' => 5,
                'comment' => 'Entrega super rápida e o produto é de excelente qualidade. Superou minhas expectativas!',
            ],
            [
                'rating' => 5,
                'comment' => 'Atendimento impecável, voltarei a comprar! A joia é linda e muito bem acabada.',
            ],
            [
                'rating' => 4,
                'comment' => 'Joias maravilhosas. Recomendo a todos. Apenas a entrega demorou um pouco mais que o esperado.',
            ],
            [
                'rating' => 5,
                'comment' => 'Produto de qualidade excepcional! Chegou muito bem embalado e exatamente como na foto.',
            ],
            [
                'rating' => 5,
                'comment' => 'Adorei minha compra! A joia é ainda mais bonita pessoalmente. Muito satisfeita!',
            ],
            [
                'rating' => 4,
                'comment' => 'Ótimo custo-benefício. Material de qualidade e design elegante. Recomendo!',
            ],
        ];

        // Criar reviews
        foreach ($reviews as $index => $reviewData) {
            if (isset($createdUsers[$index]) && isset($products[$index])) {
                Review::updateOrCreate(
                    [
                        'user_id' => $createdUsers[$index]->id,
                        'product_id' => $products[$index]->id,
                    ],
                    [
                        'rating' => $reviewData['rating'],
                        'comment' => $reviewData['comment'],
                        'approved' => true, // Aprovadas para aparecer no site
                        'order_id' => null,
                    ]
                );
            }
        }

        $this->command->info('Reviews criadas com sucesso!');
    }
}

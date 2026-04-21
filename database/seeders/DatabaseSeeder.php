<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin padrão (idempotente) e catálogo base + ingestão automática das imagens locais.
        $this->call([
            AdminUserSeeder::class,
            ProductSeeder::class,
            ProductFromImagesSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}

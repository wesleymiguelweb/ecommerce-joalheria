<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Atualizar todos os produtos com cor 'neutro' para 'prata'
        DB::table('products')
            ->where('color', 'neutro')
            ->update(['color' => 'prata']);

        // Atualizar todos os produtos com cor 'ouro' para 'prata'
        DB::table('products')
            ->where('color', 'ouro')
            ->update(['color' => 'prata']);

        // Atualizar produtos com cor NULL que tenham 'prata' no nome
        DB::table('products')
            ->whereNull('color')
            ->where(function($query) {
                $query->where('name', 'like', '%prata%')
                      ->orWhere('name', 'like', '%Prata%');
            })
            ->update(['color' => 'prata']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para neutro se necessário
        DB::table('products')
            ->where('color', 'prata')
            ->update(['color' => 'neutro']);
    }
};

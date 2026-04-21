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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'color')) {
                $table->string('color')->nullable()->after('category');
            }
        });

        // Atualiza os produtos existentes com cores baseadas no nome
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $nameLower = strtolower($product->name);
            $color = 'neutro'; // valor padrão
            
            if (str_contains($nameLower, 'prata')) {
                $color = 'prata';
            } elseif (str_contains($nameLower, 'ouro')) {
                $color = 'ouro';
            }
            
            DB::table('products')
                ->where('id', $product->id)
                ->update(['color' => $color]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};

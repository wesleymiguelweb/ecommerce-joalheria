<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Criar índices de performance para MySQL em produção
     * Executar APÓS todas as outras migrations
     */
    public function up(): void
    {
        // Índices de busca e filtro de produtos
        DB::statement("CREATE INDEX idx_products_category_stock ON products(category, stock)");
        DB::statement("CREATE INDEX idx_products_brand ON products(brand)");
        DB::statement("CREATE INDEX idx_products_price ON products(price)");

        // Índice de busca por texto (FULLTEXT)
        DB::statement("CREATE FULLTEXT INDEX ft_products_search ON products(name, description)");

        // Índices de usuários
        DB::statement("CREATE INDEX idx_users_email ON users(email)");
        DB::statement("CREATE UNIQUE INDEX idx_users_email_unique ON users(email)");

        // Índices de pedidos (relacionamentos)
        DB::statement("CREATE INDEX idx_orders_user_id ON orders(user_id)");
        DB::statement("CREATE INDEX idx_orders_status ON orders(status)");
        DB::statement("CREATE INDEX idx_orders_created_at ON orders(created_at)");
        DB::statement("CREATE INDEX idx_orders_number ON orders(order_number)");

        // Índices compostos para queries comuns
        DB::statement("CREATE INDEX idx_orders_user_date ON orders(user_id, created_at DESC)");
        DB::statement("CREATE INDEX idx_orders_status_date ON orders(status, created_at DESC)");

        // Índices de itens do pedido
        DB::statement("CREATE INDEX idx_order_items_order_id ON order_items(order_id)");
        DB::statement("CREATE INDEX idx_order_items_product_id ON order_items(product_id)");

        // Índices de coupons (validações)
        DB::statement("CREATE INDEX idx_coupons_code ON coupons(code)");
        DB::statement("CREATE INDEX idx_coupons_active_dates ON coupons(active, valid_from, valid_until)");

        // Índices de avaliações
        DB::statement("CREATE INDEX idx_reviews_product_id ON reviews(product_id)");
        DB::statement("CREATE INDEX idx_reviews_user_id ON reviews(user_id)");
        DB::statement("CREATE INDEX idx_reviews_rating ON reviews(rating)");

        // Índices de tabelas padrão Laravel
        DB::statement("CREATE INDEX idx_sessions_user_id ON sessions(user_id)");
        DB::statement("CREATE INDEX idx_sessions_last_activity ON sessions(last_activity)");
        DB::statement("CREATE INDEX idx_jobs_queue ON jobs(queue)");
    }

    /**
     * Reverter indices (cuidado em produção!)
     */
    public function down(): void
    {
        // Remover índices de produtos
        DB::statement("DROP INDEX idx_products_category_stock ON products");
        DB::statement("DROP INDEX idx_products_brand ON products");
        DB::statement("DROP INDEX idx_products_price ON products");
        DB::statement("DROP INDEX ft_products_search ON products");

        // Remover índices de usuários
        DB::statement("DROP INDEX idx_users_email ON users");
        DB::statement("DROP INDEX idx_users_email_unique ON users");

        // Remover índices de pedidos
        DB::statement("DROP INDEX idx_orders_user_id ON orders");
        DB::statement("DROP INDEX idx_orders_status ON orders");
        DB::statement("DROP INDEX idx_orders_created_at ON orders");
        DB::statement("DROP INDEX idx_orders_number ON orders");
        DB::statement("DROP INDEX idx_orders_user_date ON orders");
        DB::statement("DROP INDEX idx_orders_status_date ON orders");

        // Remover índices de itens
        DB::statement("DROP INDEX idx_order_items_order_id ON order_items");
        DB::statement("DROP INDEX idx_order_items_product_id ON order_items");

        // Remover índices de coupons
        DB::statement("DROP INDEX idx_coupons_code ON coupons");
        DB::statement("DROP INDEX idx_coupons_active_dates ON coupons");

        // Remover índices de avaliações
        DB::statement("DROP INDEX idx_reviews_product_id ON reviews");
        DB::statement("DROP INDEX idx_reviews_user_id ON reviews");
        DB::statement("DROP INDEX idx_reviews_rating ON reviews");

        // Remover índices padrão
        DB::statement("DROP INDEX idx_sessions_user_id ON sessions");
        DB::statement("DROP INDEX idx_sessions_last_activity ON sessions");
        DB::statement("DROP INDEX idx_jobs_queue ON jobs");
    }
};

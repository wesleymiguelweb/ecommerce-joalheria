<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartilhar variável global para verificar se há múltiplas cores
        view()->composer('*', function ($view) {
            $distinctColors = \App\Models\Product::select('color')
                ->distinct()
                ->whereNotNull('color')
                ->count();

            $view->with('hasMultipleColors', $distinctColors > 1);
        });
    }
}

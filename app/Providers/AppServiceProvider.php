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
        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

// DESATIVADO PARA RENDER (sem MySQL): Compartilhar variável global para verificar se há múltiplas cores
        // $distinctColors = \App\Models\Product::select('color')
        //     ->distinct()
        //     ->whereNotNull('color')
        //     ->count();
        
        $hasMultipleColors = false; // Default para Render/local sem DB
        
        view()->composer('*', function ($view) use ($hasMultipleColors) {
            $view->with('hasMultipleColors', $hasMultipleColors);
        });
    }
}

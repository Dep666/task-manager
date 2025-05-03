<?php

namespace App\Providers;

use Barryvdh\DomPDF\Facade\Pdf;
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
        // Зарегистрируем PDF фасад
        $this->app->bind('PDF', function() {
            return new Pdf;
        });
    }
} 
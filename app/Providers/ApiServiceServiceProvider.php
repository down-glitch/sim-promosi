<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SekolahIndonesiaApiService;

class ApiServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Daftarkan SekolahIndonesiaApiService sebagai singleton
        $this->app->singleton(SekolahIndonesiaApiService::class, function ($app) {
            return new SekolahIndonesiaApiService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SpesaExtra;
use App\Observers\SpesaExtraObserver;

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
        // Observer registration per SpesaExtra
        SpesaExtra::observe(SpesaExtraObserver::class);
    }
}

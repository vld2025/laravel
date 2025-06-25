<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SpesaExtra;
use App\Models\Report;
use App\Observers\SpesaExtraObserver;
use App\Observers\ReportObserver;

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
        
        // Observer registration per Report
        Report::observe(ReportObserver::class);
    }
}

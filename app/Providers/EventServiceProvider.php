
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        \App\Models\Report::observe(\App\Observers\ReportObserver::class);
        \App\Models\SpesaExtra::observe(\App\Observers\SpesaExtraObserver::class);
        \App\Models\Documento::observe(\App\Observers\DocumentoObserver::class);
    }

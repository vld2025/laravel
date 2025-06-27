<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

class HeaderButtonProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::topbar.end',
            fn (): string => auth()->user()?->canViewAllData() ? 
                '<a href="/user" style="margin-left: 16px; background: #10b981; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">📱 Mobile</a>' : ''
        );
    }
}

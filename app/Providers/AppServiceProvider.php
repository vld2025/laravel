<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Pulsante cambio vista INTEGRATO nella topbar
        FilamentView::registerRenderHook(
            'panels::topbar.end',
            function (): string {
                if (!auth()->check() || !auth()->user()->canViewAllData()) {
                    return '';
                }

                return '<div style="display: flex; align-items: center; gap: 12px; margin-right: 12px;">
                    <!-- PULSANTE VISTA MOBILE/DESKTOP -->
                    <a href="' . (request()->is('admin*') ? '/user' : '/admin') . '" 
                       style="display: flex; align-items: center; gap: 6px; padding: 6px 10px; 
                              background: rgba(173, 216, 230, 0.5); color: #1e40af; 
                              border-radius: 6px; text-decoration: none; font-weight: 600; 
                              font-size: 12px; border: 1px solid rgba(173, 216, 230, 0.7); 
                              transition: all 0.2s; backdrop-filter: blur(4px);"
                       onmouseover="this.style.background=\'rgba(173, 216, 230, 0.7)\'"
                       onmouseout="this.style.background=\'rgba(173, 216, 230, 0.5)\'">
                        ' . (request()->is('admin*') ? '📱 Vista Mobile' : '🖥️ Vista Desktop') . '
                    </a>
                </div>';
            }
        );
    }
}

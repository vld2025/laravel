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
        // SOLO il pulsante cambio vista - NESSUN controllo mobile
        FilamentView::registerRenderHook(
            'panels::head.end',
            function (): string {
                if (!auth()->check() || !auth()->user()->canViewAllData()) {
                    return '';
                }
                
                return '<style>
                .vld-switch-btn {
                    position: fixed !important;
                    top: 15px !important;
                    right: 80px !important;
                    z-index: 9999 !important;
                    background: rgba(173, 216, 230, 0.5) !important;
                    color: #1e40af !important;
                    padding: 8px 12px !important;
                    border-radius: 6px !important;
                    text-decoration: none !important;
                    font-weight: 600 !important;
                    font-size: 13px !important;
                    border: 1px solid rgba(173, 216, 230, 0.7) !important;
                    transition: all 0.2s !important;
                    backdrop-filter: blur(4px) !important;
                }
                .vld-switch-btn:hover {
                    background: rgba(173, 216, 230, 0.7) !important;
                }
                </style>
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const topbar = document.querySelector(".fi-topbar");
                    if (topbar && !document.querySelector(".vld-switch-btn")) {
                        const btn = document.createElement("a");
                        btn.className = "vld-switch-btn";
                        
                        if (window.location.pathname.includes("/admin")) {
                            btn.href = "/user";
                            btn.innerHTML = "📱 Vista Mobile";
                        } else if (window.location.pathname.includes("/user")) {
                            btn.href = "/admin";
                            btn.innerHTML = "🖥️ Vista Desktop";
                        }
                        
                        if (btn.href) {
                            document.body.appendChild(btn);
                        }
                    }
                });
                </script>';
            }
        );
    }
}

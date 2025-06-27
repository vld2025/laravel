<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

class VldMobileProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): string => '
            <style>
            /* VLD Service Mobile - SOLO per mobile, conserva desktop */
            @media (max-width: 768px) {
                /* Header mobile - mantieni logo */
                .fi-topbar {
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    z-index: 100 !important;
                    background: white !important;
                    border-bottom: 1px solid #e5e7eb !important;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
                }
                
                /* Sidebar mobile collassabile */
                .fi-sidebar {
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    width: 280px !important;
                    height: 100vh !important;
                    transform: translateX(-100%) !important;
                    transition: transform 0.3s ease !important;
                    z-index: 90 !important;
                    /* Mantieni stili originali sidebar */
                }
                
                .fi-sidebar.mobile-open {
                    transform: translateX(0) !important;
                }
                
                /* Contenuto mobile */
                .fi-main-ctn {
                    padding-top: 70px !important;
                    margin-left: 0 !important;
                    width: 100% !important;
                }
                
                /* Hamburger personalizzato */
                .vld-mobile-menu {
                    position: fixed !important;
                    top: 15px !important;
                    left: 15px !important;
                    z-index: 101 !important;
                    background: white !important;
                    border: 1px solid #e5e7eb !important;
                    border-radius: 6px !important;
                    padding: 8px !important;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
                    cursor: pointer !important;
                }
                
                /* Nascondi hamburger originali solo su mobile */
                .fi-topbar-open-sidebar-btn,
                .fi-topbar-close-sidebar-btn {
                    display: none !important;
                }
                
                /* Touch improvements */
                .fi-input, .fi-select-input, .fi-textarea { 
                    min-height: 48px !important; 
                    font-size: 16px !important; 
                }
                .fi-btn { 
                    min-height: 48px !important; 
                    padding: 0.875rem 1rem !important; 
                }
            }
            
            /* Desktop - mantieni tutto originale */
            @media (min-width: 769px) {
                .vld-mobile-menu {
                    display: none !important;
                }
            }
            </style>
            
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (window.innerWidth <= 768) {
                    console.log("📱 VLD Mobile: Conservative mobile setup...");
                    
                    // Crea hamburger conservativo
                    function createMobileMenu() {
                        if (document.querySelector(".vld-mobile-menu")) return;
                        
                        const menuBtn = document.createElement("div");
                        menuBtn.className = "vld-mobile-menu";
                        menuBtn.innerHTML = `
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        `;
                        
                        document.body.appendChild(menuBtn);
                        
                        let isOpen = false;
                        const sidebar = document.querySelector(".fi-sidebar");
                        
                        menuBtn.addEventListener("click", function() {
                            if (!sidebar) return;
                            
                            isOpen = !isOpen;
                            if (isOpen) {
                                sidebar.classList.add("mobile-open");
                                menuBtn.innerHTML = `
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                `;
                            } else {
                                sidebar.classList.remove("mobile-open");
                                menuBtn.innerHTML = `
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                `;
                            }
                        });
                    }
                    
                    setTimeout(createMobileMenu, 200);
                    console.log("✅ VLD Mobile: Conservative setup complete");
                }
            });
            </script>
            '
        );
    }
}

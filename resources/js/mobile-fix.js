// VLD Service Mobile Layout Fix - JavaScript Only Approach
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 VLD Mobile Fix Loading...');
    
    function applyMobileFix() {
        if (window.innerWidth <= 768) {
            console.log('📱 Applying Mobile Layout Fix');
            
            // Force hide sidebar
            const sidebar = document.querySelector('.fi-sidebar, aside[class*="sidebar"]');
            if (sidebar) {
                sidebar.style.setProperty('display', 'none', 'important');
                console.log('✅ Sidebar hidden');
            }
            
            // Hide hamburger menu buttons
            const hamburgers = document.querySelectorAll('.fi-topbar-open-sidebar-btn, .fi-topbar-close-sidebar-btn, button[class*="sidebar"]');
            hamburgers.forEach(btn => {
                btn.style.setProperty('display', 'none', 'important');
            });
            if (hamburgers.length > 0) console.log('✅ Hamburger buttons hidden');
            
            // Expand main content to full width
            const mainContent = document.querySelector('.fi-main-ctn, .fi-main, main');
            if (mainContent) {
                mainContent.style.setProperty('margin-left', '0', 'important');
                mainContent.style.setProperty('width', '100%', 'important');
                mainContent.style.setProperty('max-width', '100%', 'important');
                console.log('✅ Main content expanded');
            }
            
            // Remove any left padding from layout containers
            const layouts = document.querySelectorAll('.fi-layout, .fi-body, body > div');
            layouts.forEach(layout => {
                layout.style.setProperty('padding-left', '0', 'important');
            });
            
            console.log('🎯 Mobile Fix Applied Successfully!');
        } else {
            console.log('💻 Desktop view - Mobile fix not applied');
        }
    }
    
    // Apply fix immediately
    applyMobileFix();
    
    // Apply fix on window resize
    window.addEventListener('resize', applyMobileFix);
    
    // Apply fix on route changes (for SPA behavior)
    let currentUrl = window.location.href;
    const urlObserver = new MutationObserver(() => {
        if (window.location.href !== currentUrl) {
            currentUrl = window.location.href;
            setTimeout(applyMobileFix, 200);
        }
    });
    urlObserver.observe(document.body, { childList: true, subtree: true });
    
    console.log('🚀 VLD Mobile Fix Initialized');
});

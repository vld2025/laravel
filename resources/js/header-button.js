document.addEventListener('DOMContentLoaded', function() {
    // Solo per admin/manager
    if (window.location.pathname.includes('/admin')) {
        // Aggiungi click listener all'header
        const topbar = document.querySelector('.fi-topbar');
        if (topbar) {
            topbar.addEventListener('click', function(e) {
                // Se clicchi nella zona del pulsante (destra dell'header)
                const rect = topbar.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const headerWidth = rect.width;
                
                // Se clicchi negli ultimi 150px a destra
                if (clickX > headerWidth - 150) {
                    window.location.href = '/user';
                }
            });
        }
    }
});

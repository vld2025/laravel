<style>
/* NASCONDI HEADER FILAMENT MA NON LE AZIONI */
.fi-topbar,
.fi-topbar-heading,
.fi-sidebar-header,
nav[class*="fi-topbar"],
.fi-navigation,
.fi-breadcrumbs,
.fi-layout-header,
.fi-topbar-start,
.fi-topbar-end {
    display: none !important;
}

/* NASCONDI HEADER SOLO DOVE NON SERVONO AZIONI */
.fi-header:not(.fi-page-header) {
    display: none !important;
}

/* MOSTRA SEMPRE LE AZIONI HEADER */
.fi-header-actions,
.fi-page-header-actions,
[data-slot="header-actions"] {
    display: flex !important;
    visibility: visible !important;
    position: relative !important;
    z-index: 100 !important;
}

/* NASCONDI SOLO I TITOLI */
.fi-simple-header-heading,
.fi-header-heading,
.fi-simple-layout .fi-simple-header .fi-simple-header-heading,
h1.fi-header-heading,
h1[class*="fi-"],
.fi-simple-header h1,
.fi-simple-main h1,
.fi-form h1,
.fi-card h1 {
    display: none !important;
}

/* ASSICURATI CHE IL CONTENUTO INIZI DALL'ALTO */
.fi-main,
.fi-layout,
.fi-page {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

.fi-main-ctn {
    padding-top: 0 !important;
}

/* FORZA L'INIZIO DEL CONTENUTO DALL'ALTO */
body {
    padding-top: 64px !important; /* Spazio per il nostro header fisso */
}

/* CENTRA MEGLIO IL FORM LOGIN */
.fi-simple-main {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 100vh !important;
    padding-top: 0 !important;
}

/* NASCONDI TUTTO CIÒ CHE CONTIENE "ACCEDI" */
*:contains("Accedi") {
    display: none !important;
}
</style>

<script>
// RIMUOVI LA SCRITTA "ACCEDI" CON JAVASCRIPT
document.addEventListener('DOMContentLoaded', function() {
    const elements = document.querySelectorAll('h1, h2, h3, .fi-header-heading, .fi-simple-header-heading');
    elements.forEach(function(element) {
        if (element.textContent.trim() === 'Accedi') {
            element.style.display = 'none';
        }
    });

    setTimeout(function() {
        const headings = document.querySelectorAll('h1, h2, h3');
        headings.forEach(function(heading) {
            if (heading.textContent.includes('Accedi')) {
                heading.style.display = 'none';
            }
        });
    }, 100);
});
</script>

<style>
/* Aggiungi pulsante nella toolbar */
.fi-ta-header-ctn .fi-ta-header-toolbar::before {
    content: '';
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #10b981;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    margin-right: 12px;
    transition: background-color 0.2s;
}

.fi-ta-header-ctn .fi-ta-header-toolbar::before:hover {
    background: #059669;
}

/* Responsive su mobile */
@media (max-width: 768px) {
    .fi-ta-header-ctn .fi-ta-header-toolbar::before {
        content: '+';
        padding: 6px 10px;
        font-size: 16px;
        font-weight: bold;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aggiungi funzionalità click al pulsante
    const toolbar = document.querySelector('.fi-ta-header-ctn .fi-ta-header-toolbar');
    if (toolbar) {
        toolbar.addEventListener('click', function(e) {
            if (e.target === toolbar && e.offsetX < 50) {
                window.location.href = '/user/documentos/create';
            }
        });
    }
});
</script>

<!-- PWA Meta Tags -->
<meta name="theme-color" content="#10b981">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="VLD Service">
<meta name="mobile-web-app-capable" content="yes">

<!-- PWA Icons - Favicon per tutte le dimensioni -->
<link rel="manifest" href="/manifest.json">
<link rel="apple-touch-icon" href="/images/favicon/f2.png">
<link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/f2.png">
<link rel="icon" type="image/png" sizes="192x192" href="/images/favicon/f2.png">
<link rel="icon" type="image/png" sizes="512x512" href="/images/favicon/f2.png">

<!-- Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/sw.js')
      .then(function(registration) {
        console.log('SW registered: ', registration);
      })
      .catch(function(registrationError) {
        console.log('SW registration failed: ', registrationError);
      });
  });
}
</script>

<!-- Auto-update PWA -->
<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.addEventListener('controllerchange', () => {
    window.location.reload();
  });
  // Controlla aggiornamenti ogni 30 secondi
  setInterval(() => {
    navigator.serviceWorker.getRegistration().then(reg => {
      if (reg) reg.update();
    });
  }, 30000);
}
</script>

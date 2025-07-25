<!-- FOOTER FISSO MOBILE - TRASPARENTE -->
<footer style="position: fixed; bottom: 16px; left: 8px; right: 8px; z-index: 50; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(229, 231, 235, 0.8); border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
    <div style="display: flex; gap: 8px; padding: 12px; height: 70px;">
        <!-- {{ __("ui.footer_expenses") }} -->
        <a href="{{ route('filament.user.resources.spesas.index') }}" 
           onclick="vibrate()" 
           style="flex: 1; display: flex; align-items: center; justify-content: center; 
                  background: {{ request()->routeIs('filament.user.resources.spesas.*') ? 'rgba(249, 115, 22, 0.8)' : 'rgba(107, 114, 128, 0.5)' }}; 
                  color: {{ request()->routeIs('filament.user.resources.spesas.*') ? 'white' : 'rgb(55, 65, 81)' }};
                  border: 1px solid {{ request()->routeIs('filament.user.resources.spesas.*') ? 'rgba(249, 115, 22, 0.6)' : 'rgba(209, 213, 219, 0.5)' }};
                  text-decoration: none; transition: all 0.3s ease; font-weight: 600; font-size: 14px; 
                  border-radius: 12px; backdrop-filter: blur(5px);">
            {{ __("ui.footer_expenses") }}
        </a>

        <!-- {{ __("ui.footer_reports") }} - PRINCIPALE! -->
        <a href="{{ route('filament.user.resources.reports.index') }}" 
           onclick="vibrate()" 
           style="flex: 1.4; display: flex; align-items: center; justify-content: center; 
                  background: {{ request()->routeIs('filament.user.resources.reports.*') ? 'rgba(59, 130, 246, 0.8)' : 'rgba(59, 130, 246, 0.6)' }}; 
                  color: white;
                  border: 1px solid {{ request()->routeIs('filament.user.resources.reports.*') ? 'rgba(37, 99, 235, 0.8)' : 'rgba(59, 130, 246, 0.6)' }};
                  text-decoration: none; transition: all 0.3s ease; font-weight: 700; font-size: 16px; 
                  border-radius: 14px; backdrop-filter: blur(5px); 
                  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);">
            {{ __("ui.footer_reports") }}
        </a>

        <!-- {{ __("ui.footer_expenses") }} {{ __("ui.footer_extra") }} -->
        <a href="{{ route('filament.user.resources.spesa-extras.index') }}" 
           onclick="vibrate()" 
           style="flex: 1; display: flex; align-items: center; justify-content: center; 
                  background: {{ request()->routeIs('filament.user.resources.spesa-extras.*') ? 'rgba(147, 51, 234, 0.8)' : 'rgba(107, 114, 128, 0.5)' }}; 
                  color: {{ request()->routeIs('filament.user.resources.spesa-extras.*') ? 'white' : 'rgb(55, 65, 81)' }};
                  border: 1px solid {{ request()->routeIs('filament.user.resources.spesa-extras.*') ? 'rgba(147, 51, 234, 0.6)' : 'rgba(209, 213, 219, 0.5)' }};
                  text-decoration: none; transition: all 0.3s ease; font-weight: 600; font-size: 12px; 
                  border-radius: 12px; backdrop-filter: blur(5px);">
            {{ __("ui.footer_extra") }}
        </a>

        <!-- {{ __("ui.footer_docs") }} -->
        <a href="{{ route('filament.user.resources.documentos.index') }}" 
           onclick="vibrate()" 
           style="flex: 1; display: flex; align-items: center; justify-content: center; 
                  background: {{ request()->routeIs('filament.user.resources.documentos.*') ? 'rgba(34, 197, 94, 0.8)' : 'rgba(107, 114, 128, 0.5)' }}; 
                  color: {{ request()->routeIs('filament.user.resources.documentos.*') ? 'white' : 'rgb(55, 65, 81)' }};
                  border: 1px solid {{ request()->routeIs('filament.user.resources.documentos.*') ? 'rgba(34, 197, 94, 0.6)' : 'rgba(209, 213, 219, 0.5)' }};
                  text-decoration: none; transition: all 0.3s ease; font-weight: 600; font-size: 14px; 
                  border-radius: 12px; backdrop-filter: blur(5px);">
            {{ __("ui.footer_docs") }}
        </a>
    </div>
</footer>

<!-- PADDING PER FOOTER FISSO -->
<div style="height: 100px;"></div>

<!-- FAB DINAMICO - TRASPARENTE -->
@if(request()->routeIs('filament.user.resources.documentos.index'))
<div style="position: fixed; bottom: 100px; right: 20px; z-index: 1000;">
    <a href="/user/documentos/create" onclick="vibrate()" 
       style="display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; 
              background: rgba(34, 197, 94, 0.8); border: 2px solid rgba(34, 197, 94, 0.6); color: white; 
              border-radius: 50%; text-decoration: none; font-size: 24px; font-weight: bold; 
              box-shadow: 0 8px 16px rgba(34, 197, 94, 0.4); backdrop-filter: blur(10px); transition: all 0.3s ease;">+</a>
</div>
@elseif(request()->routeIs('filament.user.resources.spesas.index'))
<div style="position: fixed; bottom: 100px; right: 20px; z-index: 1000;">
    <a href="/user/spesas/create" onclick="vibrate()" 
       style="display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; 
              background: rgba(249, 115, 22, 0.8); border: 2px solid rgba(249, 115, 22, 0.6); color: white; 
              border-radius: 50%; text-decoration: none; font-size: 24px; font-weight: bold; 
              box-shadow: 0 8px 16px rgba(249, 115, 22, 0.4); backdrop-filter: blur(10px); transition: all 0.3s ease;">+</a>
</div>
@elseif(request()->routeIs('filament.user.resources.spesa-extras.index'))
<div style="position: fixed; bottom: 100px; right: 20px; z-index: 1000;">
    <a href="/user/spesa-extras/create" onclick="vibrate()" 
       style="display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; 
              background: rgba(147, 51, 234, 0.8); border: 2px solid rgba(147, 51, 234, 0.6); color: white; 
              border-radius: 50%; text-decoration: none; font-size: 24px; font-weight: bold; 
              box-shadow: 0 8px 16px rgba(147, 51, 234, 0.4); backdrop-filter: blur(10px); transition: all 0.3s ease;">+</a>
</div>
@elseif(request()->routeIs('filament.user.resources.reports.index'))
<div style="position: fixed; bottom: 100px; right: 20px; z-index: 1000;">
    <a href="/user/reports/create" onclick="vibrate()" 
       style="display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; 
              background: rgba(59, 130, 246, 0.8); border: 2px solid rgba(59, 130, 246, 0.6); color: white; 
              border-radius: 50%; text-decoration: none; font-size: 24px; font-weight: bold; 
              box-shadow: 0 8px 16px rgba(59, 130, 246, 0.4); backdrop-filter: blur(10px); transition: all 0.3s ease;">+</a>
</div>
@endif

<!-- JAVASCRIPT -->
<script>
function vibrate() {
    if (navigator.vibrate) {
        navigator.vibrate(50);
    }
}

document.querySelectorAll('footer a, [style*="position: fixed"] a').forEach(button => {
    button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px) scale(1.05)';
        this.style.filter = 'brightness(1.1)';
    });
    
    button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
        this.style.filter = 'brightness(1)';
    });
    
    button.addEventListener('mousedown', function() {
        this.style.transform = 'translateY(1px) scale(0.98)';
        vibrate();
    });
    
    button.addEventListener('mouseup', function() {
        this.style.transform = 'translateY(-2px) scale(1.05)';
    });
});
</script>

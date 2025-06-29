<!-- FOOTER FISSO MOBILE -->
<footer style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 50; background: white; border-top: 1px solid #e5e7eb; box-shadow: 0 -1px 3px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-around; padding: 12px 0; height: 80px;">
        <!-- HOME -->
        <a href="{{ route('filament.user.pages.dashboard') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; color: {{ request()->routeIs('filament.user.pages.dashboard') ? '#3b82f6' : '#9ca3af' }}; text-decoration: none; transition: color 0.2s;">
            <svg style="width: 24px; height: 24px; margin-bottom: 4px;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
            </svg>
            <span style="font-size: 12px; font-weight: 500;">Home</span>
        </a>

        <!-- SPESE -->
        <a href="{{ route('filament.user.resources.spesas.index') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; color: {{ request()->routeIs('filament.user.resources.spesas.*') ? '#ea580c' : '#9ca3af' }}; text-decoration: none; transition: color 0.2s;">
            <svg style="width: 24px; height: 24px; margin-bottom: 4px;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
            </svg>
            <span style="font-size: 12px; font-weight: 500;">Spese</span>
        </a>

        <!-- SPESE EXTRA - PLACEHOLDER per ora -->
        <a href="#" style="display: flex; flex-direction: column; align-items: center; justify-content: center; color: #9ca3af; text-decoration: none; transition: color 0.2s;">
            <svg style="width: 24px; height: 24px; margin-bottom: 4px;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
            </svg>
            <span style="font-size: 10px; font-weight: 500;">Spese Extra</span>
        </a>

        <!-- DOCS - COLLEGATO! -->
        <a href="{{ route('filament.user.resources.documentos.index') }}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; color: {{ request()->routeIs('filament.user.resources.documentos.*') ? '#9333ea' : '#9ca3af' }}; text-decoration: none; transition: color 0.2s;">
            <svg style="width: 24px; height: 24px; margin-bottom: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span style="font-size: 12px; font-weight: 500;">Docs</span>
        </a>
    </div>
</footer>

<!-- PADDING PER FOOTER FISSO -->
<div style="height: 80px;"></div>

<!-- FAB DINAMICO -->
@if(request()->routeIs('filament.user.resources.documentos.index'))
<div style="position: fixed; bottom: 100px; right: 20px; z-index: 1000;">
    <a href="/user/documentos/create" style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: rgba(16, 185, 129, 0.8); color: white; border-radius: 50%; text-decoration: none; font-size: 20px; font-weight: bold; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">+</a>
</div>
@elseif(request()->routeIs('filament.user.resources.spesas.index'))
<div style="position: fixed; bottom: 100px; right: 20px; z-index: 1000;">
    <a href="/user/spesas/create" style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: rgba(234, 88, 12, 0.8); color: white; border-radius: 50%; text-decoration: none; font-size: 20px; font-weight: bold; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">+</a>
</div>
@endif

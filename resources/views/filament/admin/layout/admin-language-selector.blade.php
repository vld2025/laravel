{{-- Language Selector per Admin Panel Desktop --}}
<style>
.admin-language-selector {
    position: relative;
    display: inline-block;
}
.admin-language-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: 1px solid #e5e7eb;
    min-width: 180px;
    z-index: 1000;
    margin-top: 4px;
}
.admin-language-dropdown.show {
    display: block;
}
.admin-language-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    color: #374151;
}
.admin-language-button:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}
.admin-language-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    color: #374151;
    text-decoration: none;
    transition: background-color 0.2s;
    font-size: 14px;
}
.admin-language-option:hover {
    background: #f9fafb;
    color: #374151;
    text-decoration: none;
}
.admin-language-option.active {
    background: #eff6ff;
    color: #2563eb;
}
.admin-flag-icon {
    width: 20px;
    height: 14px;
    border-radius: 2px;
    background-size: cover;
    background-position: center;
}
.flag-it { background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3 2"><rect width="1" height="2" fill="%23009246"/><rect width="1" height="2" x="1" fill="%23fff"/><rect width="1" height="2" x="2" fill="%23ce2b37"/></svg>'); }
.flag-en { background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30"><rect width="60" height="30" fill="%23012169"/><g fill="%23fff"><path d="m0 0 60 30m0-30L0 30"/><path d="m25 0v30m10 0V0"/></g><g fill="%23c8102e"><path d="m0 0 60 30m0-30L0 30" stroke-width="6"/><path d="m30 0v30M0 15h60" stroke-width="10"/></g></svg>'); }
.flag-de { background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 5 3"><rect width="5" height="1" fill="%23000"/><rect width="5" height="1" y="1" fill="%23dd0000"/><rect width="5" height="1" y="2" fill="%23ffce00"/></svg>'); }
.flag-ru { background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3 2"><rect width="3" height="1" fill="%23fff"/><rect width="3" height="1" y="1" fill="%230039a6"/><rect width="3" height="1" y="0.67" fill="%23d52b1e"/></svg>'); }
</style>

@auth
<div class="admin-language-selector">
    @php
        $currentLocale = session("locale", app()->getLocale());
        $languages = [
            'it' => 'Italiano',
            'en' => 'English', 
            'de' => 'Deutsch',
            'ru' => 'Русский'
        ];
    @endphp
    
    <button onclick="toggleAdminLanguageDropdown()" class="admin-language-button">
        <span class="admin-flag-icon flag-{{ $currentLocale }}"></span>
        <span>{{ $languages[$currentLocale] ?? 'Language' }}</span>
        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div id="adminLanguageDropdown" class="admin-language-dropdown">
        @foreach($languages as $locale => $name)
            <a href="{{ route('language.switch', $locale) }}" 
               class="admin-language-option {{ $currentLocale === $locale ? 'active' : '' }}">
                <span class="admin-flag-icon flag-{{ $locale }}"></span>
                <span>{{ $name }}</span>
                @if($currentLocale === $locale)
                    <svg style="margin-left: auto; width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>

<script>
function toggleAdminLanguageDropdown() {
    const dropdown = document.getElementById('adminLanguageDropdown');
    dropdown.classList.toggle('show');
}

// Chiudi dropdown cliccando fuori
document.addEventListener('click', function(event) {
    const selector = document.querySelector('.admin-language-selector');
    if (!selector.contains(event.target)) {
        document.getElementById('adminLanguageDropdown').classList.remove('show');
    }
});

// Chiudi dropdown quando si clicca su una lingua
document.querySelectorAll('.admin-language-option').forEach(function(link) {
    link.addEventListener('click', function() {
        document.getElementById('adminLanguageDropdown').classList.remove('show');
    });
});
</script>
@endauth

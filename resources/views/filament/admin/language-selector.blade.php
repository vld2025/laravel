<div class="fi-dropdown fi-user-menu" style="margin-right: 1rem;">
    <button type="button" class="fi-dropdown-trigger fi-user-menu-trigger group flex shrink-0 items-center justify-center rounded-full p-2 text-gray-400 outline-none transition duration-75 hover:bg-gray-50 hover:text-gray-500 focus:bg-gray-50 focus:text-gray-500 dark:text-gray-500 dark:hover:bg-white/5 dark:hover:text-gray-400 dark:focus:bg-white/5 dark:focus:text-gray-400" onclick="toggleLanguageDropdown()">
        <span class="flag-icon flag-{{ session('locale', 'it') }}" style="width: 20px; height: 14px; background-size: cover; border-radius: 2px;"></span>
    </button>
    
    <div id="adminLanguageDropdown" class="fi-dropdown-panel absolute right-0 top-full z-10 mt-2 hidden w-48 rounded-lg bg-white p-1 shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <a href="{{ route('language.switch', 'it') }}" class="fi-dropdown-list-item flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-700 outline-none transition duration-75 hover:bg-gray-50 focus:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5 dark:focus:bg-white/5 {{ session('locale', 'it') === 'it' ? 'bg-gray-50 dark:bg-white/5' : '' }}">
            <span class="flag-icon flag-it" style="width: 20px; height: 14px; background-size: cover; border-radius: 2px;"></span>
            Italiano
        </a>
        <a href="{{ route('language.switch', 'en') }}" class="fi-dropdown-list-item flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-700 outline-none transition duration-75 hover:bg-gray-50 focus:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5 dark:focus:bg-white/5 {{ session('locale', 'it') === 'en' ? 'bg-gray-50 dark:bg-white/5' : '' }}">
            <span class="flag-icon flag-en" style="width: 20px; height: 14px; background-size: cover; border-radius: 2px;"></span>
            English
        </a>
        <a href="{{ route('language.switch', 'de') }}" class="fi-dropdown-list-item flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-700 outline-none transition duration-75 hover:bg-gray-50 focus:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5 dark:focus:bg-white/5 {{ session('locale', 'it') === 'de' ? 'bg-gray-50 dark:bg-white/5' : '' }}">
            <span class="flag-icon flag-de" style="width: 20px; height: 14px; background-size: cover; border-radius: 2px;"></span>
            Deutsch
        </a>
        <a href="{{ route('language.switch', 'ru') }}" class="fi-dropdown-list-item flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-700 outline-none transition duration-75 hover:bg-gray-50 focus:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5 dark:focus:bg-white/5 {{ session('locale', 'it') === 'ru' ? 'bg-gray-50 dark:bg-white/5' : '' }}">
            <span class="flag-icon flag-ru" style="width: 20px; height: 14px; background-size: cover; border-radius: 2px;"></span>
            Русский
        </a>
    </div>
</div>

<style>
.flag-it { background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3 2"><rect width="1" height="2" fill="%23009246"/><rect width="1" height="2" x="1" fill="%23fff"/><rect width="1" height="2" x="2" fill="%23ce2b37"/></svg>'); }
.flag-en { background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30"><rect width="60" height="30" fill="%23012169"/><g fill="%23fff"><path d="m0 0 60 30m0-30L0 30"/><path d="m25 0v30m10 0V0"/></g><g fill="%23c8102e"><path d="m0 0 60 30m0-30L0 30" stroke-width="6"/><path d="m30 0v30M0 15h60" stroke-width="10"/></g></svg>'); }
.flag-de { background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 5 3"><rect width="5" height="1" fill="%23000"/><rect width="5" height="1" y="1" fill="%23dd0000"/><rect width="5" height="1" y="2" fill="%23ffce00"/></svg>'); }
.flag-ru { background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3 2"><rect width="3" height="1" fill="%23fff"/><rect width="3" height="1" y="1" fill="%230039a6"/><rect width="3" height="1" y="0.67" fill="%23d52b1e"/></svg>'); }
</style>

<script>
function toggleLanguageDropdown() {
    const dropdown = document.getElementById('adminLanguageDropdown');
    dropdown.classList.toggle('hidden');
}

// Chiudi dropdown quando si clicca fuori
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('adminLanguageDropdown');
    const trigger = event.target.closest('.fi-dropdown-trigger');
    if (!trigger) {
        dropdown.classList.add('hidden');
    }
});
</script>

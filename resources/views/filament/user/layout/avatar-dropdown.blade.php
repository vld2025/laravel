<!-- AVATAR DROPDOWN PER HEADER FILAMENT STANDARD -->
<style>
.avatar-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: 1px solid #e5e7eb;
    min-width: 200px;
    z-index: 1000;
}
.avatar-dropdown.show {
    display: block;
}
.avatar-container {
    position: relative;
}
#avatarFileInput {
    display: none;
}
</style>

<div class="avatar-container">
    <button onclick="toggleAvatarDropdown()" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 transition-colors">
        @if(auth()->user()->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar {{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border-2 border-gray-300">
        @else
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        @endif
    </button>

    <!-- Dropdown Menu -->
    <div id="avatarDropdown" class="avatar-dropdown">
        <div class="p-4 border-b border-gray-100">
            <p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
        </div>

        <div class="p-2">
            <!-- CARICA AVATAR -->
            <button onclick="triggerAvatarUpload()" class="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Carica Avatar
            </button>

            <!-- LOGOUT -->
            <form method="POST" action="{{ route('filament.user.auth.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md transition-colors">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>

<!-- INPUT FILE NASCOSTO -->
<form id="avatarForm" action="{{ route('filament.user.upload-avatar') }}" method="POST" enctype="multipart/form-data" style="display: none;">
    @csrf
    <input type="file" id="avatarFileInput" name="avatar" accept="image/*" capture="user">
</form>

<script>
function toggleAvatarDropdown() {
    const dropdown = document.getElementById('avatarDropdown');
    dropdown.classList.toggle('show');
}

function triggerAvatarUpload() {
    document.getElementById('avatarDropdown').classList.remove('show');
    document.getElementById('avatarFileInput').click();
}

document.getElementById('avatarFileInput').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        document.getElementById('avatarForm').submit();
    }
});

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('avatarDropdown');
    const container = document.querySelector('.avatar-container');
    if (container && !container.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});
</script>

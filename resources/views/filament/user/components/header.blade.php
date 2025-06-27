<div class="flex items-center justify-between w-full px-4 py-2 bg-green-50 border-b border-green-200">
    <!-- LOGO A SINISTRA -->
    <div class="flex items-center">
        <img src="{{ asset('images/logo/1.png') }}" alt="VLD Service GmbH" class="h-10 w-auto">
        <span class="ml-3 text-lg font-semibold text-green-800">VLD Service Mobile</span>
    </div>
    
    <!-- USER INFO A DESTRA -->
    <div class="flex items-center text-sm text-green-700">
        <span>{{ auth()->user()->name }}</span>
    </div>
</div>

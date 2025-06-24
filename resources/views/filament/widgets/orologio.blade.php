<div class="bg-white p-4 rounded-lg shadow border border-gray-200" 
     x-data="{ 
         currentTime: '{{ now()->format('H:i:s') }}',
         currentDate: '{{ now()->translatedFormat('l, d F Y') }}',
         lastUpdate: '{{ now()->format('H:i:s') }}',
         updateClock() {
             const now = new Date();
             this.currentTime = now.toLocaleTimeString('it-IT', {
                 hour: '2-digit',
                 minute: '2-digit', 
                 second: '2-digit',
                 hour12: false
             });
             this.currentDate = now.toLocaleDateString('it-IT', {
                 weekday: 'long',
                 year: 'numeric',
                 month: 'long',
                 day: 'numeric'
             });
             this.lastUpdate = this.currentTime;
         }
     }"
     x-init="setInterval(() => updateClock(), 1000)">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">⏰ Ora Corrente Server</h3>
            <p class="text-sm text-gray-600">
                <span class="inline-flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Aggiornamento automatico ogni secondo
                </span>
            </p>
        </div>
        <div class="text-right">
            <div class="text-2xl font-bold text-blue-600" x-text="currentTime">{{ now()->format('H:i:s') }}</div>
            <div class="text-sm text-gray-500" x-text="currentDate">{{ now()->translatedFormat('l, d F Y') }}</div>
            <div class="text-xs text-gray-400 mt-1">
                Ultimo aggiornamento: <span x-text="lastUpdate">{{ now()->format('H:i:s') }}</span>
            </div>
        </div>
    </div>
</div>

<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Statistiche utente -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-lg p-4 shadow">
                <h3 class="text-lg font-semibold text-green-600">Report Mese</h3>
                <p class="text-2xl font-bold">12</p>
            </div>
            <div class="bg-white rounded-lg p-4 shadow">
                <h3 class="text-lg font-semibold text-blue-600">Ore Lavorate</h3>
                <p class="text-2xl font-bold">96</p>
            </div>
        </div>

        <!-- Azioni rapide -->
        <div class="bg-white rounded-lg p-6 shadow">
            <h3 class="text-lg font-semibold mb-4">Azioni Rapide</h3>
            <div class="grid grid-cols-2 gap-3">
                <button class="bg-green-500 text-white p-4 rounded-lg">
                    Nuovo Report
                </button>
                <button class="bg-orange-500 text-white p-4 rounded-lg">
                    Nuova Spesa
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>

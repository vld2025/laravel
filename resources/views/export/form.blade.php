<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Report Excel - VLD Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Export Report Excel</h1>
                <p class="text-gray-600 mt-2">Esporta i report mensili in formato Excel</p>
            </div>

            <form id="exportForm" method="POST" action="{{ route('export.report.mensile') }}">
                @csrf
                
                <!-- Committente -->
                <div class="mb-4">
                    <label for="committente_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Committente
                    </label>
                    <select name="committente_id" id="committente_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleziona committente...</option>
                        @foreach($committenti as $committente)
                            <option value="{{ $committente->id }}">{{ $committente->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Anno -->
                <div class="mb-4">
                    <label for="anno" class="block text-sm font-medium text-gray-700 mb-2">
                        Anno
                    </label>
                    <select name="anno" id="anno" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($anni as $anno)
                            <option value="{{ $anno }}" {{ $anno == date('Y') ? 'selected' : '' }}>
                                {{ $anno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mese -->
                <div class="mb-6">
                    <label for="mese" class="block text-sm font-medium text-gray-700 mb-2">
                        Mese
                    </label>
                    <select name="mese" id="mese" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($mesi as $numero => $nome)
                            <option value="{{ $numero }}" {{ $numero == date('n') ? 'selected' : '' }}>
                                {{ $nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Preview periodo -->
                <div id="periodoPreview" class="mb-6 p-4 bg-blue-50 rounded-md hidden">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Periodo di fatturazione:</h3>
                    <p class="text-sm text-blue-700">
                        Dal <span id="dataInizio"></span> al <span id="dataFine"></span>
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        Giorno fatturazione: <span id="giornoFatturazione"></span>
                    </p>
                </div>

                <!-- Bottoni -->
                <div class="flex space-x-3">
                    <button type="button" id="previewBtn" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Anteprima Periodo
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Scarica Excel
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="/admin" class="text-blue-600 hover:text-blue-800 text-sm">
                    ← Torna al pannello admin
                </a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('previewBtn').addEventListener('click', function() {
            const form = document.getElementById('exportForm');
            const formData = new FormData(form);
            
            if (!formData.get('committente_id') || !formData.get('anno') || !formData.get('mese')) {
                alert('Compila tutti i campi prima di visualizzare l\'anteprima');
                return;
            }

            fetch('{{ route('export.preview.periodo') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('dataInizio').textContent = data.periodo_inizio;
                document.getElementById('dataFine').textContent = data.periodo_fine;
                document.getElementById('giornoFatturazione').textContent = data.giorno_fatturazione;
                document.getElementById('periodoPreview').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore nel caricamento dell\'anteprima');
            });
        });
    </script>
</body>
</html>

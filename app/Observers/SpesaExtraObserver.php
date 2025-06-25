<?php

namespace App\Observers;

use App\Models\SpesaExtra;
use App\Services\AIService;
use Illuminate\Support\Facades\Log;

class SpesaExtraObserver
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Handle the SpesaExtra "created" event.
     */
    public function created(SpesaExtra $spesaExtra): void
    {
        // Elabora automaticamente con AI dopo la creazione
        $this->processWithAI($spesaExtra);
    }

    /**
     * Handle the SpesaExtra "updated" event.
     */
    public function updated(SpesaExtra $spesaExtra): void
    {
        // Se il file è cambiato, riprocessa
        if ($spesaExtra->wasChanged('file') && $spesaExtra->file) {
            $this->processWithAI($spesaExtra);
        }
    }

    /**
     * Elabora la spesa extra con AI
     */
    private function processWithAI(SpesaExtra $spesaExtra): void
    {
        try {
            // Verifica che l'AI sia configurata
            if (!$this->aiService->isConfigured()) {
                Log::info('AI not configured, skipping processing for SpesaExtra ID: ' . $spesaExtra->id);
                return;
            }

            // Verifica che ci sia un file
            if (!$spesaExtra->file) {
                Log::info('No file to process for SpesaExtra ID: ' . $spesaExtra->id);
                return;
            }

            // Costruisci il path completo del file
            $filePath = 'public/spese-extra/' . basename($spesaExtra->file);
            
            Log::info('Processing SpesaExtra ID: ' . $spesaExtra->id . ' with file: ' . $filePath);

            // Elabora con AI
            $result = $this->aiService->extractDataFromReceipt($filePath);

            if ($result['success']) {
                // Aggiorna solo se l'AI ha trovato dati validi
                $updateData = [];
                
                if ($result['importo'] !== null) {
                    $updateData['importo'] = $result['importo'];
                }
                
                if (!empty($result['descrizione']) && $result['descrizione'] !== 'Errore nell\'elaborazione AI') {
                    $updateData['descrizione'] = $result['descrizione'];
                }

                if (!empty($updateData)) {
                    // Aggiorna senza triggering dell'observer per evitare loop infinito
                    SpesaExtra::withoutEvents(function () use ($spesaExtra, $updateData) {
                        $spesaExtra->update($updateData);
                    });

                    Log::info('Successfully processed SpesaExtra ID: ' . $spesaExtra->id, $updateData);
                } else {
                    Log::info('No valid data extracted for SpesaExtra ID: ' . $spesaExtra->id);
                }
            } else {
                Log::warning('AI processing failed for SpesaExtra ID: ' . $spesaExtra->id, [
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception in SpesaExtraObserver for ID: ' . $spesaExtra->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

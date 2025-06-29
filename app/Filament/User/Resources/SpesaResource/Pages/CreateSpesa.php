<?php

namespace App\Filament\User\Resources\SpesaResource\Pages;

use App\Filament\User\Resources\SpesaResource;
use App\Services\AIService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateSpesa extends CreateRecord
{
    protected static string $resource = SpesaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function afterCreate(): void
    {
        // Processa la ricevuta con ChatGPT
        if (!empty($this->record->file)) {
            $this->processWithChatGPT($this->record->file);
        }
    }

    private function processWithChatGPT(string $filePath): void
    {
        try {
            Log::info('Processing receipt with ChatGPT', ['file' => $filePath]);
            
            $aiService = app(AIService::class);
            
            if (!$aiService->isConfigured()) {
                return;
            }
            
            // Usa il metodo esistente per estrarre dati dalle ricevute
            $result = $aiService->extractDataFromReceipt($filePath);
            
            if ($result['success']) {
                // Aggiorna la spesa con i dati estratti se disponibili
                $updateData = [];
                
                if (!empty($result['descrizione'])) {
                    $updateData['descrizione'] = $result['descrizione'];
                }
                
                if (!empty($updateData)) {
                    $this->record->update($updateData);
                    Log::info('Spesa updated with AI data', $updateData);
                }
            }
            
        } catch (\Exception $e) {
            Log::error('ChatGPT processing error: ' . $e->getMessage());
        }
    }
}

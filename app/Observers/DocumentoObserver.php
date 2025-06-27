<?php

namespace App\Observers;

use App\Models\Documento;
use App\Services\BackupService;
use Illuminate\Support\Facades\Log;

class DocumentoObserver
{
    private $backupService;
    
    public function __construct()
    {
        $this->backupService = new BackupService();
    }
    
    /**
     * Handle the Documento "created" event.
     */
    public function created(Documento $documento): void
    {
        $this->backupDocumento($documento);
    }
    
    /**
     * Handle the Documento "updated" event.
     */
    public function updated(Documento $documento): void
    {
        if ($documento->wasChanged('file_path')) {
            $this->backupDocumento($documento);
        }
    }
    
    /**
     * Backup documento su NAS
     */
    private function backupDocumento(Documento $documento)
    {
        try {
            if ($documento->file_path) {
                $sottotipo = match($documento->tipo) {
                    'buste_paga' => 'BustePaga',
                    'personale' => 'Personali',
                    'aziendale' => 'Aziendali',
                    default => 'Altri'
                };
                
                $sottocartella = $documento->user ? $documento->user->name : 'Generale';
                
                // Ottieni il file dalla storage
                $percorsoFile = storage_path('app/public/' . $documento->file_path);
                if (file_exists($percorsoFile)) {
                    $file = new \Illuminate\Http\UploadedFile(
                        $percorsoFile,
                        basename($documento->file_path),
                        mime_content_type($percorsoFile),
                        null,
                        true
                    );
                    
                    $this->backupService->backupFile(
                        $file, 
                        "Documenti/{$sottotipo}", 
                        $sottocartella,
                        $documento->titolo . '_' . basename($documento->file_path)
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Errore backup documento: ' . $e->getMessage());
        }
    }
}

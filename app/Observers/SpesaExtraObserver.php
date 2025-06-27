<?php

namespace App\Observers;

use App\Models\SpesaExtra;
use App\Services\BackupService;
use Illuminate\Support\Facades\Log;

class SpesaExtraObserver
{
    private $backupService;
    
    public function __construct()
    {
        $this->backupService = new BackupService();
    }
    
    /**
     * Handle the SpesaExtra "created" event.
     */
    public function created(SpesaExtra $spesaExtra): void
    {
        $this->backupRicevuta($spesaExtra);
    }
    
    /**
     * Handle the SpesaExtra "updated" event.
     */
    public function updated(SpesaExtra $spesaExtra): void
    {
        if ($spesaExtra->wasChanged('ricevuta')) {
            $this->backupRicevuta($spesaExtra);
        }
    }
    
    /**
     * Backup ricevuta su NAS
     */
    private function backupRicevuta(SpesaExtra $spesaExtra)
    {
        try {
            if ($spesaExtra->ricevuta) {
                $user = $spesaExtra->user;
                $sottocartella = $user->name;
                
                // Ottieni il file dalla storage
                $percorsoFile = storage_path('app/public/' . $spesaExtra->ricevuta);
                if (file_exists($percorsoFile)) {
                    $file = new \Illuminate\Http\UploadedFile(
                        $percorsoFile,
                        basename($spesaExtra->ricevuta),
                        mime_content_type($percorsoFile),
                        null,
                        true
                    );
                    
                    $this->backupService->backupFile($file, 'SpesaExtra', $sottocartella);
                }
            }
        } catch (\Exception $e) {
            Log::error('Errore backup spesa extra: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Observers;

use App\Models\Report;
use App\Services\FestivoService;
use App\Services\ReportGeneratorService;
use App\Services\BackupService;
use Illuminate\Support\Facades\Log;

class ReportObserver
{
    protected $reportGenerator;
    protected $backupService;

    public function __construct(ReportGeneratorService $reportGenerator, BackupService $backupService)
    {
        $this->reportGenerator = $reportGenerator;
        $this->backupService = $backupService;
    }

    /**
     * Handle the Report "creating" event.
     */
    public function creating(Report $report): void
    {
        $this->calcolaFestivo($report);
        $this->copiaDatiPerFatturazione($report);
    }

    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        $this->generaReportAI($report);
        $this->calcolaFestivo($report);
        $this->backupReport($report);
    }

    /**
     * Handle the Report "updating" event.
     */
    public function updating(Report $report): void
    {
        // Se la data è cambiata, ricalcola il festivo
        if ($report->isDirty('data')) {
            $this->calcolaFestivo($report);
        }

        // Se l'user ha modificato le ore e non è admin/manager,
        // aggiorna anche i campi fatturazione
        if ($this->shouldUpdateFatturazione($report)) {
            $this->copiaDatiPerFatturazione($report);
        }
    }

    /**
     * Handle the Report "updated" event.
     */
    public function updated(Report $report): void
    {
        $this->generaReportAI($report);
        $this->calcolaFestivo($report);
        $this->backupReport($report);
    }

    /**
     * Calcola se la data del report è festiva
     */
    private function calcolaFestivo(Report $report): void
    {
        if ($report->data) {
            $report->festivo = FestivoService::isFestivo($report->data);
        }
    }

    /**
     * Copia i dati dell'user nei campi fatturazione se vuoti
     */
    private function copiaDatiPerFatturazione(Report $report): void
    {
        // Copia ore lavorate se il campo fatturazione è vuoto
        if (is_null($report->ore_lavorate_fatturazione) && !is_null($report->ore_lavorate)) {
            $report->ore_lavorate_fatturazione = $report->ore_lavorate;
        }

        // Copia ore viaggio se il campo fatturazione è vuoto
        if (is_null($report->ore_viaggio_fatturazione) && !is_null($report->ore_viaggio)) {
            $report->ore_viaggio_fatturazione = $report->ore_viaggio;
        }
    }

    /**
     * Determina se aggiornare i campi fatturazione
     */
    private function shouldUpdateFatturazione(Report $report): bool
    {
        $user = auth()->user();

        // Se non c'è utente autenticato, non aggiornare
        if (!$user) {
            return false;
        }

        // Se è admin/manager, non aggiornare automaticamente (lo fanno manualmente)
        if ($user->canViewAllData()) {
            return false;
        }

        // Se l'user normale ha modificato le sue ore, aggiorna anche fatturazione
        return $report->isDirty('ore_lavorate') || $report->isDirty('ore_viaggio');
    }

    /**
     * Genera report AI automaticamente
     */
    private function generaReportAI(Report $report): void
    {
        $this->generateMultilingualReport($report);
    }

    /**
     * Genera report multilingua automaticamente
     */
    private function generateMultilingualReport(Report $report): void
    {
        try {
            // Verifica che il servizio sia configurato
            if (!$this->reportGenerator->isConfigured()) {
                Log::info('Report Generator not configured, skipping multilingual generation for Report ID: ' . $report->id);
                return;
            }

            // Verifica che ci sia una descrizione
            if (empty($report->descrizione_lavori)) {
                Log::info('No description provided, skipping multilingual generation for Report ID: ' . $report->id);
                return;
            }

            Log::info('Generating multilingual report for Report ID: ' . $report->id);

            // Genera i report in tutte le lingue
            $result = $this->reportGenerator->generateProfessionalReport($report);

            if ($result['success']) {
                // Aggiorna i campi senza triggering dell'observer
                Report::withoutEvents(function () use ($report, $result) {
                    $updateData = [];

                    if (!empty($result['reports']['it'])) {
                        $updateData['descrizione_it'] = $result['reports']['it'];
                    }

                    if (!empty($result['reports']['en'])) {
                        $updateData['descrizione_en'] = $result['reports']['en'];
                    }

                    if (!empty($result['reports']['de'])) {
                        $updateData['descrizione_de'] = $result['reports']['de'];
                    }

                    if (!empty($result['reports']['ru'])) {
                        $updateData['descrizione_ru'] = $result['reports']['ru'];
                    }

                    if (!empty($updateData)) {
                        $report->update($updateData);
                        Log::info('Successfully generated multilingual reports for Report ID: ' . $report->id, array_keys($updateData));
                    }
                });
            } else {
                Log::warning('Failed to generate multilingual reports for Report ID: ' . $report->id, [
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception in multilingual report generation for Report ID: ' . $report->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Backup automatico dopo creazione/modifica report
     */
    private function backupReport(Report $report)
    {
        try {
            // Backup Excel del report
            $this->backupService->aggiornaExcelReport($report);
            
            Log::info("Report backup completed for Report ID: " . $report->id);
            
        } catch (\Exception $e) {
            Log::error('Errore backup report: ' . $e->getMessage());
        }
    }
}

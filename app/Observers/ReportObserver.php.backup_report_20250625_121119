<?php

namespace App\Observers;

use App\Models\Report;
use App\Services\FestivoService;

class ReportObserver
{
    /**
     * Handle the Report "creating" event.
     */
    public function creating(Report $report): void
    {
        $this->calcolaFestivo($report);
        $this->copiaDatiPerFatturazione($report);
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
}

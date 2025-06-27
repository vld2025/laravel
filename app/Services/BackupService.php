<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Committente;
use App\Exports\ReportMensileExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupService
{
    private $nasPath;
    private $localPath;

    public function __construct()
    {
        $this->nasPath = '/mnt/synology-backup/VLD-Backup';
        $this->localPath = storage_path('app/backup');
    }

    /**
     * Esporta report in Excel per committente e mese - SOVRASCRIVE IL FILE ESISTENTE
     */
    public function esportaReportMensile($mese, $anno, $committenteId = null)
    {
        try {
            if ($committenteId) {
                $committenti = Committente::where('id', $committenteId)->get();
            } else {
                $committenti = Committente::all();
            }

            foreach ($committenti as $committente) {
                $reports = Report::with(['user', 'cliente', 'commessa'])
                    ->whereYear('data', $anno)
                    ->whereMonth('data', $mese)
                    ->whereHas('commessa.cliente', function($q) use ($committente) {
                        $q->where('committente_id', $committente->id);
                    })
                    ->orderBy('data')
                    ->orderBy('created_at')
                    ->get();

                if ($reports->count() > 0) {
                    // Nome file FISSO (senza timestamp) per sovrascrivere
                    $nomeFile = "Report_{$this->getNomeMese($mese)}_{$anno}_{$committente->nome}.xlsx";
                    $percorsoRelativo = "Excel/{$anno}/" . $this->getNomeMese($mese) . "/{$committente->nome}/";

                    // Crea le directory se non esistono
                    $this->creaDirectory($percorsoRelativo);

                    // Genera Excel
                    $export = new ReportMensileExport($committente->id, $anno, $mese);

                    // Percorso completo del file
                    $percorsoCompletoLocale = $this->localPath . '/' . $percorsoRelativo . $nomeFile;
                    $percorsoCompletoNAS = $this->nasPath . '/' . $percorsoRelativo . $nomeFile;

                    // Elimina file esistente se presente (per sovrascrivere)
                    if (file_exists($percorsoCompletoLocale)) {
                        unlink($percorsoCompletoLocale);
                    }
                    if (file_exists($percorsoCompletoNAS)) {
                        unlink($percorsoCompletoNAS);
                    }

                    // Salva il nuovo file
                    Excel::store($export, $percorsoRelativo . $nomeFile, 'backup');

                    // Copia su NAS
                    $this->copiasuNAS($percorsoRelativo . $nomeFile);

                    Log::info("Excel Report aggiornato: {$percorsoRelativo}{$nomeFile}");

                    return $percorsoRelativo . $nomeFile;
                }
            }
        } catch (\Exception $e) {
            Log::error("Errore backup Excel: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Esporta SINGOLO report in tempo reale quando viene creato/modificato
     */
    public function aggiornaExcelReport(Report $report)
    {
        try {
            // CARICA ESPLICITAMENTE TUTTE LE RELAZIONI
            $report->load(['commessa.cliente.committente']);
            
            $committente = $report->commessa->cliente->committente;
            $mese = $report->data->month;
            $anno = $report->data->year;

            // Recupera TUTTI i report del mese per quel committente usando l'ID
            $reports = Report::with(['user', 'cliente', 'commessa'])
                ->whereYear('data', $anno)
                ->whereMonth('data', $mese)
                ->whereHas('commessa.cliente', function($q) use ($committente) {
                    $q->where('committente_id', $committente->id);
                })
                ->orderBy('data')
                ->orderBy('created_at')
                ->get();

            if ($reports->count() > 0) {
                // Nome file FISSO per sovrascrivere
                $nomeFile = "Report_{$this->getNomeMese($mese)}_{$anno}_{$committente->nome}.xlsx";
                $percorsoRelativo = "Excel/{$anno}/" . $this->getNomeMese($mese) . "/{$committente->nome}/";

                // Crea le directory se non esistono
                $this->creaDirectory($percorsoRelativo);

                // Genera Excel con TUTTI i report del mese
                $export = new ReportMensileExport($committente->id, $anno, $mese);

                // Salva sovrascrivendo il file esistente
                Excel::store($export, $percorsoRelativo . $nomeFile, 'backup');

                // Copia su NAS (sovrascrive)
                $this->copiasuNAS($percorsoRelativo . $nomeFile);

                Log::info("Excel aggiornato in tempo reale per nuovo report: {$percorsoRelativo}{$nomeFile}");
            }
        } catch (\Exception $e) {
            Log::error("Errore aggiornamento Excel report: " . $e->getMessage());
        }
    }

    /**
     * Backup di un file caricato
     */
    public function backupFile($file, $tipo, $sottocartella = '', $nomeOriginale = null)
    {
        try {
            $anno = Carbon::now()->year;
            $mese = Carbon::now()->month;
            $nomeMese = $this->getNomeMese($mese);

            $percorso = "{$tipo}/{$anno}/{$nomeMese}";
            if ($sottocartella) {
                $percorso .= "/{$sottocartella}";
            }

            $this->creaDirectory($percorso);

            // Usa nome originale o genera nuovo nome
            $nomeFile = $nomeOriginale ?: $file->hashName();

            // Salva localmente
            Storage::disk('backup')->putFileAs($percorso, $file, $nomeFile);

            // Copia su NAS
            $this->copiasuNAS($percorso . '/' . $nomeFile);

            Log::info("File backuppato: {$percorso}/{$nomeFile}");

            return $percorso . '/' . $nomeFile;
        } catch (\Exception $e) {
            Log::error("Errore backup file: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Backup di un file PDF già generato
     */
    public function backupPDF($contenutoPDF, $tipo, $nomeFile)
    {
        try {
            $anno = Carbon::now()->year;
            $percorso = "PDF/{$tipo}/{$anno}";

            $this->creaDirectory($percorso);

            // Salva localmente
            $percorsoCompleto = $this->localPath . '/' . $percorso . '/' . $nomeFile;
            file_put_contents($percorsoCompleto, $contenutoPDF);

            // Copia su NAS
            $this->copiasuNAS($percorso . '/' . $nomeFile);

            Log::info("PDF backuppato: {$percorso}/{$nomeFile}");

            return $percorso . '/' . $nomeFile;
        } catch (\Exception $e) {
            Log::error("Errore backup PDF: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Backup del database
     */
    public function backupDatabase()
    {
        try {
            $data = Carbon::now()->format('Y-m-d_H-i-s');
            $nomeFile = "database_backup_{$data}.sql";
            $percorso = "Database/Backup_giornalieri";

            $this->creaDirectory($percorso);

            // Comando mysqldump
            $comando = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                config('database.connections.mysql.username'),
                escapeshellarg(config('database.connections.mysql.password')),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.database'),
                $this->localPath . '/' . $percorso . '/' . $nomeFile
            );

            exec($comando, $output, $return);

            if ($return === 0) {
                // Copia su NAS
                $this->copiasuNAS($percorso . '/' . $nomeFile);

                // Comprimi il backup
                $this->comprimiFile($percorso . '/' . $nomeFile);

                Log::info("Database backuppato: {$percorso}/{$nomeFile}");

                return $percorso . '/' . $nomeFile;
            } else {
                throw new \Exception("Errore mysqldump: " . implode("\n", $output));
            }
        } catch (\Exception $e) {
            Log::error("Errore backup database: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crea directory se non esistono
     */
    private function creaDirectory($percorso)
    {
        // Locale
        $percorsoCompleto = $this->localPath . '/' . $percorso;
        if (!file_exists($percorsoCompleto)) {
            mkdir($percorsoCompleto, 0755, true);
        }

        // NAS
        $percorsoNAS = $this->nasPath . '/' . $percorso;
        if (!file_exists($percorsoNAS)) {
            mkdir($percorsoNAS, 0755, true);
        }
    }

    /**
     * Copia file su NAS
     */
    private function copiasuNAS($percorsoRelativo)
    {
        $origine = $this->localPath . '/' . $percorsoRelativo;
        $destinazione = $this->nasPath . '/' . $percorsoRelativo;

        if (file_exists($origine)) {
            if (!copy($origine, $destinazione)) {
                throw new \Exception("Impossibile copiare su NAS: {$percorsoRelativo}");
            }
        }
    }

    /**
     * Comprimi file
     */
    private function comprimiFile($percorsoRelativo)
    {
        $percorsoCompleto = $this->localPath . '/' . $percorsoRelativo;
        $percorsoCompresso = $percorsoCompleto . '.gz';

        $comando = "gzip -c {$percorsoCompleto} > {$percorsoCompresso}";
        exec($comando);

        // Copia anche il file compresso su NAS
        $this->copiasuNAS($percorsoRelativo . '.gz');
    }

    /**
     * Ottieni nome mese
     */
    private function getNomeMese($mese)
    {
        $mesi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo',
            4 => 'Aprile', 5 => 'Maggio', 6 => 'Giugno',
            7 => 'Luglio', 8 => 'Agosto', 9 => 'Settembre',
            10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];

        return sprintf('%02d', $mese) . '-' . $mesi[$mese];
    }

    /**
     * Pulisci vecchi backup database (mantieni solo ultimi X giorni)
     */
    public function pulisciVecchiBackup($giorniDaMantenere = 30)
    {
        $percorso = $this->localPath . '/Database/Backup_giornalieri';
        $percorsoNAS = $this->nasPath . '/Database/Backup_giornalieri';

        $dataLimite = Carbon::now()->subDays($giorniDaMantenere);

        // Pulisci locale
        if (is_dir($percorso)) {
            $files = glob($percorso . '/database_backup_*.sql*');
            foreach ($files as $file) {
                if (filemtime($file) < $dataLimite->timestamp) {
                    unlink($file);
                    Log::info("Rimosso vecchio backup: " . basename($file));
                }
            }
        }

        // Pulisci NAS
        if (is_dir($percorsoNAS)) {
            $files = glob($percorsoNAS . '/database_backup_*.sql*');
            foreach ($files as $file) {
                if (filemtime($file) < $dataLimite->timestamp) {
                    unlink($file);
                }
            }
        }
    }
}

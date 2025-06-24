<?php

namespace App\Console\Commands;

use App\Models\AutomazionePdf;
use App\Models\User;
use App\Models\Spesa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class InviaSpeseMensili extends Command
{
    protected $signature = 'spese:invia-mensili {--force : Forza l\'invio anche se non è il momento programmato}';

    protected $description = 'Invia automaticamente i PDF delle spese mensili via email';

    public function handle()
    {
        $this->info('🚀 Avvio controllo automazione PDF spese mensili...');

        $configurazioni = AutomazionePdf::where('attiva', true)->get();

        if ($configurazioni->isEmpty()) {
            $this->warn('⚠️  Nessuna configurazione automazione attiva trovata.');
            return 0;
        }

        foreach ($configurazioni as $config) {
            $this->processAutomazione($config);
        }

        $this->info('✅ Controllo automazione completato!');
        return 0;
    }

    private function processAutomazione(AutomazionePdf $config)
    {
        $force = $this->option('force');

        if (!$force && !$config->shouldExecuteNow()) {
            $this->line("⏭️  Configurazione ID {$config->id}: Non è il momento di eseguire");
            return;
        }

        $this->info("📧 Processando configurazione ID {$config->id}...");

        // Calcola mese precedente
        $meseScorso = now();
        $anno = $meseScorso->year;
        $mese = $meseScorso->month;

        $mesiNomi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];

        $meseNome = $mesiNomi[$mese];

        // Determina utenti da processare
        $utenti = $this->getUtentiDaProcessare($config);

        $tuttiPdfGenerati = [];
        $errori = [];

        foreach ($utenti as $user) {
            try {
                $pdfPaths = $this->generaPdfPerUtenteConSplit($user, $anno, $mese, $meseNome, $config);

                foreach ($pdfPaths as $pdfData) {
                    $tuttiPdfGenerati[] = $pdfData;
                    $this->line("  ✅ PDF generato: {$pdfData['nome']} ({$pdfData['dimensione']} MB)");
                }

            } catch (\Exception $e) {
                $errori[] = "Errore per {$user->name}: " . $e->getMessage();
                $this->error("  ❌ Errore per {$user->name}: " . $e->getMessage());
            }
        }

        // Invia email multiple se necessario
        if (!empty($tuttiPdfGenerati)) {
            $this->inviaEmailMultiple($config, $tuttiPdfGenerati, $meseNome, $anno);
        }

        // Aggiorna log esecuzione
        $config->update([
            'ultima_esecuzione' => now(),
            'ultimo_risultato' => [
                'pdf_generati' => count($tuttiPdfGenerati),
                'utenti_processati' => count($utenti),
                'errori' => $errori,
                'timestamp' => now()->toISOString()
            ]
        ]);

        $this->info("📊 Risultato: " . count($tuttiPdfGenerati) . " PDF generati per " . count($utenti) . " utenti");
    }

    private function getUtentiDaProcessare(AutomazionePdf $config)
    {
        $query = User::where('role', 'user');

        if ($config->utenti_inclusi) {
            $query->whereIn('id', $config->utenti_inclusi);
        }

        $utenti = $query->get();

        if ($config->solo_con_spese) {
            $meseScorso = now();
            $utenti = $utenti->filter(function ($user) use ($meseScorso) {
                return Spesa::where('user_id', $user->id)
                    ->where('anno', $meseScorso->year)
                    ->where('mese', $meseScorso->month)
                    ->exists();
            });
        }

        return $utenti;
    }

    private function generaPdfPerUtenteConSplit($user, $anno, $mese, $meseNome, $config)
    {
        $spese = Spesa::where('user_id', $user->id)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->orderBy('created_at')
            ->get();

        if ($spese->isEmpty() && $config->solo_con_spese) {
            return [];
        }

        $pdfGenerati = [];
        $maxSizeBytes = 20 * 1024 * 1024; // 20MB
        $currentGroup = [];
        $groupNumber = 1;

        foreach ($spese as $spesa) {
            $currentGroup[] = $spesa;

            // Genera PDF di test per questo gruppo
            $testPdf = $this->generaPdfPerGruppo($user, $currentGroup, $anno, $mese, $meseNome, $groupNumber);
            $testSize = strlen($testPdf);

            // Se supera 20MB o ha raggiunto 3 spese, finalizza il gruppo
            if ($testSize > $maxSizeBytes ) {
                // Se il gruppo ha solo 1 spesa e supera già 20MB, lo salva comunque
                if (count($currentGroup) == 1 || $testSize <= $maxSizeBytes) {
                    $fileName = $this->salvaGruppoPdf($user, $currentGroup, $anno, $mese, $groupNumber, $testPdf);
                    $sizeMB = round($testSize / (1024 * 1024), 1);
                    
                    $pdfGenerati[] = [
                        'utente' => $user->name,
                        'path' => $fileName,
                        'nome' => "Spese {$meseNome} {$anno} - Parte {$groupNumber}",
                        'dimensione' => $sizeMB,
                        'spese_count' => count($currentGroup),
                        'gruppo' => $groupNumber
                    ];
                    
                    $currentGroup = [];
                    $groupNumber++;
                } else {
                    // Rimuovi l'ultima spesa e finalizza il gruppo precedente
                    $ultimaSpesa = array_pop($currentGroup);
                    
                    if (!empty($currentGroup)) {
                        $finalPdf = $this->generaPdfPerGruppo($user, $currentGroup, $anno, $mese, $meseNome, $groupNumber);
                        $fileName = $this->salvaGruppoPdf($user, $currentGroup, $anno, $mese, $groupNumber, $finalPdf);
                        $sizeMB = round(strlen($finalPdf) / (1024 * 1024), 1);
                        
                        $pdfGenerati[] = [
                            'utente' => $user->name,
                            'path' => $fileName,
                            'nome' => "Spese {$meseNome} {$anno} - Parte {$groupNumber}",
                            'dimensione' => $sizeMB,
                            'spese_count' => count($currentGroup),
                            'gruppo' => $groupNumber
                        ];
                        
                        $groupNumber++;
                    }
                    
                    // Inizia nuovo gruppo con l'ultima spesa
                    $currentGroup = [$ultimaSpesa];
                }
            }
        }

        // Finalizza l'ultimo gruppo se non vuoto
        if (!empty($currentGroup)) {
            $finalPdf = $this->generaPdfPerGruppo($user, $currentGroup, $anno, $mese, $meseNome, $groupNumber);
            $fileName = $this->salvaGruppoPdf($user, $currentGroup, $anno, $mese, $groupNumber, $finalPdf);
            $sizeMB = round(strlen($finalPdf) / (1024 * 1024), 1);
            
            $pdfGenerati[] = [
                'utente' => $user->name,
                'path' => $fileName,
                'nome' => "Spese {$meseNome} {$anno} - Parte {$groupNumber}",
                'dimensione' => $sizeMB,
                'spese_count' => count($currentGroup),
                'gruppo' => $groupNumber
            ];
        }

        return $pdfGenerati;
    }

    private function generaPdfPerGruppo($user, $spese, $anno, $mese, $meseNome, $gruppo)
    {
        $pdf = Pdf::loadView('pdf.spese-mensili', compact(
            'user', 'spese', 'anno', 'mese', 'meseNome'
        ));

        $pdf->setPaper('A4', 'portrait');
        return $pdf->output();
    }

    private function salvaGruppoPdf($user, $spese, $anno, $mese, $gruppo, $pdfContent)
    {
        $fileName = sprintf(
            'spese_%s_%02d_%d_parte_%d.pdf',
            str_replace(['@', '.'], '_', strtolower($user->name)),
            $mese,
            $anno,
            $gruppo
        );

        $filePath = "pdf/spese/automazione/{$fileName}";
        Storage::disk('public')->put($filePath, $pdfContent);

        return $filePath;
    }

    private function inviaEmailMultiple($config, $pdfGenerati, $meseNome, $anno)
    {
        // Raggruppa per utente
        $pdfPerUtente = [];
        foreach ($pdfGenerati as $pdf) {
            $pdfPerUtente[$pdf['utente']][] = $pdf;
        }

        foreach ($pdfPerUtente as $nomeUtente => $pdfUtente) {
            $totalePart = count($pdfUtente);
            
            foreach ($pdfUtente as $index => $pdf) {
                $numeroParte = $index + 1;
                $oggetto = str_replace(
                    ['{mese}', '{anno}'],
                    [$meseNome, $anno],
                    $config->email_oggetto
                );
                
                if ($totalePart > 1) {
                    $oggetto .= " - Parte {$numeroParte} di {$totalePart}";
                }

                $this->info("📧 Invio email: {$oggetto}");
                $this->info("📧 PDF: {$pdf['nome']} ({$pdf['dimensione']} MB)");

                try {
                    Mail::send('emails.spese-mensili', [
                        'pdfGenerati' => [$pdf],
                        'meseNome' => $meseNome,
                        'anno' => $anno,
                        'messaggio' => $config->email_messaggio ?? 'In allegato trovate il riepilogo delle spese mensili.',
                        'parte' => $numeroParte,
                        'totaleParti' => $totalePart
                    ], function ($message) use ($config, $oggetto, $pdf) {
                        $message->to($config->email_destinatari)
                                ->subject($oggetto);
                        
                        $filePath = Storage::disk('public')->path($pdf['path']);
                        if (file_exists($filePath)) {
                            $message->attach($filePath, [
                                'as' => basename($pdf['path']),
                                'mime' => 'application/pdf'
                            ]);
                        }
                    });
                    
                    $this->info("✅ Email parte {$numeroParte}/{$totalePart} inviata con successo!");
                    
                } catch (\Exception $e) {
                    $this->error("❌ Errore invio email parte {$numeroParte}: " . $e->getMessage());
                }
            }
        }
    }
}

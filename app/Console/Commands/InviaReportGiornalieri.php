<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AutomazioneReport;
use App\Models\Report;
use App\Services\ReportGeneratorService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class InviaReportGiornalieri extends Command
{
    protected $signature = 'report:invia-giornalieri {--force : Forza l\'invio ignorando le condizioni}';

    protected $description = 'Invia i report giornalieri via email secondo le configurazioni impostate';

    protected $reportGenerator;

    public function __construct(ReportGeneratorService $reportGenerator)
    {
        parent::__construct();
        $this->reportGenerator = $reportGenerator;
    }

    public function handle()
    {
        $this->info('🚀 Avvio invio report giornalieri...');

        // Trova tutte le configurazioni attive
        $automazioni = AutomazioneReport::where('attivo', true)->get();

        if ($automazioni->isEmpty()) {
            $this->warn('⚠️  Nessuna automazione attiva trovata.');
            return Command::SUCCESS;
        }

        $this->info("📋 Trovate {$automazioni->count()} automazioni attive");

        $invii_effettuati = 0;

        foreach ($automazioni as $automazione) {
            try {
                if ($this->option('force') || $automazione->shouldSendNow()) {
                    $this->line("📧 Elaborando: {$automazione->nome}");
                    
                    if ($this->inviaReportPerAutomazione($automazione)) {
                        $invii_effettuati++;
                        $automazione->markAsSent();
                        $this->info("✅ Inviato con successo: {$automazione->nome}");
                    } else {
                        $this->error("❌ Errore nell'invio: {$automazione->nome}");
                    }
                } else {
                    $this->line("⏭️  Saltando: {$automazione->nome} (non è il momento giusto)");
                }
            } catch (\Exception $e) {
                $this->error("❌ Errore per {$automazione->nome}: " . $e->getMessage());
                Log::error("Errore invio report automazione {$automazione->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("🎉 Processo completato. Report inviati: {$invii_effettuati}");

        return Command::SUCCESS;
    }

    private function inviaReportPerAutomazione(AutomazioneReport $automazione): bool
    {
        try {
            // Ottieni i report di ieri (o oggi se force)
            $data_report = $this->option('force') ? now() : yesterday();
            
            $this->line("📅 Cercando report per il {$data_report->format('d/m/Y')}");

            // SEMPRE tutti i report del giorno - nessuna selezione
            $reports = Report::with(['user', 'committente', 'cliente', 'commessa'])
                ->whereDate('data', $data_report)
                ->orderBy('user_id')
                ->orderBy('created_at')
                ->get();

            if ($reports->isEmpty()) {
                $this->warn("⚠️  Nessun report trovato per il {$data_report->format('d/m/Y')}");
                return false;
            }

            $this->line("📊 Trovati {$reports->count()} report");

            // Se raggruppamento attivo: UNA email con tutti i report
            if ($automazione->raggruppa_per_giorno) {
                return $this->inviaEmailRaggruppata($automazione, $reports, $data_report);
            } else {
                // Altrimenti: email separate per ogni utente
                return $this->inviaEmailSeparate($automazione, $reports, $data_report);
            }

        } catch (\Exception $e) {
            Log::error("Errore in inviaReportPerAutomazione", [
                'automazione_id' => $automazione->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function inviaEmailRaggruppata(AutomazioneReport $automazione, $reports, Carbon $data): bool
    {
        try {
            // Passa direttamente i report invece di generare HTML
            $this->inviaEmail($automazione, ['reports' => $reports], $data, 'TUTTI I TECNICI');
            return true;
        } catch (\Exception $e) {
            Log::error("Errore inviaEmailRaggruppata", ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function inviaEmailSeparate(AutomazioneReport $automazione, $reports, Carbon $data): bool
    {
        try {
            // Raggruppa per utente
            $reportPerUtente = $reports->groupBy('user_id');

            foreach ($reportPerUtente as $userId => $reportsUtente) {
                $nomeUtente = $reportsUtente->first()->user->name;
                
                // Invia email separata per questo utente con i suoi report
                $this->inviaEmail($automazione, ['reports' => $reportsUtente], $data, $nomeUtente);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Errore inviaEmailSeparate", ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function generaPdfReport($reports, AutomazioneReport $automazione, Carbon $data, string $identificativo, string $lingua): array
    {
        try {
            // Genera contenuti AI per ogni report
            $reportContent = [];
            foreach ($reports as $report) {
                if (!empty($report->descrizione_lavori)) {
                    try {
                        $result = $this->reportGenerator->generateSingleLanguageReport(
                            $report, 
                            $lingua, 
                            $automazione->includi_dettagli_ore, 
                            $automazione->prompt_personalizzato
                        );
                        
                        if ($result['success'] && !empty($result['content'])) {
                            $reportContent[$report->id] = $result['content'];
                        }
                    } catch (\Exception $e) {
                        \Log::error("Errore generazione report AI per report {$report->id}: " . $e->getMessage());
                    }
                }
            }

            // Cerca template attivo per i report
            $template = \App\Models\PdfTemplate::where('tipo', 'report')
                ->where('attivo', true)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($template) {
                // Usa template dal database
                $html = $template->renderizzaTemplate([
                    'reports' => $reports,
                    'data' => $data,
                    'identificativo' => $identificativo,
                    'includi_dettagli_ore' => $automazione->includi_dettagli_ore,
                    'reportContent' => $reportContent,
                    'lingua' => $lingua
                ]);
                
                $pdf = Pdf::loadHTML($html);
                $pdf->setPaper($template->formato_pagina, $template->orientamento);
                
                if ($template->margini) {
                    $pdf->setOption('margin_top', $template->margini['top'] ?? 20);
                    $pdf->setOption('margin_right', $template->margini['right'] ?? 20);
                    $pdf->setOption('margin_bottom', $template->margini['bottom'] ?? 20);
                    $pdf->setOption('margin_left', $template->margini['left'] ?? 20);
                }
            } else {
                // Fallback al template file
                $pdf = Pdf::loadView('pdf.report-giornalieri', [
                    'reports' => $reports,
                    'data' => $data,
                    'identificativo' => $identificativo,
                    'includi_dettagli_ore' => $automazione->includi_dettagli_ore,
                    'reportContent' => $reportContent,
                    'lingua' => $lingua
                ]);
                $pdf->setPaper('A4', 'portrait');
            }
            
            // Nome file
            $nomeFile = sprintf(
                'report_%s_%s_%s.pdf',
                $data->format('Y-m-d'),
                \Str::slug($identificativo),
                $lingua
            );

            return [
                'success' => true,
                'content' => $pdf->output(),
                'filename' => $nomeFile
            ];

        } catch (\Exception $e) {
            \Log::error("Errore generazione PDF report: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    private function inviaEmail(AutomazioneReport $automazione, array $contenuti, Carbon $data, string $identificativo): void
    {
        $oggetto = "📋 Report VLD Service - {$data->format('d/m/Y')}";
        if ($identificativo !== 'TUTTI I TECNICI') {
            $oggetto .= " - {$identificativo}";
        }

        // Prepara allegati PDF per ogni lingua
        $allegati = [];
        foreach ($automazione->lingue as $lingua) {
            $pdfResult = $this->generaPdfReport(
                $contenuti['reports'] ?? $contenuti, // gestisce sia report raggruppati che separati
                $automazione,
                $data,
                $identificativo,
                $lingua
            );

            if ($pdfResult['success']) {
                $allegati[] = [
                    'content' => $pdfResult['content'],
                    'filename' => $pdfResult['filename']
                ];
            }
        }

        if (empty($allegati)) {
            $this->error("❌ Nessun PDF generato per l'invio");
            return;
        }

        // Prepara il corpo dell'email
        $corpoEmail = "<h2>Report Giornaliero VLD Service</h2>";
        $corpoEmail .= "<p>In allegato i report del <strong>{$data->format('d/m/Y')}</strong>";
        if ($identificativo !== 'TUTTI I TECNICI') {
            $corpoEmail .= " per <strong>{$identificativo}</strong>";
        }
        $corpoEmail .= ".</p>";
        
        $corpoEmail .= "<p>Report generati in: ";
        $lingueNomi = ['it' => 'Italiano', 'en' => 'English', 'de' => 'Deutsch', 'ru' => 'Русский'];
        $lingueList = [];
        foreach ($automazione->lingue as $lingua) {
            $lingueList[] = $lingueNomi[$lingua] ?? $lingua;
        }
        $corpoEmail .= implode(', ', $lingueList) . "</p>";
        
        $corpoEmail .= "<hr>";
        $corpoEmail .= "<p><small>Email inviata automaticamente da VLD Service GmbH</small></p>";

        // Invia a tutti i destinatari
        foreach ($automazione->email_destinatari as $email) {
            try {
                Mail::html($corpoEmail, function ($message) use ($email, $oggetto, $allegati) {
                    $message->to($email)
                            ->subject($oggetto)
                            ->from(config('mail.from.address'), config('mail.from.name'));
                    
                    // Aggiungi tutti i PDF come allegati
                    foreach ($allegati as $allegato) {
                        $message->attachData(
                            $allegato['content'],
                            $allegato['filename'],
                            ['mime' => 'application/pdf']
                        );
                    }
                });

                $this->line("📧 Email inviata a: {$email} con " . count($allegati) . " PDF allegati");
            } catch (\Exception $e) {
                $this->error("❌ Errore invio email a {$email}: " . $e->getMessage());
            }
        }
    }
}

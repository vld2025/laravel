<?php

namespace App\Services;

use OpenAI;
use App\Models\Report;
use Illuminate\Support\Facades\Log;

class ReportGeneratorService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('openai.api_key'));
    }

    public function generateProfessionalReport(Report $report, bool $includi_dettagli_ore = true): array
    {
        try {
            if (empty($report->descrizione_lavori)) {
                return [
                    'success' => false,
                    'error' => 'Nessuna descrizione lavori fornita'
                ];
            }

            // Prepara i dati del report
            $reportData = $this->prepareReportData($report);
            
            // Genera report in 4 lingue
            $results = [];
            $languages = [
                'it' => 'italiano',
                'en' => 'inglese', 
                'de' => 'tedesco',
                'ru' => 'russo'
            ];

            foreach ($languages as $code => $language) {
                $result = $this->generateReportInLanguage($reportData, $language, $code, $includi_dettagli_ore);
                if ($result['success']) {
                    $results[$code] = $result['content'];
                } else {
                    Log::warning("Fallito report in {$language}: " . $result['error']);
                    $results[$code] = null;
                }
            }

            return [
                'success' => true,
                'reports' => $results
            ];

        } catch (\Exception $e) {
            Log::error('Report Generator Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function prepareReportData(Report $report): array
    {
        $report->load(['user', 'committente', 'cliente', 'commessa']);
        
        return [
            'data' => $report->data->format('d/m/Y'),
            'giorno_settimana' => $this->getGiornoSettimana($report->data),
            'tecnico' => $report->user->name,
            'committente' => $report->committente->nome,
            'cliente' => $report->cliente->nome,
            'commessa' => $report->commessa->nome,
            'ore_lavorate' => $report->ore_lavorate,
            'ore_viaggio' => $report->ore_viaggio,
            'km_auto' => $report->km_auto,
            'descrizione_originale' => $report->descrizione_lavori,
            'notturno' => $report->notturno,
            'trasferta' => $report->trasferta,
            'festivo' => $report->festivo
        ];
    }

    private function generateReportInLanguage(array $data, string $language, string $code, bool $includi_dettagli_ore = true): array
    {
        try {
            $prompt = $this->buildPrompt($data, $language, $code, $includi_dettagli_ore);

            $response = $this->client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 800,
                'temperature' => 0.3
            ]);

            $content = trim($response->choices[0]->message->content);

            return [
                'success' => true,
                'content' => $content
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function buildPrompt(array $data, string $language, string $code, bool $includi_dettagli_ore = true): string
    {
        $templates = [
            'it' => "Crea un report di lavoro professionale in italiano basato sui seguenti dati:",
            'en' => "Create a professional work report in English based on the following data:",
            'de' => "Erstellen Sie einen professionellen Arbeitsbericht auf Deutsch basierend auf folgenden Daten:",
            'ru' => "Создайте профессиональный отчет о работе на русском языке на основе следующих данных:"
        ];

        $formatInstructions = [
            'it' => $includi_dettagli_ore ? "Formato: Report professionale con intestazione, dettagli lavoro svolto, ore e chilometri. Stile formale aziendale." : "Formato: Report professionale con intestazione e dettagli lavoro svolto. NON includere ore o chilometri. Stile formale aziendale.",
            'en' => $includi_dettagli_ore ? "Format: Professional report with header, work details performed, hours and kilometers. Formal business style." : "Format: Professional report with header and work details performed. DO NOT include hours or kilometers. Formal business style.",
            'de' => $includi_dettagli_ore ? "Format: Professioneller Bericht mit Kopfzeile, Arbeitsdetails, Stunden und Kilometer. Formeller Geschäftsstil." : "Format: Professioneller Bericht mit Kopfzeile und Arbeitsdetails. KEINE Stunden oder Kilometer einschließen. Formeller Geschäftsstil.",
            'ru' => $includi_dettagli_ore ? "Формат: Профессиональный отчет с заголовком, деталями выполненной работы, часами и километрами. Формальный деловой стиль." : "Формат: Профессиональный отчет с заголовком и деталями выполненной работы. НЕ включать часы или километры. Формальный деловой стиль."
        ];

        $prompt = $templates[$code] . "\n\n" .
               "Data: {$data['data']} ({$data['giorno_settimana']})\n" .
               "Tecnico: {$data['tecnico']}\n" .
               "Committente: {$data['committente']}\n" .
               "Cliente: {$data['cliente']}\n" .
               "Commessa: {$data['commessa']}\n";
        
        if ($includi_dettagli_ore) {
            $prompt .= "Ore lavorate: {$data['ore_lavorate']}h\n" .
                      "Ore viaggio: {$data['ore_viaggio']}h\n" .
                      "Chilometri: {$data['km_auto']} km\n";
        }
        
        $prompt .= "Lavoro notturno: " . ($data['notturno'] ? 'Sì' : 'No') . "\n" .
                  "Trasferta: " . ($data['trasferta'] ? 'Sì' : 'No') . "\n" .
                  "Festivo: " . ($data['festivo'] ? 'Sì' : 'No') . "\n" .
                  "Descrizione attività: {$data['descrizione_originale']}\n\n" .
                  $formatInstructions[$code] . "\n" .
                  "Rispondi SOLO con il report professionale formattato, senza introduzioni o spiegazioni.";
        
        return $prompt;
    }

    private function getGiornoSettimana($data): string
    {
        $giorni = [
            'Monday' => 'Lunedì',
            'Tuesday' => 'Martedì',
            'Wednesday' => 'Mercoledì', 
            'Thursday' => 'Giovedì',
            'Friday' => 'Venerdì',
            'Saturday' => 'Sabato',
            'Sunday' => 'Domenica'
        ];

        return $giorni[$data->format('l')] ?? $data->format('l');
    }

    public function isConfigured(): bool
    {
        return !empty(config('openai.api_key')) && config('openai.api_key') !== 'your_openai_api_key_here';
    }

    public function generateSingleLanguageReport(Report $report, string $lingua, bool $includi_dettagli_ore = true, ?string $prompt_personalizzato = null): array
    {
        try {
            if (empty($report->descrizione_lavori)) {
                return [
                    'success' => false,
                    'error' => 'Nessuna descrizione lavori fornita'
                ];
            }

            $reportData = $this->prepareReportData($report);
            
            $languageMap = [
                'it' => 'italiano',
                'en' => 'inglese', 
                'de' => 'tedesco',
                'ru' => 'russo'
            ];
            
            $language = $languageMap[$lingua] ?? 'italiano';
            
            // Se c'è un prompt personalizzato, usalo
            if (!empty($prompt_personalizzato)) {
                $prompt = $this->buildCustomPrompt($reportData, $language, $lingua, $prompt_personalizzato, $includi_dettagli_ore);
            } else {
                $prompt = $this->buildPrompt($reportData, $language, $lingua, $includi_dettagli_ore);
            }

            $response = $this->client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 800,
                'temperature' => 0.3
            ]);

            $content = trim($response->choices[0]->message->content);

            return [
                'success' => true,
                'content' => $content
            ];

        } catch (\Exception $e) {
            Log::error('Single Report Generation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function buildCustomPrompt(array $data, string $language, string $code, string $template, bool $includi_dettagli_ore): string
    {
        // Sostituisci i placeholder con i dati reali
        $replacements = [
            '{data}' => $data['data'],
            '{giorno_settimana}' => $data['giorno_settimana'],
            '{tecnico}' => $data['tecnico'],
            '{committente}' => $data['committente'],
            '{cliente}' => $data['cliente'],
            '{commessa}' => $data['commessa'],
            '{ore_lavorate}' => $data['ore_lavorate'],
            '{ore_viaggio}' => $data['ore_viaggio'],
            '{km_auto}' => $data['km_auto'],
            '{descrizione}' => $data['descrizione_originale'],
            '{notturno}' => $data['notturno'] ? 'Sì' : 'No',
            '{trasferta}' => $data['trasferta'] ? 'Sì' : 'No',
            '{festivo}' => $data['festivo'] ? 'Sì' : 'No',
        ];

        $prompt = strtr($template, $replacements);
        
        // Aggiungi istruzioni sulla lingua
        $languageInstructions = [
            'it' => "\n\nGenera il report in italiano.",
            'en' => "\n\nGenerate the report in English.",
            'de' => "\n\nErstellen Sie den Bericht auf Deutsch.",
            'ru' => "\n\nСоздайте отчет на русском языке."
        ];
        
        $prompt .= $languageInstructions[$code] ?? $languageInstructions['it'];
        
        // Aggiungi istruzioni sull'inclusione ore se necessario
        if (!$includi_dettagli_ore) {
            $prompt .= "\n\nIMPORTANTE: NON includere ore lavorate, ore viaggio o chilometri nel report.";
        }
        
        return $prompt;
    }
}

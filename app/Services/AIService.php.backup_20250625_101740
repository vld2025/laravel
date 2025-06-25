<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('openai.api_key'));
    }

    public function extractDataFromReceipt(string $filePath): array
    {
        try {
            // Usa il disk 'public' per i file caricati da Filament
            $publicDisk = Storage::disk('public');
            
            Log::info('AI Processing attempt', [
                'input_path' => $filePath,
                'file_exists' => $publicDisk->exists($filePath)
            ]);
            
            if (!$publicDisk->exists($filePath)) {
                throw new \Exception("File non trovato: {$filePath}");
            }

            $fileContent = $publicDisk->get($filePath);
            $mimeType = $publicDisk->mimeType($filePath);
            
            Log::info('AI Processing file found', [
                'mime_type' => $mimeType,
                'file_size' => strlen($fileContent)
            ]);

            // PDF handling
            if ($mimeType === 'application/pdf' || str_ends_with(strtolower($filePath), '.pdf')) {
                return [
                    'importo' => null,
                    'descrizione' => 'PDF caricato - elaborazione manuale richiesta',
                    'success' => true,
                    'error' => null
                ];
            }
            
            // Image processing
            $base64 = base64_encode($fileContent);
            
            $supportedMimeTypes = [
                'image/jpeg' => 'image/jpeg',
                'image/jpg' => 'image/jpeg', 
                'image/png' => 'image/png',
                'image/gif' => 'image/gif',
                'image/webp' => 'image/webp'
            ];

            if (!isset($supportedMimeTypes[$mimeType])) {
                $imageInfo = @getimagesizefromstring($fileContent);
                if ($imageInfo !== false) {
                    $mimeType = $imageInfo['mime'];
                    Log::info('MIME corrected', ['new_mime' => $mimeType]);
                }
            }

            if (!isset($supportedMimeTypes[$mimeType])) {
                throw new \Exception("Tipo file non supportato: {$mimeType}");
            }

            $actualMimeType = $supportedMimeTypes[$mimeType];
            
            Log::info('Sending to OpenAI', ['mime_type' => $actualMimeType]);
            
            $response = $this->client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analizza questa ricevuta e estrai JSON: {"importo": numero_decimale, "descrizione": "descrizione_italiana_breve"}. Solo JSON, nient\'altro.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$actualMimeType};base64,{$base64}"
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 300,
                'temperature' => 0.1
            ]);
            
            $content = trim($response->choices[0]->message->content);
            $content = preg_replace('/```json\s*/', '', $content);
            $content = preg_replace('/```\s*$/', '', $content);
            
            Log::info('OpenAI Response', ['content' => $content]);
            
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Risposta AI non valida: ' . $content);
            }
            
            $result = [
                'importo' => isset($data['importo']) && is_numeric($data['importo']) ? (float)$data['importo'] : null,
                'descrizione' => $data['descrizione'] ?? null,
                'success' => true,
                'raw_response' => $content
            ];
            
            Log::info('AI Processing successful', $result);
            return $result;
            
        } catch (\Exception $e) {
            Log::error('AI Service Error: ' . $e->getMessage());
            
            return [
                'importo' => null,
                'descrizione' => 'Errore AI: ' . $e->getMessage(),
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function isConfigured(): bool
    {
        return !empty(config('openai.api_key')) && config('openai.api_key') !== 'your_openai_api_key_here';
    }
}

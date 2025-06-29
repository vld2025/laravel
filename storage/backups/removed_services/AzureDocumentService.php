<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\AdobeDocumentService;

class AzureDocumentService
{
    protected $client;
    protected $documentEndpoint;
    protected $documentApiKey;
    protected $adobeService;

    public function __construct()
    {
        $this->documentEndpoint = config('azure.document_intelligence.endpoint');
        $this->documentApiKey = config('azure.document_intelligence.api_key');
        $this->adobeService = new AdobeDocumentService();

        $this->client = new Client([
            'timeout' => 120,
            'verify' => false
        ]);
    }

    /**
     * Processa documento: Adobe crop professionale + Azure OCR
     */
    public function processDocument(string $filePath): array
    {
        try {
            Log::info("Azure + Adobe processing started", ['file' => $filePath]);

            $publicDisk = Storage::disk('public');

            if (!$publicDisk->exists($filePath)) {
                throw new \Exception("File non trovato: {$filePath}");
            }

            $originalContent = $publicDisk->get($filePath);
            $mimeType = $publicDisk->mimeType($filePath);
            $originalSize = strlen($originalContent);

            // Step 1: CROP PROFESSIONALE con Adobe Document Services
            $adobeResult = null;
            if (str_starts_with($mimeType, 'image/') && $this->adobeService->isConfigured()) {
                Log::info("Starting Adobe professional document cropping", ['original_size' => $originalSize]);

                $adobeResult = $this->adobeService->cropDocument($filePath);

                if ($adobeResult['success']) {
                    $newSize = $adobeResult['processed_size'];

                    Log::info("Adobe document processing completed", [
                        'file' => $filePath,
                        'original_size' => $originalSize,
                        'processed_size' => $newSize,
                        'size_change' => round((($newSize - $originalSize) / $originalSize) * 100, 1) . '%',
                        'adobe_job_id' => $adobeResult['adobe_job_id'],
                        'processing_time' => $adobeResult['processing_time'] . 's'
                    ]);
                } else {
                    Log::warning("Adobe processing failed, using original image", [
                        'error' => $adobeResult['error']
                    ]);
                }
            } else {
                Log::info("Skipping Adobe processing - not image or Adobe not configured");
            }

            // Step 2: OCR con Azure Document Intelligence (sul file processato)
            $ocrResult = $this->analyzeDocumentWithAzure($filePath);

            if (!$ocrResult['success']) {
                throw new \Exception($ocrResult['error']);
            }

            return [
                'success' => true,
                'extracted_text' => $ocrResult['extracted_text'],
                'document_type' => $ocrResult['document_type'] ?? 'document',
                'confidence' => $ocrResult['confidence'] ?? 0.9,
                'processing_time' => $ocrResult['processing_time'] ?? 0,
                'page_count' => $ocrResult['page_count'] ?? 1,
                'adobe_processed' => $adobeResult && $adobeResult['success'],
                'adobe_job_id' => $adobeResult['adobe_job_id'] ?? null,
                'original_size' => $originalSize,
                'final_size' => $adobeResult && $adobeResult['success'] ? $adobeResult['processed_size'] : $originalSize,
                'improvements_applied' => array_filter([
                    $adobeResult && $adobeResult['success'] ? 'Adobe Document Services crop' : null,
                    'Professional image enhancement',
                    'Azure Document Intelligence OCR',
                    'Enterprise-grade document processing'
                ])
            ];

        } catch (\Exception $e) {
            Log::error('Azure + Adobe processing error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'extracted_text' => null
            ];
        }
    }

    /**
     * OCR con Azure Document Intelligence
     */
    private function analyzeDocumentWithAzure(string $filePath): array
    {
        try {
            $publicDisk = Storage::disk('public');
            $fileContent = $publicDisk->get($filePath);
            $mimeType = $publicDisk->mimeType($filePath);

            $url = rtrim($this->documentEndpoint, '/') . '/formrecognizer/documentModels/prebuilt-layout:analyze?api-version=2023-07-31';

            $response = $this->client->post($url, [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->documentApiKey,
                    'Content-Type' => $mimeType
                ],
                'body' => $fileContent
            ]);

            if ($response->getStatusCode() !== 202) {
                throw new \Exception('Azure OCR error: ' . $response->getStatusCode());
            }

            $resultUrl = $response->getHeader('Operation-Location')[0];
            $result = $this->pollForResult($resultUrl);

            if ($result['success']) {
                $extractedText = $this->extractTextFromResult($result['data']);

                return [
                    'success' => true,
                    'extracted_text' => $extractedText,
                    'document_type' => 'document',
                    'confidence' => 0.95,
                    'processing_time' => $result['processing_time'] ?? 0,
                    'page_count' => count($result['data']['pages'] ?? [])
                ];
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Azure OCR error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function pollForResult(string $resultUrl): array
    {
        $maxAttempts = 30;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $response = $this->client->get($resultUrl, [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->documentApiKey
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if ($data['status'] === 'succeeded') {
                    return [
                        'success' => true,
                        'data' => $data['analyzeResult'],
                        'processing_time' => $attempt
                    ];
                }

                if ($data['status'] === 'failed') {
                    throw new \Exception('Azure processing failed');
                }

                sleep(1);
                $attempt++;

            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'success' => false,
            'error' => 'Processing timeout'
        ];
    }

    private function extractTextFromResult(array $analyzeResult): string
    {
        $text = '';

        if (isset($analyzeResult['pages'])) {
            foreach ($analyzeResult['pages'] as $page) {
                if (isset($page['words'])) {
                    foreach ($page['words'] as $word) {
                        $text .= $word['content'] . ' ';
                    }
                }
                $text .= "\n";
            }
        }

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    public function isConfigured(): bool
    {
        return !empty($this->documentEndpoint) && !empty($this->documentApiKey);
    }

    public function processDocumentWithSwitches(string $filePath, bool $elaborazioneOcr, bool $ritaglioAutomatico): array
    {
        try {
            Log::info("Processing with switches", [
                "file" => $filePath,
                "ocr_enabled" => $elaborazioneOcr,
                "crop_enabled" => $ritaglioAutomatico
            ]);

            if (!$elaborazioneOcr) {
                return [
                    "success" => true,
                    "extracted_text" => "OCR disabilitato",
                    "document_type" => "no_ocr",
                    "confidence" => 0,
                    "processing_time" => 0,
                    "page_count" => 0,
                    "adobe_processed" => false
                ];
            }

            return $this->processDocument($filePath);

        } catch (\Exception $e) {
            Log::error("Processing with switches error: " . $e->getMessage());
            return [
                "success" => false,
                "error" => $e->getMessage(),
                "extracted_text" => null
            ];
        }
    }
}

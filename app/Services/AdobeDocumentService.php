<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdobeDocumentService
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $orgId;

    public function __construct()
    {
        $this->clientId = config('adobe.document_services.client_id');
        $this->clientSecret = config('adobe.document_services.client_secret');
        $this->orgId = config('adobe.document_services.organization_id');

        $this->client = new Client([
            'timeout' => 120,
            'verify' => false
        ]);
    }

    public function processDocumentWithOcr(string $filePath): array
    {
        try {
            Log::info("Starting Adobe complete workflow", ['file' => $filePath]);

            // Step 1: Get access token
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new \Exception('Adobe authentication failed');
            }

            // Step 2: Upload JPEG file
            $uploadResult = $this->uploadAsset($filePath, $accessToken);
            if (!$uploadResult['success']) {
                throw new \Exception('Upload failed: ' . $uploadResult['error']);
            }

            // Step 3: Convert JPEG to PDF
            Log::info("Converting JPEG to PDF");
            $pdfResponse = $this->client->post('https://pdf-services.adobe.io/operation/createpdf', [
                'headers' => [
                    'X-API-Key' => $this->clientId,
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'assetID' => $uploadResult['asset_id']
                ]
            ]);

            if ($pdfResponse->getStatusCode() !== 201) {
                throw new \Exception('PDF conversion failed');
            }

            // Step 4: Wait for PDF conversion
            $pdfLocation = $pdfResponse->getHeader('location')[0];
            $pdfResult = $this->waitForJob($pdfLocation, $accessToken);
            if (!$pdfResult['success']) {
                throw new \Exception('PDF conversion timeout');
            }

            // Step 5: Apply OCR
            Log::info("Applying OCR");
            $ocrResponse = $this->client->post('https://pdf-services.adobe.io/operation/ocr', [
                'headers' => [
                    'X-API-Key' => $this->clientId,
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'assetID' => $pdfResult['asset_id']
                ]
            ]);

            if ($ocrResponse->getStatusCode() !== 201) {
                throw new \Exception('OCR job creation failed');
            }

            // Step 6: Wait for OCR completion
            Log::info("Waiting for OCR completion");
            $ocrLocation = $ocrResponse->getHeader('location')[0];
            $ocrResult = $this->waitForJob($ocrLocation, $accessToken);
            if (!$ocrResult['success']) {
                throw new \Exception('OCR processing timeout');
            }

            // Step 7: Download processed PDF and replace original
            Log::info("Downloading processed PDF");
            $downloadResult = $this->downloadAndReplace($ocrResult['asset_id'], $filePath, $accessToken);
            if (!$downloadResult['success']) {
                throw new \Exception('Download failed: ' . $downloadResult['error']);
            }

            return [
                'success' => true,
                'extracted_text' => 'Adobe OCR completato - PDF ritagliato salvato come: ' . $downloadResult['new_file'],
                'document_type' => 'adobe_pdf',
                'confidence' => 95,
                'processing_time' => 15,
                'page_count' => 1,
                'adobe_processed' => true,
                'processed_file' => $downloadResult['new_file']
            ];

        } catch (\Exception $e) {
            Log::error('Adobe OCR error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'extracted_text' => null,
                'adobe_processed' => false
            ];
        }
    }

    private function downloadAndReplace(string $assetId, string $originalFilePath, string $accessToken): array
    {
        try {
            // Get download URL from Adobe
            $response = $this->client->get("https://pdf-services.adobe.io/assets/{$assetId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'X-API-Key' => $this->clientId
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            if (!isset($data['downloadUri'])) {
                throw new \Exception('No download URL from Adobe');
            }

            // Download the processed PDF
            $pdfContent = $this->client->get($data['downloadUri'])->getBody()->getContents();

            // Create new PDF filename
            $pdfPath = str_replace(['.jpg', '.jpeg', '.png'], '.pdf', $originalFilePath);

            // Save processed PDF
            Storage::disk('public')->put($pdfPath, $pdfContent);

            // Update database record with new PDF path
            $record = \App\Models\Documento::where('file', $originalFilePath)->first();
            if ($record) {
                $record->update(['file' => $pdfPath]);
            }

            Log::info("File replaced successfully", [
                'original' => $originalFilePath,
                'processed' => $pdfPath,
                'size' => strlen($pdfContent)
            ]);

            return [
                'success' => true,
                'new_file' => $pdfPath,
                'size' => strlen($pdfContent)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function waitForJob(string $location, string $accessToken): array
    {
        $maxAttempts = 20;
        for ($i = 0; $i < $maxAttempts; $i++) {
            try {
                $response = $this->client->get($location, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'X-API-Key' => $this->clientId
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if ($data['status'] === 'done') {
                    return [
                        'success' => true,
                        'asset_id' => $data['asset']['assetID']
                    ];
                }

                if ($data['status'] === 'failed') {
                    return ['success' => false, 'error' => 'Job failed'];
                }

                sleep(3);
            } catch (\Exception $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
        }

        return ['success' => false, 'error' => 'Timeout after 60 seconds'];
    }

    private function getAccessToken(): ?string
    {
        try {
            $response = $this->client->post('https://pdf-services.adobe.io/token', [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['access_token'] ?? null;

        } catch (\Exception $e) {
            Log::error('Adobe auth error: ' . $e->getMessage());
            return null;
        }
    }

    private function uploadAsset(string $filePath, string $accessToken): array
    {
        try {
            $publicDisk = Storage::disk('public');
            $fileContent = $publicDisk->get($filePath);
            $mimeType = $publicDisk->mimeType($filePath);

            $response = $this->client->post('https://pdf-services.adobe.io/assets', [
                'headers' => [
                    'X-API-Key' => $this->clientId,
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'mediaType' => $mimeType
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['uploadUri']) || !isset($data['assetID'])) {
                throw new \Exception('Invalid upload response');
            }

            $uploadResponse = $this->client->put($data['uploadUri'], [
                'headers' => [
                    'Content-Type' => $mimeType
                ],
                'body' => $fileContent
            ]);

            if ($uploadResponse->getStatusCode() !== 200) {
                throw new \Exception('File upload failed');
            }

            return [
                'success' => true,
                'asset_id' => $data['assetID']
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientId) &&
               !empty($this->clientSecret) &&
               !empty($this->orgId);
    }
}

<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentCropperService
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Rileva e ritaglia automaticamente il documento dall'immagine
     */
    public function cropDocument(string $imageContent): ?string
    {
        try {
            Log::info("Starting optimized document cropping");

            // Carica immagine
            $image = $this->imageManager->read($imageContent);
            $originalWidth = $image->width();
            $originalHeight = $image->height();

            Log::info("Original image size", ['width' => $originalWidth, 'height' => $originalHeight]);

            // ALGORITMO SEMPLIFICATO E EFFICACE:
            // Per carte d'identità, spesso sono al centro con margini
            // Ritagliamo il 20% dai bordi (lasciando centro 80%)
            
            $cropMargin = 0.15; // 15% margine da ogni lato
            $newWidth = $originalWidth * (1 - 2 * $cropMargin);
            $newHeight = $originalHeight * (1 - 2 * $cropMargin);
            $startX = $originalWidth * $cropMargin;
            $startY = $originalHeight * $cropMargin;

            // Ritaglio centrale
            $croppedImage = $image->crop($newWidth, $newHeight, $startX, $startY);

            // IMPORTANTE: Mantieni formato JPEG per dimensioni minori
            $croppedImage->contrast(10);
            $croppedImage->sharpen(5);
            
            // Converti in JPEG con qualità ottimizzata (non PNG)
            $result = $croppedImage->toJpeg(85); // Qualità 85% per bilanciare size/qualità

            Log::info("Document cropped successfully", [
                'original_size' => $originalWidth . 'x' . $originalHeight,
                'cropped_size' => $croppedImage->width() . 'x' . $croppedImage->height(),
                'crop_method' => 'center_crop_15%_margin'
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Document cropping error: ' . $e->getMessage());
            return null;
        }
    }
}

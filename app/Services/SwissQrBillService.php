<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class SwissQrBillService
{
    public function generateQrBill(array $data): string
    {
        // Costruzione del payload Swiss QR Bill secondo ISO 20022
        $qrData = $this->buildQrPayload($data);
        
        // Generazione del QR Code
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($qrData)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(543) // Dimensione standard Swiss QR Bill (543x543 pixel)
            ->margin(0)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        return base64_encode($result->getString());
    }

    private function buildQrPayload(array $data): string
    {
        // Formato Swiss QR Bill secondo ISO 20022
        $lines = [
            'SPC',                           // QR Type
            '0200',                          // Version
            '1',                             // Coding Type
            $data['iban'] ?? '',             // IBAN
            'K',                             // Creditor Address Type (K=Combined)
            $data['creditor_name'] ?? '',    // Creditor Name
            $data['creditor_address'] ?? '', // Creditor Address
            $data['creditor_postal_code'] ?? '', // Creditor Postal Code
            $data['creditor_city'] ?? '',    // Creditor City
            $data['creditor_country'] ?? 'CH', // Creditor Country
            '',                              // Ultimate Creditor Address Type
            '',                              // Ultimate Creditor Name
            '',                              // Ultimate Creditor Address
            '',                              // Ultimate Creditor Postal Code
            '',                              // Ultimate Creditor City
            '',                              // Ultimate Creditor Country
            $data['amount'] ?? '',           // Amount
            $data['currency'] ?? 'CHF',      // Currency
            'K',                             // Debtor Address Type
            $data['debtor_name'] ?? '',      // Debtor Name
            $data['debtor_address'] ?? '',   // Debtor Address
            $data['debtor_postal_code'] ?? '', // Debtor Postal Code
            $data['debtor_city'] ?? '',      // Debtor City
            $data['debtor_country'] ?? 'CH', // Debtor Country
            'QRR',                           // Reference Type (QRR for QR Reference)
            $data['reference'] ?? '',        // Reference
            $data['additional_info'] ?? '',  // Additional Information
            'EPD',                           // Trailer
            $data['billing_info'] ?? ''      // Billing Information
        ];

        return implode("\r\n", $lines);
    }

    public function generateQrReference(string $customerId, string $invoiceNumber): string
    {
        // Genera un QR Reference secondo lo standard svizzero
        $reference = str_pad($customerId, 8, '0', STR_PAD_LEFT) . 
                    str_pad($invoiceNumber, 18, '0', STR_PAD_LEFT);
        
        // Calcola il check digit usando l'algoritmo modulo 10
        $checkDigit = $this->calculateCheckDigit($reference);
        
        return $reference . $checkDigit;
    }

    private function calculateCheckDigit(string $reference): string
    {
        $table = [0, 9, 4, 6, 8, 2, 7, 1, 3, 5];
        $carry = 0;
        
        for ($i = 0; $i < strlen($reference); $i++) {
            $carry = $table[($carry + intval($reference[$i])) % 10];
        }
        
        return strval((10 - $carry) % 10);
    }
}

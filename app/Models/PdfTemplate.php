<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    protected $fillable = [
        'nome',
        'descrizione',
        'tipo',
        'template_html',
        'css_personalizzato',
        'variabili_disponibili',
        'attivo',
        'orientamento',
        'formato_pagina',
        'margini'
    ];

    protected $casts = [
        'variabili_disponibili' => 'array',
        'margini' => 'array',
        'attivo' => 'boolean'
    ];

    protected $attributes = [
        'orientamento' => 'portrait',
        'formato_pagina' => 'A4',
        'tipo' => 'report',
        'margini' => '{"top": 20, "right": 20, "bottom": 20, "left": 20}'
    ];

    public static function getTipiDisponibili(): array
    {
        return [
            'report' => 'Report Giornalieri',
            'fattura' => 'Fatture',
            'spese' => 'Spese Mensili',
            'altro' => 'Altro'
        ];
    }

    public static function getOrientamenti(): array
    {
        return [
            'portrait' => 'Verticale',
            'landscape' => 'Orizzontale'
        ];
    }

    public static function getFormatiPagina(): array
    {
        return [
            'A4' => 'A4',
            'A3' => 'A3',
            'letter' => 'Letter',
            'legal' => 'Legal'
        ];
    }

    public function renderizzaTemplate(array $data = []): string
    {
        // Crea un file temporaneo per il template
        $tempPath = storage_path('app/temp_templates');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }
        
        $tempFile = $tempPath . '/' . uniqid('template_') . '.blade.php';
        file_put_contents($tempFile, $this->template_html);
        
        // Renderizza il template usando View facade
        $html = view()->file($tempFile, $data)->render();
        
        // Rimuovi il file temporaneo
        unlink($tempFile);
        
        // Aggiungi CSS personalizzato se presente
        if ($this->css_personalizzato) {
            $style = '<style type="text/css">' . $this->css_personalizzato . '</style>';
            $html = str_replace('</head>', $style . '</head>', $html);
        }
        
        return $html;
    }
}

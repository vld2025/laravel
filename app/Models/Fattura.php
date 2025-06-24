<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fattura extends Model
{
    protected $table = 'fatture';

    protected $fillable = [
        'numero',
        'committente_id',
        'data_emissione',
        'mese_riferimento',
        'anno_riferimento',
        'stato',
        'totale_ore_lavoro',
        'totale_ore_viaggio',
        'totale_km',
        'totale_pranzi',
        'totale_trasferte',
        'totale_spese_extra',
        'imponibile',
        'sconto',
        'totale',
        'dettagli',
        'pdf_path',
    ];

    protected $casts = [
        'data_emissione' => 'date',
        'totale_ore_lavoro' => 'decimal:2',
        'totale_ore_viaggio' => 'decimal:2',
        'totale_spese_extra' => 'decimal:2',
        'imponibile' => 'decimal:2',
        'sconto' => 'decimal:2',
        'totale' => 'decimal:2',
        'dettagli' => 'array',
    ];

    // Relazione
    public function committente(): BelongsTo
    {
        return $this->belongsTo(Committente::class);
    }

    // Metodi helper
    public function getStatoLabelAttribute(): string
    {
        return match($this->stato) {
            'bozza' => 'Bozza',
            'emessa' => 'Emessa',
            'pagata' => 'Pagata',
            default => $this->stato
        };
    }

    public function getPeriodoRiferimentoAttribute(): string
    {
        $mesi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];
        
        return $mesi[$this->mese_riferimento] . ' ' . $this->anno_riferimento;
    }

    public function getTotaleTempoProduttivoAttribute(): float
    {
        return $this->totale_ore_lavoro + $this->totale_ore_viaggio;
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }

    public static function generateNumero(int $anno): string
    {
        $lastNumber = static::whereYear('data_emissione', $anno)
            ->orderBy('numero', 'desc')
            ->first()?->numero;
        
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $nextNumber = (int)end($parts) + 1;
        } else {
            $nextNumber = 1;
        }
        
        return sprintf('%d-%04d', $anno, $nextNumber);
    }

    public static function getStati(): array
    {
        return [
            'bozza' => 'Bozza',
            'emessa' => 'Emessa', 
            'pagata' => 'Pagata',
        ];
    }
}

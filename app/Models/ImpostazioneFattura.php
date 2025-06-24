<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpostazioneFattura extends Model
{
    protected $table = 'impostazioni_fattura';

    protected $fillable = [
        'committente_id',
        'indirizzo_fatturazione',
        'partita_iva',
        'iban',
        'swiss_qr_bill',
        'fatturazione_automatica',
        'giorno_fatturazione',
        'email_destinatari',
        'costo_orario',
        'costo_km',
        'costo_pranzo',
        'costo_trasferta',
        'costo_fisso_intervento',
        'percentuale_notturno',
        'percentuale_festivo',
        'sconto_percentuale',
    ];

    protected $casts = [
        'swiss_qr_bill' => 'boolean',
        'fatturazione_automatica' => 'boolean',
        'email_destinatari' => 'array',
        'costo_orario' => 'decimal:2',
        'costo_km' => 'decimal:2',
        'costo_pranzo' => 'decimal:2',
        'costo_trasferta' => 'decimal:2',
        'costo_fisso_intervento' => 'decimal:2',
        'percentuale_notturno' => 'decimal:2',
        'percentuale_festivo' => 'decimal:2',
        'sconto_percentuale' => 'decimal:2',
    ];

    // Relazione
    public function committente(): BelongsTo
    {
        return $this->belongsTo(Committente::class);
    }

    // Metodi helper per calcoli fatturazione
    public function calcolaImportoOre(float $ore, bool $notturno = false, bool $festivo = false): float
    {
        $importo = $ore * $this->costo_orario;
        
        if ($notturno) {
            $importo += $importo * ($this->percentuale_notturno / 100);
        }
        
        if ($festivo) {
            $importo += $importo * ($this->percentuale_festivo / 100);
        }
        
        return $importo;
    }

    public function calcolaImportoKm(int $km): float
    {
        return $km * $this->costo_km;
    }

    public function applicaSconto(float $importo): float
    {
        if ($this->sconto_percentuale > 0) {
            return $importo - ($importo * ($this->sconto_percentuale / 100));
        }
        
        return $importo;
    }
}

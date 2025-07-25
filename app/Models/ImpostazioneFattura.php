<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpostazioneFattura extends Model
{
    use HasFactory;

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
        // Campi Swiss QR Bill
        'qr_creditor_name',
        'qr_creditor_address',
        'qr_creditor_postal_code',
        'qr_creditor_city',
        'qr_creditor_country',
        'qr_additional_info',
        'qr_billing_info',
    ];

    protected $attributes = [
        'percentuale_notturno' => 0,
        'percentuale_festivo' => 0,
        'sconto_percentuale' => 0,
        'swiss_qr_bill' => false,
        'fatturazione_automatica' => false,
        'qr_creditor_country' => 'CH',
    ];

    protected $casts = [
        'email_destinatari' => 'array',
        'swiss_qr_bill' => 'boolean',
        'fatturazione_automatica' => 'boolean',
        'costo_orario' => 'decimal:2',
        'costo_km' => 'decimal:2',
        'costo_pranzo' => 'decimal:2',
        'costo_trasferta' => 'decimal:2',
        'costo_fisso_intervento' => 'decimal:2',
        'percentuale_notturno' => 'decimal:2',
        'percentuale_festivo' => 'decimal:2',
        'sconto_percentuale' => 'decimal:2',
    ];

    public function committente(): BelongsTo
    {
        return $this->belongsTo(Committente::class);
    }
}

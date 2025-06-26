<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $table = 'report';

    protected $fillable = [
        'user_id',
        'data',
        'committente_id',
        'cliente_id',
        'commessa_id',
        'ore_lavorate',
        'ore_viaggio',
        'km_auto',
        'auto_privata',
        'notturno',
        'trasferta',
        'descrizione_lavori',
        'descrizione_it',
        'descrizione_en',
        'descrizione_de',
        'descrizione_ru',
        'ore_lavorate_fatturazione',
        'ore_viaggio_fatturazione',
        'fatturato',
        'descrizione_it',
        'descrizione_en',
        'descrizione_de',
        'descrizione_ru',
        'festivo',
    ];

    protected $casts = [
        'data' => 'date',
        'ore_lavorate' => 'decimal:1',
        'ore_viaggio' => 'decimal:1',
        'ore_lavorate_fatturazione' => 'decimal:1',
        'ore_viaggio_fatturazione' => 'decimal:1',
        'auto_privata' => 'boolean',
        'notturno' => 'boolean',
        'trasferta' => 'boolean',
        'fatturato' => 'boolean',
        'festivo' => 'boolean',
    ];

    // Relazioni
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function committente(): BelongsTo
    {
        return $this->belongsTo(Committente::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function commessa(): BelongsTo
    {
        return $this->belongsTo(Commessa::class);
    }

    // Metodi helper
    public function getTotalOreAttribute(): float
    {
        return $this->ore_lavorate + $this->ore_viaggio;
    }

    public function getTotalOreFatturazioneAttribute(): float
    {
        return ($this->ore_lavorate_fatturazione ?? $this->ore_lavorate) + 
               ($this->ore_viaggio_fatturazione ?? $this->ore_viaggio);
    }

    public function isModificabile(): bool
    {
        return !$this->fatturato;
    }

    public function canUserEdit(User $user): bool
    {
        // Admin e Manager possono sempre modificare
        if ($user->canViewAllData()) {
            return true;
        }
        
        // User può modificare solo i propri report non fatturati
        return $this->user_id === $user->id && !$this->fatturato;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpesaExtra extends Model
{
    protected $table = 'spese_extra';

    protected $fillable = [
        'user_id',
        'committente_id',
        'file',
        'importo',
        'descrizione',
        'data',
    ];

    protected $casts = [
        'data' => 'date',
        'importo' => 'decimal:2',
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

    // Metodi helper
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file);
    }

    public function getImportoFormattedAttribute(): string
    {
        return 'CHF ' . number_format($this->importo, 2, '.', '\'');
    }

    public function isProcessedByAI(): bool
    {
        return !is_null($this->importo) && !is_null($this->descrizione);
    }
}

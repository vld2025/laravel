<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spesa extends Model
{
    protected $table = 'spese';

    protected $fillable = [
        'user_id',
        'anno',
        'mese',
        'file',
        'descrizione',
    ];

    protected $casts = [
        'anno' => 'integer',
        'mese' => 'integer',
    ];

    // Relazione: Una spesa appartiene a un utente
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Metodi helper
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file);
    }

    public function getMeseAnnoAttribute(): string
    {
        return sprintf('%02d/%d', $this->mese, $this->anno);
    }

    public static function getMesi(): array
    {
        return [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];
    }
}

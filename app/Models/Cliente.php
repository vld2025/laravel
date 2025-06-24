<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clienti';

    protected $fillable = [
        'nome',
        'committente_id',
        'indirizzo',
        'dati_bancari',
        'informazioni',
    ];

    protected $casts = [
        'dati_bancari' => 'array',
    ];

    // Relazione: Un cliente appartiene a un committente
    public function committente(): BelongsTo
    {
        return $this->belongsTo(Committente::class);
    }

    // Relazione: Un cliente ha molte commesse
    public function commesse(): HasMany
    {
        return $this->hasMany(Commessa::class);
    }
}

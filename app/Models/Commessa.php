<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commessa extends Model
{
    protected $table = 'commesse';

    protected $fillable = [
        'nome',
        'descrizione',
        'cliente_id',
    ];

    // Relazione: Una commessa appartiene a un cliente
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relazione: Una commessa appartiene a un committente (attraverso il cliente)
    public function committente(): BelongsTo
    {
        return $this->belongsTo(Committente::class, 'committente_id');
    }

    // Accessor per ottenere il committente attraverso il cliente
    public function getCommittenteAttribute()
    {
        return $this->cliente?->committente;
    }
}

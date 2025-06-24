<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Committente extends Model
{
    protected $table = 'committenti';

    protected $fillable = [
        'nome',
        'partita_iva',
        'indirizzo',
        'iban',
        'logo',
        'dati_bancari',
        'informazioni',
    ];

    protected $casts = [
        'dati_bancari' => 'array',
    ];

    // Relazione: Un committente ha molti clienti
    public function clienti(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    // Relazione: Un committente ha molte commesse (attraverso i clienti)
    public function commesse()
    {
        return $this->hasManyThrough(Commessa::class, Cliente::class);
    }
}

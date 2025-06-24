<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    protected $table = 'documenti';

    protected $fillable = [
        'user_id',
        'caricato_da',
        'tipo',
        'nome',
        'file',
    ];

    // Relazioni
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function caricatoDa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caricato_da');
    }

    // Metodi helper
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file);
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'busta_paga' => 'Busta Paga',
            'personale' => 'Documento Personale',
            'aziendale' => 'Documento Aziendale',
            default => $this->tipo
        };
    }

    public function canUserAccess(User $user): bool
    {
        return match($this->tipo) {
            'busta_paga' => $this->user_id === $user->id || $user->canViewAllData(),
            'personale' => $this->user_id === $user->id || $user->canViewAllData(),
            'aziendale' => true, // tutti possono vedere documenti aziendali
            default => false
        };
    }

    public static function getTipi(): array
    {
        return [
            'busta_paga' => 'Busta Paga',
            'personale' => 'Documento Personale',
            'aziendale' => 'Documento Aziendale',
        ];
    }
}

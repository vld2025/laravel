<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AutomazioneReport extends Model
{
    use HasFactory;

    protected $table = 'automazione_reports';

    protected $fillable = [
        'nome',
        'attivo',
        'ora_invio',
        'email_destinatari',
        'lingue',
        'solo_giorni_lavorativi',
        'raggruppa_per_giorno',
        'formato_file',
        'includi_dettagli_ore',
        'prompt_personalizzato',
        'note',
        'ultimo_invio'
    ];

    protected $casts = [
        'attivo' => 'boolean',
        'email_destinatari' => 'array',
        'lingue' => 'array',
        'solo_giorni_lavorativi' => 'boolean',
        'raggruppa_per_giorno' => 'boolean',
        'includi_dettagli_ore' => 'boolean',
        'ora_invio' => 'datetime:H:i',
        'ultimo_invio' => 'datetime',
    ];

    protected $attributes = [
        'lingue' => '["it"]',
        'email_destinatari' => '[]',
        'formato_file' => 'pdf',
        'solo_giorni_lavorativi' => true,
        'raggruppa_per_giorno' => true,
        'raggruppa_per_giorno' => false,
    ];

    /**
     * Ottieni le lingue disponibili
     */
    public static function getAvailableLanguages(): array
    {
        return [
            'it' => 'Italiano',
            'en' => 'English',
            'de' => 'Deutsch',
            'ru' => 'Русский'
        ];
    }

    /**
     * Ottieni i formati file disponibili
     */
    public static function getAvailableFormats(): array
    {
        return [
            'pdf' => 'PDF',
            'excel' => 'Excel (XLSX)'
        ];
    }

    /**
     * Verifica se è ora di inviare
     */
    public function shouldSendNow(): bool
    {
        if (!$this->attivo) {
            return false;
        }

        $now = now();
        $targetTime = $now->copy()->setTimeFromTimeString($this->ora_invio->format('H:i:s'));

        // Verifica se è il momento giusto (con tolleranza di 5 minuti)
        if (abs($now->diffInMinutes($targetTime)) > 5) {
            return false;
        }

        // Verifica giorni lavorativi
        if ($this->solo_giorni_lavorativi && $now->isWeekend()) {
            return false;
        }

        // Verifica se già inviato oggi
        if ($this->ultimo_invio && $this->ultimo_invio->isToday()) {
            return false;
        }

        return true;
    }

    /**
     * Marca come inviato
     */
    public function markAsSent(): void
    {
        $this->update(['ultimo_invio' => now()]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomazionePdf extends Model
{
    protected $table = 'automazione_pdf';

    protected $fillable = [
        'attiva',
        'giorno_invio',
        'ora_invio',
        'email_destinatari',
        'email_oggetto',
        'email_messaggio',
        'ultima_esecuzione',
        'ultimo_risultato',
        'mese_riferimento',
    ];

    protected $casts = [
        'attiva' => 'boolean',
        'ora_invio' => 'datetime:H:i',
        'email_destinatari' => 'array',
        'ultima_esecuzione' => 'datetime',
        'ultimo_risultato' => 'array',
        'mese_riferimento',
    ];

    // Metodi helper
    public function getOraInvioFormattedAttribute(): string
    {
        return $this->ora_invio->format('H:i');
    }

    public function getProximaEsecuzioneAttribute(): ?\Carbon\Carbon
    {
        if (!$this->attiva) {
            return null;
        }

        $now = now();
        $proximoMese = $now->copy()->addMonth();
        
        // Calcola il prossimo giorno di invio
        $dataInvio = $proximoMese->day($this->giorno_invio);
        $dataInvio->setTimeFromTimeString($this->ora_invio->format('H:i:s'));
        
        return $dataInvio;
    }

    public function shouldExecuteNow(): bool
    {
        if (!$this->attiva) {
            return false;
        }

        $now = now();
        $targetTime = $now->copy()
            ->day($this->giorno_invio)
            ->setTimeFromTimeString($this->ora_invio->format('H:i:s'));

        // Verifica se è esattamente il momento giusto
        $currentMinute = $now->format('H:i');
        $targetMinute = $targetTime->format('H:i');
        
        return $currentMinute === $targetMinute;
    }

    public function getEmailDestinatariFormattedAttribute(): string
    {
        return implode(', ', $this->email_destinatari ?? []);
    }

    public static function getConfigurazioneDefault(): array
    {
        return [
            'attiva' => false,
            'giorno_invio' => 1,
            'ora_invio' => '09:00',
            'email_destinatari' => ['admin@vld.internet-box.ch'],
            'email_oggetto' => 'Spese Mensili VLD Service - {mese} {anno}',
            'email_messaggio' => 'In allegato trovate il riepilogo delle spese mensili con tutti i documenti allegati.',
        ];
    }
}

<?php

namespace App\Services;

use Carbon\Carbon;

class FestivoService
{
    /**
     * Giorni festivi fissi in Svizzera/Ticino
     */
    private static array $festiviFissi = [
        '01-01' => 'Capodanno',
        '01-06' => 'Epifania (Ticino)',
        '03-19' => 'San Giuseppe (Ticino)',
        '05-01' => 'Festa del Lavoro',
        '08-01' => 'Festa Nazionale Svizzera',
        '08-15' => 'Assunzione (Ticino)',
        '11-01' => 'Ognissanti (Ticino)',
        '12-08' => 'Immacolata Concezione (Ticino)',
        '12-25' => 'Natale',
        '12-26' => 'Santo Stefano'
    ];

    /**
     * Verifica se una data è festiva
     */
    public static function isFestivo(Carbon $data): bool
    {
        // Domenica è sempre festivo
        if ($data->dayOfWeek === Carbon::SUNDAY) {
            return true;
        }

        // Verifica giorni fissi
        $dataFormatted = $data->format('m-d');
        if (isset(self::$festiviFissi[$dataFormatted])) {
            return true;
        }

        // Calcola festivi mobili (Pasqua, ecc.)
        return self::isFestivoMobile($data);
    }

    /**
     * Calcola i festivi mobili (basati su Pasqua)
     */
    private static function isFestivoMobile(Carbon $data): bool
    {
        $anno = $data->year;
        
        // Calcola la Pasqua
        $pasqua = self::calcolaPasqua($anno);
        
        // Festivi basati su Pasqua
        $festiviMobili = [
            $pasqua->copy()->subDays(2),  // Venerdì Santo
            $pasqua->copy()->addDay(),    // Lunedì dell'Angelo
            $pasqua->copy()->addDays(39), // Ascensione
            $pasqua->copy()->addDays(50), // Lunedì di Pentecoste
        ];

        foreach ($festiviMobili as $festivo) {
            if ($data->isSameDay($festivo)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calcola la data di Pasqua per un anno
     */
    private static function calcolaPasqua(int $anno): Carbon
    {
        // Algoritmo di Gauss per calcolare Pasqua
        $a = $anno % 19;
        $b = intval($anno / 100);
        $c = $anno % 100;
        $d = intval($b / 4);
        $e = $b % 4;
        $f = intval(($b + 8) / 25);
        $g = intval(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intval($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intval(($a + 11 * $h + 22 * $l) / 451);
        $n = intval(($h + $l - 7 * $m + 114) / 31);
        $p = ($h + $l - 7 * $m + 114) % 31;

        return Carbon::create($anno, $n, $p + 1);
    }

    /**
     * Ottieni il nome del festivo (se è festivo)
     */
    public static function getNomeFestivo(Carbon $data): ?string
    {
        if ($data->dayOfWeek === Carbon::SUNDAY) {
            return 'Domenica';
        }

        $dataFormatted = $data->format('m-d');
        if (isset(self::$festiviFissi[$dataFormatted])) {
            return self::$festiviFissi[$dataFormatted];
        }

        // Controlla festivi mobili
        $anno = $data->year;
        $pasqua = self::calcolaPasqua($anno);
        
        if ($data->isSameDay($pasqua->copy()->subDays(2))) return 'Venerdì Santo';
        if ($data->isSameDay($pasqua->copy()->addDay())) return 'Lunedì dell\'Angelo';
        if ($data->isSameDay($pasqua->copy()->addDays(39))) return 'Ascensione';
        if ($data->isSameDay($pasqua->copy()->addDays(50))) return 'Lunedì di Pentecoste';

        return null;
    }
}

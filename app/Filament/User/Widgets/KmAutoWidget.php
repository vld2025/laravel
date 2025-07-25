<?php

namespace App\Filament\User\Widgets;

use App\Models\Report;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KmAutoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $meseCorrente = Carbon::now();
        
        // Km auto privata questo mese
        $kmAutoPrivata = Report::where('user_id', $user->id)
            ->where('auto_privata', true)
            ->whereMonth('data', $meseCorrente->month)
            ->whereYear('data', $meseCorrente->year)
            ->sum('km_auto');
            
        // Km totali (privata + aziendale)
        $kmTotali = Report::where('user_id', $user->id)
            ->whereMonth('data', $meseCorrente->month)
            ->whereYear('data', $meseCorrente->year)
            ->sum('km_auto');
            
        // Rimborso stimato (0.70 CHF/km per auto privata)
        $rimborsoStimato = $kmAutoPrivata * 0.70;
        
        // Mini-grafico ultimi 6 mesi Km Privata
        $kmPrivataTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $mese = Carbon::now()->subMonths($i);
            $km = Report::where('user_id', $user->id)
                ->where('auto_privata', true)
                ->whereMonth('data', $mese->month)
                ->whereYear('data', $mese->year)
                ->sum('km_auto');
            $kmPrivataTrend[] = (int) $km;
        }
        
        // Mini-grafico ultimi 6 mesi Km Totali
        $kmTotaliTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $mese = Carbon::now()->subMonths($i);
            $km = Report::where('user_id', $user->id)
                ->whereMonth('data', $mese->month)
                ->whereYear('data', $mese->year)
                ->sum('km_auto');
            $kmTotaliTrend[] = (int) $km;
        }
        
        return [
            Stat::make(__('ui.private_car_km'), number_format($kmAutoPrivata) . ' km')
                ->description(__("ui.reimbursement_description") . " " . number_format($rimborsoStimato, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($kmPrivataTrend)
                ->color('warning'),
                
            Stat::make(__('ui.total_km'), number_format($kmTotali) . ' km')
                ->description(__('ui.private_company'))
                ->descriptionIcon('heroicon-m-map-pin')
                ->chart($kmTotaliTrend)
                ->color('primary'),
        ];
    }
}

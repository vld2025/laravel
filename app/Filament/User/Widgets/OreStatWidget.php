<?php

namespace App\Filament\User\Widgets;

use App\Models\Report;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OreStatWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $meseCorrente = Carbon::now();
        
        // Ore lavorate questo mese
        $oreLavorateQuery = Report::where('user_id', $user->id)
            ->whereMonth('data', $meseCorrente->month)
            ->whereYear('data', $meseCorrente->year);
            
        $oreLavorate = $oreLavorateQuery->sum('ore_lavorate');
        $oreViaggio = $oreLavorateQuery->sum('ore_viaggio');
        $oreTotali = $oreLavorate + $oreViaggio;
        
        // Giorni lavorativi nel mese (esclusi weekend)
        $giorniLavorativi = $meseCorrente->copy()->startOfMonth()
            ->diffInWeekdays($meseCorrente->copy()->endOfMonth()) + 1;
            
        // Ore dovute (8 ore/giorno per giorni lavorativi)
        $oreDovute = $giorniLavorativi * 8;
        
        // Percentuale completamento
        $percentuale = $oreDovute > 0 ? round(($oreTotali / $oreDovute) * 100, 1) : 0;
        
        // Colore basato su percentuale
        $coloreOre = $percentuale >= 100 ? 'success' : ($percentuale >= 80 ? 'warning' : 'danger');
        
        // Mini-grafici ultimi 6 mesi
        $oreLavorateTrend = [];
        $oreViaggioTrend = [];
        $oreTotaliTrend = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $mese = Carbon::now()->subMonths($i);
            $datiMese = Report::where('user_id', $user->id)
                ->whereMonth('data', $mese->month)
                ->whereYear('data', $mese->year)
                ->selectRaw('SUM(ore_lavorate) as ore_lavorate, SUM(ore_viaggio) as ore_viaggio')
                ->first();
                
            $oreLav = (float) ($datiMese->ore_lavorate ?? 0);
            $oreViag = (float) ($datiMese->ore_viaggio ?? 0);
            
            $oreLavorateTrend[] = $oreLav;
            $oreViaggioTrend[] = $oreViag;
            $oreTotaliTrend[] = $oreLav + $oreViag;
        }
        
        return [
            Stat::make(__('ui.hours_worked'), number_format($oreLavorate, 1) . 'h')
                ->description(__('ui.pure_work_hours'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->chart($oreLavorateTrend)
                ->color('primary'),
                
            Stat::make(__('ui.total_hours'), number_format($oreTotali, 1) . 'h')
                ->description("из {$oreDovute}ч положено ({$percentuale}%)")
                ->descriptionIcon($percentuale >= 100 ? 'heroicon-m-check-circle' : 'heroicon-m-clock')
                ->chart($oreTotaliTrend)
                ->color($coloreOre),
                
            Stat::make(__('ui.travel_hours'), number_format($oreViaggio, 1) . 'h')
                ->description(__('ui.travel_time'))
                ->descriptionIcon('heroicon-m-truck')
                ->chart($oreViaggioTrend)
                ->color('info'),
        ];
    }
}

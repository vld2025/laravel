<?php
namespace App\Filament\Widgets;

use App\Models\Report;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatisticheReport extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();
        $inizioMese = Carbon::now()->startOfMonth();
        $fineMese = Carbon::now()->endOfMonth();
        
        // Query base per user normale
        $query = Report::whereBetween('data', [$inizioMese, $fineMese]);
        
        if (!auth()->user()->canViewAllData()) {
            $query->where('user_id', $userId);
        }
        
        $reportMese = $query->count();
        $oreLavorate = $query->sum('ore_lavorate');
        $oreViaggio = $query->sum('ore_viaggio');
        $kmPercorsi = $query->sum('km_auto');
        
        return [
            Stat::make('Report del Mese', $reportMese)
                ->description('Report inseriti')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->extraAttributes([
                    'style' => 'background: rgba(255, 255, 255, 0.9);'
                ]),
                
            Stat::make('Ore Lavorate', number_format($oreLavorate, 1) . ' ore')
                ->description('Questo mese')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: rgba(255, 255, 255, 0.9);'
                ]),
                
            Stat::make('Ore Viaggio', number_format($oreViaggio, 1) . ' ore')
                ->description('Questo mese')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: rgba(255, 255, 255, 0.9);'
                ]),
                
            Stat::make('KM Percorsi', number_format($kmPercorsi, 0) . ' km')
                ->description('Questo mese')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('danger')
                ->extraAttributes([
                    'style' => 'background: rgba(255, 255, 255, 0.9);'
                ]),
        ];
    }
}

<?php

namespace App\Filament\User\Widgets;

use App\Models\Report;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class OreMensiliChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __("ui.monthly_hours_chart");
    }
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $user = auth()->user();
        $mesi = [];
        $oreLavorate = [];
        $oreViaggio = [];
        
        // Ultimi 6 mesi
        for ($i = 5; $i >= 0; $i--) {
            $mese = Carbon::now()->subMonths($i);
            
            $datiMese = Report::where('user_id', $user->id)
                ->whereMonth('data', $mese->month)
                ->whereYear('data', $mese->year)
                ->selectRaw('SUM(ore_lavorate) as ore_lavorate, SUM(ore_viaggio) as ore_viaggio')
                ->first();
                
            $mesi[] = $mese->locale('it')->format('M Y');
            $oreLavorate[] = (float) ($datiMese->ore_lavorate ?? 0);
            $oreViaggio[] = (float) ($datiMese->ore_viaggio ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => __('ui.work_hours_label'),
                    'data' => $oreLavorate,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'tension' => 0.4,
                    'fill' => true,
                ],
                [
                    'label' => __('ui.travel_hours_label'),
                    'data' => $oreViaggio,
                    'backgroundColor' => 'rgba(249, 115, 22, 0.1)',
                    'borderColor' => 'rgb(249, 115, 22)',
                    'tension' => 0.4,
                    'fill' => true,
                ],
            ],
            'labels' => $mesi,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => __('ui.hours_unit')
                    ]
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top'
                ]
            ]
        ];
    }
}

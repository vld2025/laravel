<?php

namespace App\Filament\Widgets;

use App\Models\Report;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class GraficoOreLavorate extends ChartWidget
{
    protected static ?string $heading = 'Ore Lavorate - Ultimi 7 giorni';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $giorni = collect();
        $oreLavorate = collect();
        $oreViaggio = collect();
        
        for ($i = 6; $i >= 0; $i--) {
            $data = Carbon::now()->subDays($i);
            $giorni->push($data->format('d/m'));
            
            $query = Report::whereDate('data', $data->toDateString());
            
            if (!auth()->user()->canViewAllData()) {
                $query->where('user_id', auth()->id());
            }
            
            $oreLavorate->push($query->sum('ore_lavorate'));
            $oreViaggio->push($query->sum('ore_viaggio'));
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Ore Lavorate',
                    'data' => $oreLavorate->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Ore Viaggio',
                    'data' => $oreViaggio->toArray(),
                    'backgroundColor' => 'rgba(251, 191, 36, 0.5)',
                    'borderColor' => 'rgb(251, 191, 36)',
                ],
            ],
            'labels' => $giorni->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}

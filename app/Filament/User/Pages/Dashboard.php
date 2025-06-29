<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\OreStatWidget;
use App\Filament\User\Widgets\KmAutoWidget;
use App\Filament\User\Widgets\OreMensiliChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getTitle(): string
    {
        return '📊 Dashboard VLD Service';
    }

    public function getSubheading(): ?string
    {
        $greeting = now()->hour < 12 ? '🌅 Buongiorno' : (now()->hour < 18 ? '☀️ Buon pomeriggio' : '🌙 Buonasera');
        return $greeting . ' ' . auth()->user()->name . ' - ' . now()->locale('it')->format('d F Y');
    }

    public function getWidgets(): array
    {
        return [
            OreStatWidget::class,
            KmAutoWidget::class,
            OreMensiliChart::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 2,
        ];
    }
}

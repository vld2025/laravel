<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $title = ''; // TITOLO VUOTO
    
    protected static bool $shouldRegisterNavigation = false;

    public function getHeading(): string
    {
        return ''; // NESSUN HEADING
    }

    public function getSubheading(): ?string
    {
        return null;
    }
    
    public function getTitle(): string
    {
        return ''; // NESSUN TITOLO
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatisticheReport::class,
            \App\Filament\Widgets\GraficoOreLavorate::class,
        ];
    }
}

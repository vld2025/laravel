<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.user.pages.dashboard';

    public function getTitle(): string
    {
        return 'Dashboard Mobile';
    }

    public function getSubheading(): ?string
    {
        return 'Benvenuto ' . auth()->user()->name;
    }
}

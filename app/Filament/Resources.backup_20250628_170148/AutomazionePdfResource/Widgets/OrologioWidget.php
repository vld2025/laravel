<?php

namespace App\Filament\Resources\AutomazionePdfResource\Widgets;

use Filament\Widgets\Widget;

class OrologioWidget extends Widget
{
    protected static string $view = 'filament.widgets.orologio';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = -1;
}

<?php

namespace App\Filament\User\Resources\SpesaResource\Pages;

use App\Filament\User\Resources\SpesaResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSpesa extends ViewRecord
{
    protected static string $resource = SpesaResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }
}

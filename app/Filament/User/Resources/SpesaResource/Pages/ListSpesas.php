<?php

namespace App\Filament\User\Resources\SpesaResource\Pages;

use App\Filament\User\Resources\SpesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpesas extends ListRecords
{
    protected static string $resource = SpesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Nuova Spesa')
                ->color('success')
                ->size('sm'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}

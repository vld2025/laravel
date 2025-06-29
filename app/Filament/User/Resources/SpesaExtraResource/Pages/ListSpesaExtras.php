<?php

namespace App\Filament\User\Resources\SpesaExtraResource\Pages;

use App\Filament\User\Resources\SpesaExtraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpesaExtras extends ListRecords
{
    protected static string $resource = SpesaExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuova Spesa Extra')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}

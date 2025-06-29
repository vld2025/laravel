<?php

namespace App\Filament\Resources\ImpostazioneFatturaResource\Pages;

use App\Filament\Resources\ImpostazioneFatturaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImpostazioneFatturas extends ListRecords
{
    protected static string $resource = ImpostazioneFatturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

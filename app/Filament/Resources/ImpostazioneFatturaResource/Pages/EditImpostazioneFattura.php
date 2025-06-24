<?php

namespace App\Filament\Resources\ImpostazioneFatturaResource\Pages;

use App\Filament\Resources\ImpostazioneFatturaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImpostazioneFattura extends EditRecord
{
    protected static string $resource = ImpostazioneFatturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

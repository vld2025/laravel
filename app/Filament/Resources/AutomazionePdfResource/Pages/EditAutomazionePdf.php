<?php

namespace App\Filament\Resources\AutomazionePdfResource\Pages;

use App\Filament\Resources\AutomazionePdfResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutomazionePdf extends EditRecord
{
    protected static string $resource = AutomazionePdfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

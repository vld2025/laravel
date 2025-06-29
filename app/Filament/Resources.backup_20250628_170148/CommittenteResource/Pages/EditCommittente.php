<?php

namespace App\Filament\Resources\CommittenteResource\Pages;

use App\Filament\Resources\CommittenteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommittente extends EditRecord
{
    protected static string $resource = CommittenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

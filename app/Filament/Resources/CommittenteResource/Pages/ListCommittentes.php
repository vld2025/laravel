<?php

namespace App\Filament\Resources\CommittenteResource\Pages;

use App\Filament\Resources\CommittenteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommittentes extends ListRecords
{
    protected static string $resource = CommittenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

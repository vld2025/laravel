<?php

namespace App\Filament\User\Resources\SpesaResource\Pages;

use App\Filament\User\Resources\SpesaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpesa extends EditRecord
{
    protected static string $resource = SpesaResource::class;

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

    public function getBreadcrumbs(): array
    {
        return [];
    }
}

<?php

namespace App\Filament\User\Resources\SpesaExtraResource\Pages;

use App\Filament\User\Resources\SpesaExtraResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpesaExtra extends CreateRecord
{
    protected static string $resource = SpesaExtraResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}

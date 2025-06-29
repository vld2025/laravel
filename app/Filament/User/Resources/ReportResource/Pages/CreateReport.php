<?php

namespace App\Filament\User\Resources\ReportResource\Pages;

use App\Filament\User\Resources\ReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}

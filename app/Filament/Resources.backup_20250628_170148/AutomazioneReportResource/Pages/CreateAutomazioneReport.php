<?php

namespace App\Filament\Resources\AutomazioneReportResource\Pages;

use App\Filament\Resources\AutomazioneReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAutomazioneReport extends CreateRecord
{
    protected static string $resource = AutomazioneReportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\AutomazioneReportResource\Pages;

use App\Filament\Resources\AutomazioneReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutomazioneReport extends EditRecord
{
    protected static string $resource = AutomazioneReportResource::class;

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

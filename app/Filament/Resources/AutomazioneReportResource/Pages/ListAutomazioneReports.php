<?php

namespace App\Filament\Resources\AutomazioneReportResource\Pages;

use App\Filament\Resources\AutomazioneReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAutomazioneReports extends ListRecords
{
    protected static string $resource = AutomazioneReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

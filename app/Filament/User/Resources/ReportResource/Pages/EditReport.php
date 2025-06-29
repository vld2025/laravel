<?php

namespace App\Filament\User\Resources\ReportResource\Pages;

use App\Filament\User\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('← Torna alla Lista')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->button(),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->user_id === auth()->id() && !$this->record->fatturato),
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

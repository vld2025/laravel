<?php

namespace App\Filament\User\Resources\SpesaExtraResource\Pages;

use App\Filament\User\Resources\SpesaExtraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpesaExtra extends EditRecord
{
    protected static string $resource = SpesaExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('← Torna alla Lista')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->button(),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->user_id === auth()->id()),
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

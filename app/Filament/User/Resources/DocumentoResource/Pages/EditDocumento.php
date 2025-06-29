<?php

namespace App\Filament\User\Resources\DocumentoResource\Pages;

use App\Filament\User\Resources\DocumentoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;

class EditDocumento extends EditRecord
{
    protected static string $resource = DocumentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('← Torna alla Lista')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->button(),
            Actions\Action::make('download')
                ->label('Scarica')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn () => asset('storage/' . $this->record->file))
                ->openUrlInNewTab(),
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Assicurati che il file sia visibile nel form per il preview
        return $data;
    }
}

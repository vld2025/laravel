<?php

namespace App\Filament\User\Resources\DocumentoResource\Pages;

use App\Filament\User\Resources\DocumentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Storage;

class ViewDocumento extends ViewRecord
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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Anteprima Documento')
                    ->schema([
                        Infolists\Components\Group::make([
                            Infolists\Components\TextEntry::make('nome')
                                ->label('Nome documento')
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->weight('bold'),
                            
                            Infolists\Components\TextEntry::make('tipo')
                                ->label('Tipo')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'busta_paga' => 'success',
                                    'aziendale' => 'warning',
                                    'personale' => 'info',
                                    default => 'gray',
                                }),
                        ])->columns(2),
                        
                        Infolists\Components\Group::make([
                            Infolists\Components\TextEntry::make('created_at')
                                ->label('Caricato il')
                                ->dateTime('d/m/Y H:i'),
                            
                            Infolists\Components\TextEntry::make('user.name')
                                ->label('Caricato da')
                                ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isManager()),
                        ])->columns(2),
                        
                        Infolists\Components\ViewEntry::make('file_preview')
                            ->label('Anteprima')
                            ->view('filament.user.components.document-preview')
                            ->viewData(fn ($record) => [
                                'documento' => $record,
                                'fileUrl' => $record->file ? asset('storage/' . $record->file) : null,
                                'fileType' => $record->file ? pathinfo($record->file, PATHINFO_EXTENSION) : null,
                            ]),
                    ]),
            ]);
    }
}

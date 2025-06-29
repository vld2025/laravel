<?php

namespace App\Filament\User\Resources\DocumentoResource\Pages;

use App\Filament\User\Resources\DocumentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

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
                Infolists\Components\Section::make('Dettagli Documento')
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

                        Infolists\Components\ImageEntry::make('file')
                            ->label('Anteprima Documento')
                            ->disk('public')
                            ->height(400)
                            ->width('100%')
                            ->extraAttributes(['style' => 'object-fit: contain; border: 1px solid #e5e7eb; border-radius: 0.5rem;'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

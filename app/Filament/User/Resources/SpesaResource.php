<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SpesaResource\Pages;
use App\Models\Spesa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class SpesaResource extends Resource
{
    protected static ?string $model = Spesa::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Le Mie Spese';
    protected static ?string $modelLabel = 'Spesa';
    protected static ?string $pluralModelLabel = 'Spese';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('user_id')
                ->default(fn () => auth()->id()),
            
            Forms\Components\TextInput::make('anno')
                ->required()
                ->numeric()
                ->default(date('Y'))
                ->label('Anno'),
            
            Forms\Components\Select::make('mese')
                ->required()
                ->options([
                    1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo',
                    4 => 'Aprile', 5 => 'Maggio', 6 => 'Giugno',
                    7 => 'Luglio', 8 => 'Agosto', 9 => 'Settembre',
                    10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                ])
                ->default(date('n'))
                ->label('Mese'),
            
            Forms\Components\TextInput::make('descrizione')
                ->maxLength(255)
                ->label('Descrizione (opzionale)')
                ->placeholder('Descrizione della spesa...'),
            
            Forms\Components\FileUpload::make('file')
                ->required()
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->disk('public')
                ->directory('spese')
                ->downloadable()
                ->openable()
                ->label('Seleziona file o scansiona ricevuta')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // User vede solo le proprie spese
                if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('anno')
                    ->label('Anno')
                    ->width('60px')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('mese')
                    ->label('Mese')
                    ->formatStateUsing(fn ($state) => match($state) {
                        1 => 'Gen', 2 => 'Feb', 3 => 'Mar',
                        4 => 'Apr', 5 => 'Mag', 6 => 'Giu',
                        7 => 'Lug', 8 => 'Ago', 9 => 'Set',
                        10 => 'Ott', 11 => 'Nov', 12 => 'Dic',
                        default => $state
                    })
                    ->width('50px')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('descrizione')
                    ->label('Descrizione')
                    ->limit(30)
                    ->placeholder('Nessuna descrizione')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->label('Data')
                    ->width('50px'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utente')
                    ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isManager()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('anno')
                    ->options(fn () => collect(range(2023, date('Y') + 1))->mapWithKeys(fn ($year) => [$year => $year])),
                
                Tables\Filters\SelectFilter::make('mese')
                    ->options([
                        1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo',
                        4 => 'Aprile', 5 => 'Maggio', 6 => 'Giugno',
                        7 => 'Luglio', 8 => 'Agosto', 9 => 'Settembre',
                        10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Visualizza spesa')
                    ->color('primary')
                    ->url(fn (Spesa $record): string => static::getUrl('view', ['record' => $record]))
                    ->size('sm'),
                
                Tables\Actions\Action::make('download')
                    ->label('')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->tooltip('Scarica file')
                    ->action(function (Spesa $record) {
                        if (!$record->file || !Storage::disk('public')->exists($record->file)) {
                            \Filament\Notifications\Notification::make()
                                ->title('File non trovato')
                                ->danger()
                                ->send();
                            return;
                        }
                        return Storage::disk('public')->download(
                            $record->file,
                            'spesa_' . $record->anno . '_' . str_pad($record->mese, 2, '0', STR_PAD_LEFT) . '_' . $record->id . '.' . pathinfo($record->file, PATHINFO_EXTENSION)
                        );
                    })
                    ->size('sm'),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Anteprima Spesa')
                    ->schema([
                        Infolists\Components\Group::make([
                            Infolists\Components\TextEntry::make('anno')
                                ->label('Anno')
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->weight('bold'),
                            
                            Infolists\Components\TextEntry::make('mese')
                                ->label('Mese')
                                ->formatStateUsing(fn ($state) => match($state) {
                                    1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo',
                                    4 => 'Aprile', 5 => 'Maggio', 6 => 'Giugno',
                                    7 => 'Luglio', 8 => 'Agosto', 9 => 'Settembre',
                                    10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre',
                                    default => $state
                                }),
                        ])->columns(2),
                        
                        Infolists\Components\TextEntry::make('descrizione')
                            ->label('Descrizione')
                            ->placeholder('Nessuna descrizione disponibile')
                            ->visible(fn ($record) => !empty($record->descrizione)),
                        
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
                    ])
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('back')
                            ->label('← Torna alla Lista')
                            ->color('gray')
                            ->url(fn () => static::getUrl('index')),
                        
                        Infolists\Components\Actions\Action::make('download')
                            ->label('Scarica')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success')
                            ->action(function ($record) {
                                if (!$record->file || !Storage::disk('public')->exists($record->file)) {
                                    return redirect()->back()->with('error', 'File non trovato');
                                }
                                return Storage::disk('public')->download(
                                    $record->file,
                                    'spesa_' . $record->anno . '_' . str_pad($record->mese, 2, '0', STR_PAD_LEFT) . '_' . $record->id . '.' . pathinfo($record->file, PATHINFO_EXTENSION)
                                );
                            }),
                        
                        Infolists\Components\Actions\Action::make('edit')
                            ->label('Modifica')
                            ->icon('heroicon-o-pencil')
                            ->color('warning')
                            ->url(fn ($record) => static::getUrl('edit', ['record' => $record]))
                            ->visible(fn ($record) => $record->user_id === auth()->id() || auth()->user()->isAdmin() || auth()->user()->isManager()),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpesas::route('/'),
            'create' => Pages\CreateSpesa::route('/create'),
            'view' => Pages\ViewSpesa::route('/{record}'),
            'edit' => Pages\EditSpesa::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $record->user_id === $user->id || $user->isAdmin() || $user->isManager();
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $record->user_id === $user->id || $user->isAdmin() || $user->isManager();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (!auth()->user()->isAdmin() && !auth()->user()->isManager()) {
            $query->where('user_id', auth()->id());
        }
        
        return $query;
    }
}

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
                ->label(__('ui.year')),
            
            Forms\Components\Select::make('mese')
                ->required()
                ->options([
                    1 => __('ui.january'), 2 => __('ui.february'), 3 => __('ui.march'),
                    4 => __('ui.april'), 5 => __('ui.may'), 6 => __('ui.june'),
                    7 => __('ui.july'), 8 => __('ui.august'), 9 => __('ui.september'),
                    10 => __('ui.october'), 11 => __('ui.november'), 12 => __('ui.december')
                ])
                ->default(date('n'))
                ->label(__('ui.month')),
            
            Forms\Components\TextInput::make('descrizione')
                ->maxLength(255)
                ->label(__('ui.optional_description'))
                ->placeholder(__('ui.expense_description_placeholder')),
            
            Forms\Components\FileUpload::make('file')
                ->required()
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->disk('public')
                ->directory('spese')
                ->downloadable()
                ->openable()
                ->label(__('ui.scan_receipt'))
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
                    ->label(__('ui.year'))
                    ->width('60px')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('mese')
                    ->label(__('ui.month'))
                    ->formatStateUsing(fn ($state) => match($state) {
                        1 => __('ui.jan'), 2 => __('ui.feb'), 3 => __('ui.mar'),
                        4 => __('ui.apr'), 5 => __('ui.may_short'), 6 => __('ui.jun'),
                        7 => __('ui.jul'), 8 => __('ui.aug'), 9 => __('ui.sep'),
                        10 => __('ui.oct'), 11 => __('ui.nov'), 12 => __('ui.dec'),
                        default => $state
                    })
                    ->width('50px')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('descrizione')
                    ->label(__('ui.description'))
                    ->limit(30)
                    ->placeholder(__('ui.no_description'))
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->label(__('ui.date'))
                    ->width('50px'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('ui.user'))
                    ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isManager()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('anno')
                    ->options(fn () => collect(range(2023, date('Y') + 1))->mapWithKeys(fn ($year) => [$year => $year])),
                
                Tables\Filters\SelectFilter::make('mese')
                    ->options([
                        1 => __('ui.january'), 2 => __('ui.february'), 3 => __('ui.march'),
                        4 => __('ui.april'), 5 => __('ui.may'), 6 => __('ui.june'),
                        7 => __('ui.july'), 8 => __('ui.august'), 9 => __('ui.september'),
                        10 => __('ui.october'), 11 => __('ui.november'), 12 => __('ui.december')
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->tooltip(__('ui.view_expense'))
                    ->color('primary')
                    ->url(fn (Spesa $record): string => static::getUrl('view', ['record' => $record]))
                    ->size('sm'),
                
                Tables\Actions\Action::make('download')
                    ->label('')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->tooltip(__('ui.download_file'))
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
                                ->label(__('ui.year'))
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->weight('bold'),
                            
                            Infolists\Components\TextEntry::make('mese')
                                ->label(__('ui.month'))
                                ->formatStateUsing(fn ($state) => match($state) {
                                    1 => __('ui.january'), 2 => __('ui.february'), 3 => __('ui.march'),
                                    4 => __('ui.april'), 5 => __('ui.may'), 6 => __('ui.june'),
                                    7 => __('ui.july'), 8 => __('ui.august'), 9 => __('ui.september'),
                                    10 => __('ui.october'), 11 => __('ui.november'), 12 => __('ui.december'),
                                    default => $state
                                }),
                        ])->columns(2),
                        
                        Infolists\Components\TextEntry::make('descrizione')
                            ->label(__('ui.description'))
                            ->placeholder(__('ui.no_description_available'))
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
                            ->label(__('ui.back_to_list'))
                            ->color('gray')
                            ->url(fn () => static::getUrl('index')),
                        
                        Infolists\Components\Actions\Action::make('download')
                            ->label(__('ui.download'))
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
                            ->label(__('ui.edit'))
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

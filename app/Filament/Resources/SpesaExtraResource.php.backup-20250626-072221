<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpesaExtraResource\Pages;
use App\Models\SpesaExtra;
use App\Models\Committente;
use App\Services\AIService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class SpesaExtraResource extends Resource
{
    protected static ?string $model = SpesaExtra::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $modelLabel = 'Spesa Extra';

    protected static ?string $pluralModelLabel = 'Spese Extra';

    protected static ?string $navigationGroup = 'Gestione Lavori';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dati Spesa Extra')
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),

                        Forms\Components\Select::make('committente_id')
                            ->label('Committente')
                            ->required()
                            ->options(Committente::pluck('nome', 'id'))
                            ->searchable(),

                        Forms\Components\DatePicker::make('data')
                            ->required()
                            ->default(now())
                            ->label('Data Spesa'),

                        Forms\Components\FileUpload::make('file')
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->maxSize(50 * 1024) // 50MB
                            ->directory('spese-extra')
                            ->label('File Spesa')
                            ->helperText('Carica una foto o PDF della ricevuta. L\'AI estrarrà automaticamente importo e descrizione.')
                            ->previewable()
                            ->downloadable(),
                    ])->columns(2),

                Forms\Components\Section::make('Dati Estratti da AI')
                    ->description('Questi campi vengono popolati automaticamente dall\'AI quando carichi il file')
                    ->schema([
                        Forms\Components\TextInput::make('importo')
                            ->numeric()
                            ->prefix('CHF')
                            ->label('Importo')
                            ->helperText('Compilato automaticamente dall\'AI'),

                        Forms\Components\Textarea::make('descrizione')
                            ->rows(3)
                            ->label('Descrizione')
                            ->helperText('Compilato automaticamente dall\'AI'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('data')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utente')
                    ->searchable()
                    ->visible(fn () => auth()->user()?->canViewAllData()),

                Tables\Columns\TextColumn::make('committente.nome')
                    ->label('Committente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('importo')
                    ->label('Importo')
                    ->prefix('CHF ')
                    ->numeric(2)
                    ->placeholder('Da elaborare'),

                Tables\Columns\TextColumn::make('descrizione')
                    ->label('Descrizione')
                    ->limit(50)
                    ->placeholder('Da elaborare'),

                Tables\Columns\IconColumn::make('processed')
                    ->label('AI')
                    ->getStateUsing(fn (SpesaExtra $record) => $record->isProcessedByAI())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('committente')
                    ->relationship('committente', 'nome'),
                Tables\Filters\Filter::make('processed')
                    ->label('Elaborato da AI')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('importo')->whereNotNull('descrizione'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('process_ai')
                    ->label('Elabora con AI')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->visible(fn (SpesaExtra $record) => !$record->isProcessedByAI() && $record->file)
                    ->action(function (SpesaExtra $record) {
                        $aiService = app(AIService::class);
                        
                        if (!$aiService->isConfigured()) {
                            Notification::make()
                                ->title('AI non configurata')
                                ->body('Configura la chiave OpenAI per utilizzare questa funzione')
                                ->warning()
                                ->send();
                            return;
                        }

                        $filePath = $record->file;
                        $result = $aiService->extractDataFromReceipt($filePath);

                        if ($result['success']) {
                            $updateData = [];
                            
                            if ($result['importo'] !== null) {
                                $updateData['importo'] = $result['importo'];
                            }
                            
                            if (!empty($result['descrizione'])) {
                                $updateData['descrizione'] = $result['descrizione'];
                            }

                            if (!empty($updateData)) {
                                $record->update($updateData);
                                
                                Notification::make()
                                    ->title('Elaborazione completata!')
                                    ->body('AI ha estratto i dati dalla ricevuta')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Nessun dato estratto')
                                    ->body('L\'AI non è riuscita a estrarre dati utili da questo file')
                                    ->warning()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title('Errore elaborazione')
                                ->body($result['error'] ?? 'Errore sconosciuto')
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn (SpesaExtra $record) => auth()->user()?->canViewAllData() || $record->user_id === auth()->id()),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (SpesaExtra $record) => auth()->user()?->canViewAllData() || $record->user_id === auth()->id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->canViewAllData()),
                ]),
            ])
            ->defaultSort('data', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // User vede solo le proprie spese extra
        if (!auth()->user()?->canViewAllData()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpesaExtras::route('/'),
            'create' => Pages\CreateSpesaExtra::route('/create'),
            'edit' => Pages\EditSpesaExtra::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getNavigationBadge() ? "primary" : null;
    }
}

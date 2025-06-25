<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use App\Models\Committente;
use App\Models\Cliente;
use App\Models\Commessa;
use App\Exports\ReportMensileExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Report';

    protected static ?string $pluralModelLabel = 'Report';

    protected static ?string $navigationGroup = 'Gestione Lavori';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Base')
                    ->schema([
                        Forms\Components\DatePicker::make('data')
                            ->required()
                            ->default(now())
                            ->label('Data Lavoro'),

                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])->columns(1),

                Forms\Components\Section::make('Cliente e Commessa')
                    ->description('Seleziona committente, cliente e commessa in ordine gerarchico')
                    ->schema([
                        Forms\Components\Select::make('committente_id')
                            ->label('Committente')
                            ->required()
                            ->options(Committente::pluck('nome', 'id'))
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('cliente_id', null);
                                $set('commessa_id', null);
                            }),

                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->required()
                            ->options(fn (Get $get): Collection => Cliente::query()
                                ->where('committente_id', $get('committente_id'))
                                ->pluck('nome', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('commessa_id', null)),

                        Forms\Components\Select::make('commessa_id')
                            ->label('Commessa')
                            ->required()
                            ->options(fn (Get $get): Collection => Commessa::query()
                                ->where('cliente_id', $get('cliente_id'))
                                ->pluck('nome', 'id')),
                    ])->columns(3),

                Forms\Components\Section::make('Ore di Lavoro')
                    ->schema([
                        Forms\Components\TextInput::make('ore_lavorate')
                            ->label('Ore Lavorate')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(24)
                            ->suffix('ore'),

                        Forms\Components\TextInput::make('ore_viaggio')
                            ->label('Ore Viaggio')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(24)
                            ->suffix('ore'),
                    ])->columns(2),

                Forms\Components\Section::make('Trasferta e Veicolo')
                    ->schema([
                        Forms\Components\TextInput::make('km_auto')
                            ->label('Chilometri')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('km'),

                        Forms\Components\Toggle::make('auto_privata')
                            ->label('Auto Privata')
                            ->helperText('Spento = Auto aziendale'),

                        Forms\Components\Toggle::make('notturno')
                            ->label('Lavoro Notturno'),

                        Forms\Components\Toggle::make('trasferta')
                            ->label('Trasferta'),
                    ])->columns(4),

                Forms\Components\Section::make('Descrizione Lavori')
                    ->schema([
                        Forms\Components\Textarea::make('descrizione_lavori')
                            ->label('Descrizione Dettagliata')
                            ->rows(4)
                            ->helperText('Descrivi il lavoro svolto in dettaglio'),
                    ])->columns(1),

                // Sezione visibile solo ad Admin/Manager
                Forms\Components\Section::make('Dati Fatturazione')
                    ->schema([
                        Forms\Components\TextInput::make('ore_lavorate_fatturazione')
                            ->label('Ore Lavorate per Fatturazione')
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(24)
                            ->suffix('ore')
                            ->helperText('Lascia vuoto per usare le ore lavorate normali'),

                        Forms\Components\TextInput::make('ore_viaggio_fatturazione')
                            ->label('Ore Viaggio per Fatturazione')
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(24)
                            ->suffix('ore')
                            ->helperText('Lascia vuoto per usare le ore viaggio normali'),

                        Forms\Components\Toggle::make('fatturato')
                            ->label('Fatturato')
                            ->helperText('Una volta fatturato, l\'utente non può più modificare'),
                    ])
                    ->columns(3)
                    ->visible(fn () => auth()->user()?->canViewAllData()),
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

                Tables\Columns\TextColumn::make('cliente.nome')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('commessa.nome')
                    ->label('Commessa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ore_lavorate')
                    ->label('Ore Lav.')
                    ->suffix(' h'),

                Tables\Columns\TextColumn::make('ore_viaggio')
                    ->label('Ore Viag.')
                    ->suffix(' h'),

                Tables\Columns\IconColumn::make('fatturato')
                    ->label('Fatturato')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('committente')
                    ->relationship('committente', 'nome'),
                Tables\Filters\Filter::make('fatturato')
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn () => auth()->user()?->canViewAllData())
                    ->form([
                        Forms\Components\Select::make('committente_id')
                            ->label('Committente')
                            ->required()
                            ->options(Committente::pluck('nome', 'id'))
                            ->searchable(),
                        
                        Forms\Components\Select::make('anno')
                            ->label('Anno')
                            ->required()
                            ->options(array_combine(range(2020, 2030), range(2020, 2030)))
                            ->default(date('Y')),
                        
                        Forms\Components\Select::make('mese')
                            ->label('Mese')
                            ->required()
                            ->options([
                                1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
                                5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
                                9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                            ])
                            ->default(date('n'))
                    ])
                    ->action(function (array $data) {
                        $committente = Committente::find($data['committente_id']);
                        $mesi = [
                            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
                            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
                            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                        ];
                        
                        $nomeFile = "Report_{$committente->nome}_{$mesi[$data['mese']]}_{$data['anno']}.xlsx";
                        
                        Notification::make()
                            ->title('Export completato!')
                            ->body("File Excel generato: {$nomeFile}")
                            ->success()
                            ->send();
                        
                        return Excel::download(
                            new ReportMensileExport($data['committente_id'], $data['anno'], $data['mese']),
                            $nomeFile
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Report $record) => $record->canUserEdit(auth()->user())),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Report $record) => $record->canUserEdit(auth()->user())),
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

        // User vede solo i propri report
        if (!auth()->user()?->canViewAllData()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
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

<?php
namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ReportResource\Pages;
use App\Models\Report;
use App\Models\Committente;
use App\Models\Cliente;
use App\Models\Commessa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Report Lavoro';
    protected static ?string $modelLabel = 'Rapportino';
    protected static ?string $pluralModelLabel = 'Rapportini';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('ui.base_info'))
                ->schema([
                    Forms\Components\Hidden::make('user_id')
                        ->default(fn () => auth()->id()),
                        
                    Forms\Components\DatePicker::make('data')
                        ->required()
                        ->default(now())
                        ->label(__('ui.work_date'))
                        ->helperText(__('ui.holiday_auto_calc')),
                        
                    Forms\Components\Select::make('committente_id')
                        ->label(__('ui.client'))
                        ->required()
                        ->options(Committente::pluck('nome', 'id'))
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('cliente_id', null)),
                        
                    Forms\Components\Select::make('cliente_id')
                        ->label(__('ui.customer'))
                        ->required()
                        ->options(function (callable $get) {
                            $committenteId = $get('committente_id');
                            if (!$committenteId) return [];
                            return Cliente::where('committente_id', $committenteId)->pluck('nome', 'id');
                        })
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('commessa_id', null)),
                        
                    Forms\Components\Select::make('commessa_id')
                        ->label(__('ui.job'))
                        ->required()
                        ->options(function (callable $get) {
                            $clienteId = $get('cliente_id');
                            if (!$clienteId) return [];
                            return Commessa::where('cliente_id', $clienteId)->pluck('nome', 'id');
                        })
                        ->searchable(),
                ])->columns(1),
                
            Forms\Components\Section::make(__('ui.worked_hours'))
                ->schema([
                    Forms\Components\TextInput::make('ore_lavorate')
                        ->label(__('ui.work_hours_field'))
                        ->required()
                        ->numeric()
                        ->step(0.5)
                        ->minValue(0)
                        ->maxValue(24)
                        ->suffix('ore'),
                        
                    Forms\Components\TextInput::make('ore_viaggio')
                        ->label(__('ui.travel_hours_field'))
                        ->required()
                        ->numeric()
                        ->step(0.5)
                        ->minValue(0)
                        ->maxValue(24)
                        ->suffix('ore'),
                ])->columns(2),
                
            Forms\Components\Section::make(__('ui.transport'))
                ->schema([
                    Forms\Components\TextInput::make('km_auto')
                        ->label(__('ui.car_km'))
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->suffix('km'),
                        
                    Forms\Components\Toggle::make('auto_privata')
                        ->label(__('ui.private_car'))
                        ->helperText(__('ui.private_car_tooltip')),
                ])->columns(2),
                
            Forms\Components\Section::make(__('ui.work_flags'))
                ->schema([
                    Forms\Components\Toggle::make('notturno')
                        ->label(__('ui.night_work')),
                        
                    Forms\Components\Toggle::make('trasferta')
                        ->label(__('ui.business_trip')),
                        
                    // FESTIVO RIMOSSO - Calcolato automaticamente dal sistema
                ])->columns(2),
                
            Forms\Components\Section::make(__('ui.work_description'))
                ->schema([
                    Forms\Components\Textarea::make('descrizione_lavori')
                        ->label(__('ui.activity_description'))
                        ->rows(4)
                        ->helperText(__('ui.auto_translations'))
                        ->columnSpanFull(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // User vede solo i propri report
                $query->where('user_id', auth()->id());
            })
            ->columns([
                Tables\Columns\TextColumn::make('data')
                    ->label(__('ui.date'))
                    ->date('d/m/Y')
                    ->sortable()
                    ->width('80px')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('cliente.nome')
                    ->label(__('ui.job'))
                    ->limit(15)
                    ->searchable()
                    ->width('120px')
                    ->tooltip(fn ($record) => $record->cliente?->nome),
                    
                Tables\Columns\TextColumn::make('commessa.nome')
                    ->label('')
                    ->limit(12)
                    ->searchable()
                    ->width('100px')
                    ->tooltip(fn ($record) => $record->commessa?->nome),
                    
                Tables\Columns\TextColumn::make('ore_lavorate')
                    ->label(__('ui.hours'))
                    ->suffix('h')
                    ->alignCenter()
                    ->width('60px'),
                    
                Tables\Columns\TextColumn::make('ore_viaggio')
                    ->label(__('ui.travel'))
                    ->suffix('h')
                    ->alignCenter()
                    ->width('60px'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('committente_id')
                    ->relationship('committente', 'nome')
                    ->label(__('ui.client')),
                    
                Tables\Filters\Filter::make('non_fatturato')
                    ->label(__('ui.not_billed'))
                    ->query(fn (Builder $query): Builder => $query->where('fatturato', false))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('festivi')
                    ->label(__('ui.holidays_only'))
                    ->query(fn (Builder $query): Builder => $query->where('festivo', true))
                    ->toggle(),
            ])
            ->actions([
                // Nessuna azione - click sulla riga per aprire
            ])
            ->recordAction('edit')
            ->recordUrl(fn (Report $record): string => static::getUrl('edit', ['record' => $record]))
            ->bulkActions([])
            ->defaultSort('data', 'desc')
            ->paginated([10, 25]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        return $record->user_id === auth()->id() && !$record->fatturato;
    }

    public static function canDelete($record): bool
    {
        return $record->user_id === auth()->id() && !$record->fatturato;
    }
}

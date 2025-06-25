<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FatturaResource\Pages;
use App\Models\Fattura;
use App\Models\Committente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FatturaResource extends Resource
{
    protected static ?string $model = Fattura::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    
    protected static ?string $modelLabel = 'Fattura';
    
    protected static ?string $pluralModelLabel = 'Fatture';
    
    protected static ?string $navigationGroup = 'Amministrazione';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Fattura')
                    ->schema([
                        Forms\Components\TextInput::make('numero')
                            ->required()
                            ->unique(ignorable: fn ($record) => $record)
                            ->default(fn () => Fattura::generateNumero(date('Y')))
                            ->label('Numero Fattura'),
                        
                        Forms\Components\Select::make('committente_id')
                            ->label('Committente')
                            ->required()
                            ->options(Committente::pluck('nome', 'id'))
                            ->searchable(),
                        
                        Forms\Components\DatePicker::make('data_emissione')
                            ->required()
                            ->default(now())
                            ->label('Data Emissione'),
                        
                        Forms\Components\Select::make('stato')
                            ->options(Fattura::getStati())
                            ->default('bozza')
                            ->required()
                            ->label('Stato'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Periodo Riferimento')
                    ->schema([
                        Forms\Components\Select::make('mese_riferimento')
                            ->options([
                                1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
                                5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
                                9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                            ])
                            ->default(date('n'))
                            ->required()
                            ->label('Mese'),
                        
                        Forms\Components\Select::make('anno_riferimento')
                            ->options(array_combine(
                                range(date('Y') - 2, date('Y') + 1),
                                range(date('Y') - 2, date('Y') + 1)
                            ))
                            ->default(date('Y'))
                            ->required()
                            ->label('Anno'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Totali')
                    ->schema([
                        Forms\Components\TextInput::make('totale_ore_lavoro')
                            ->numeric()
                            ->label('Totale Ore Lavoro')
                            ->suffix('h'),
                        
                        Forms\Components\TextInput::make('totale_ore_viaggio')
                            ->numeric()
                            ->label('Totale Ore Viaggio')
                            ->suffix('h'),
                        
                        Forms\Components\TextInput::make('totale_km')
                            ->numeric()
                            ->label('Totale Km')
                            ->suffix('km'),
                        
                        Forms\Components\TextInput::make('imponibile')
                            ->numeric()
                            ->prefix('CHF')
                            ->label('Imponibile'),
                        
                        Forms\Components\TextInput::make('sconto')
                            ->numeric()
                            ->prefix('CHF')
                            ->label('Sconto'),
                        
                        Forms\Components\TextInput::make('totale')
                            ->numeric()
                            ->prefix('CHF')
                            ->label('Totale'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('Numero')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('committente.nome')
                    ->label('Committente')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('periodo_riferimento')
                    ->label('Periodo'),
                
                Tables\Columns\BadgeColumn::make('stato')
                    ->label('Stato')
                    ->colors([
                        'secondary' => 'bozza',
                        'warning' => 'emessa',
                        'success' => 'pagata',
                    ])
                    ->formatStateUsing(fn (string $state) => Fattura::getStati()[$state] ?? $state),
                
                Tables\Columns\TextColumn::make('totale')
                    ->label('Totale')
                    ->prefix('CHF ')
                    ->numeric(2),
                
                Tables\Columns\TextColumn::make('data_emissione')
                    ->label('Data Emissione')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stato')
                    ->options(Fattura::getStati()),
                Tables\Filters\SelectFilter::make('committente')
                    ->relationship('committente', 'nome'),
            ])
            ->actions([
                Tables\Actions\Action::make('pdf_normale')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (\App\Models\Fattura $record): string => route('pdf.fattura', $record))
                    ->openUrlInNewTab()
                    ->color('info')
                    ->visible(function (\App\Models\Fattura $record): bool {
                        $impostazioni = \App\Models\ImpostazioneFattura::where('committente_id', $record->committente_id)->first();
                        return !($impostazioni?->swiss_qr_bill ?? false);
                    }),

                Tables\Actions\Action::make('pdf_qr_bill')
                    ->label('PDF QR Bill')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn (\App\Models\Fattura $record): string => route('pdf.fattura-qr', $record))
                    ->openUrlInNewTab()
                    ->color('success')
                    ->visible(function (\App\Models\Fattura $record): bool {
                        $impostazioni = \App\Models\ImpostazioneFattura::where('committente_id', $record->committente_id)->first();
                        return $impostazioni?->swiss_qr_bill ?? false;
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('data_emissione', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFatturas::route('/'),
            'create' => Pages\CreateFattura::route('/create'),
            'edit' => Pages\EditFattura::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewAllData() ?? false;
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

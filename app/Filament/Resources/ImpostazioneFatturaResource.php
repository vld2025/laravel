<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImpostazioneFatturaResource\Pages;
use App\Models\ImpostazioneFattura;
use App\Models\Committente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ImpostazioneFatturaResource extends Resource
{
    protected static ?string $model = ImpostazioneFattura::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $modelLabel = 'Impostazione Fattura';
    
    protected static ?string $pluralModelLabel = 'Impostazioni Fattura';
    
    protected static ?string $navigationGroup = 'Amministrazione';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Committente')
                    ->schema([
                        Forms\Components\Select::make('committente_id')
                            ->label('Committente')
                            ->required()
                            ->options(Committente::pluck('nome', 'id'))
                            ->unique(ignorable: fn ($record) => $record)
                            ->searchable(),
                    ])->columns(1),
                
                Forms\Components\Section::make('Dati Fatturazione')
                    ->schema([
                        Forms\Components\Textarea::make('indirizzo_fatturazione')
                            ->required()
                            ->rows(3)
                            ->label('Indirizzo Fatturazione'),
                        
                        Forms\Components\TextInput::make('partita_iva')
                            ->required()
                            ->label('Partita IVA'),
                        
                        Forms\Components\TextInput::make('iban')
                            ->required()
                            ->label('IBAN'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Configurazioni')
                    ->schema([
                        Forms\Components\Toggle::make('swiss_qr_bill')
                            ->label('Swiss QR Bill'),
                        
                        Forms\Components\Toggle::make('fatturazione_automatica')
                            ->label('Fatturazione Automatica'),
                        
                        Forms\Components\TextInput::make('giorno_fatturazione')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(31)
                            ->label('Giorno Fatturazione (1-31)'),
                        
                        Forms\Components\TagsInput::make('email_destinatari')
                            ->label('Email Destinatari'),
                    ])->columns(4),
                
                Forms\Components\Section::make('Costi Base')
                    ->schema([
                        Forms\Components\TextInput::make('costo_orario')
                            ->required()
                            ->numeric()
                            ->prefix('CHF')
                            ->label('Costo Orario'),
                        
                        Forms\Components\TextInput::make('costo_km')
                            ->required()
                            ->numeric()
                            ->prefix('CHF')
                            ->label('Costo per Km'),
                        
                        Forms\Components\TextInput::make('costo_pranzo')
                            ->numeric()
                            ->prefix('CHF')
                            ->label('Costo Pranzo'),
                        
                        Forms\Components\TextInput::make('costo_trasferta')
                            ->numeric()
                            ->prefix('CHF')
                            ->label('Costo Trasferta'),
                        
                        Forms\Components\TextInput::make('costo_fisso_intervento')
                            ->numeric()
                            ->prefix('CHF')
                            ->label('Costo Fisso Intervento'),
                    ])->columns(3),
                
                Forms\Components\Section::make('Percentuali')
                    ->schema([
                        Forms\Components\TextInput::make('percentuale_notturno')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->label('Maggiorazione Notturno'),
                        
                        Forms\Components\TextInput::make('percentuale_festivo')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->label('Maggiorazione Festivo'),
                        
                        Forms\Components\TextInput::make('sconto_percentuale')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->label('Sconto'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('committente.nome')
                    ->label('Committente')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('costo_orario')
                    ->label('CHF/ora')
                    ->prefix('CHF ')
                    ->numeric(2),
                
                Tables\Columns\TextColumn::make('costo_km')
                    ->label('CHF/km')
                    ->prefix('CHF ')
                    ->numeric(2),
                
                Tables\Columns\IconColumn::make('swiss_qr_bill')
                    ->label('QR Bill')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('fatturazione_automatica')
                    ->label('Auto')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Aggiornato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImpostazioneFatturas::route('/'),
            'create' => Pages\CreateImpostazioneFattura::route('/create'),
            'edit' => Pages\EditImpostazioneFattura::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewAllData() ?? false;
    }
}

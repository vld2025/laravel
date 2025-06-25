<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommittenteResource\Pages;
use App\Models\Committente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommittenteResource extends Resource
{
    protected static ?string $model = Committente::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $modelLabel = 'Committente';
    
    protected static ?string $pluralModelLabel = 'Committenti';
    
    protected static ?string $navigationGroup = 'Gestione Clienti';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Principali')
                    ->schema([
                        Forms\Components\TextInput::make('nome')
                            ->required()
                            ->maxLength(255)
                            ->label('Nome Committente'),
                        
                        Forms\Components\TextInput::make('partita_iva')
                            ->label('Partita IVA')
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('indirizzo')
                            ->label('Indirizzo')
                            ->rows(3),
                    ])->columns(2),
                
                Forms\Components\Section::make('Dati Bancari')
                    ->schema([
                        Forms\Components\TextInput::make('iban')
                            ->label('IBAN')
                            ->maxLength(255),
                        
                        Forms\Components\KeyValue::make('dati_bancari')
                            ->label('Altri Dati Bancari')
                            ->keyLabel('Campo')
                            ->valueLabel('Valore'),
                    ])->columns(1),
                
                Forms\Components\Section::make('Altri Dati')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->maxSize(50 * 1024) // 50MB come da requisiti
                            ->directory('committenti/loghi'),
                        
                        Forms\Components\Textarea::make('informazioni')
                            ->label('Note e Informazioni')
                            ->rows(4),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('partita_iva')
                    ->label('P.IVA')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('clienti_count')
                    ->label('Clienti')
                    ->counts('clienti'),
                
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->size(40),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommittentes::route('/'),
            'create' => Pages\CreateCommittente::route('/create'),
            'edit' => Pages\EditCommittente::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewAllData() ?? false;
    }
}

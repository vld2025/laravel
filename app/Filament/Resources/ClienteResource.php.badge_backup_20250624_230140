<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use App\Models\Committente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $modelLabel = 'Cliente';
    
    protected static ?string $pluralModelLabel = 'Clienti';
    
    protected static ?string $navigationGroup = 'Gestione Clienti';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Cliente')
                    ->schema([
                        Forms\Components\Select::make('committente_id')
                            ->label('Committente')
                            ->required()
                            ->options(Committente::pluck('nome', 'id'))
                            ->searchable(),
                        
                        Forms\Components\TextInput::make('nome')
                            ->required()
                            ->maxLength(255)
                            ->label('Nome Cliente'),
                        
                        Forms\Components\Textarea::make('indirizzo')
                            ->label('Indirizzo')
                            ->rows(3),
                    ])->columns(2),
                
                Forms\Components\Section::make('Dati Aggiuntivi')
                    ->schema([
                        Forms\Components\KeyValue::make('dati_bancari')
                            ->label('Dati Bancari')
                            ->keyLabel('Campo')
                            ->valueLabel('Valore'),
                        
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
                
                Tables\Columns\TextColumn::make('committente.nome')
                    ->label('Committente')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('commesse_count')
                    ->label('Commesse')
                    ->counts('commesse'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('committente')
                    ->relationship('committente', 'nome'),
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewAllData() ?? false;
    }
}

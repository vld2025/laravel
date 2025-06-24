<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommessaResource\Pages;
use App\Models\Commessa;
use App\Models\Committente;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;

class CommessaResource extends Resource
{
    protected static ?string $model = Commessa::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    
    protected static ?string $modelLabel = 'Commessa';
    
    protected static ?string $pluralModelLabel = 'Commesse';
    
    protected static ?string $navigationGroup = 'Gestione Clienti';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Commessa')
                    ->schema([
                        Forms\Components\Select::make('committente_id')
                            ->label('Committente')
                            ->required()
                            ->options(Committente::pluck('nome', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('cliente_id', null)),
                        
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->required()
                            ->options(fn (Get $get): Collection => Cliente::query()
                                ->where('committente_id', $get('committente_id'))
                                ->pluck('nome', 'id')),
                        
                        Forms\Components\TextInput::make('nome')
                            ->required()
                            ->maxLength(255)
                            ->label('Nome Commessa'),
                        
                        Forms\Components\Textarea::make('descrizione')
                            ->label('Descrizione')
                            ->rows(4),
                    ])->columns(2),
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
                
                Tables\Columns\TextColumn::make('cliente.nome')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('cliente.committente.nome')
                    ->label('Committente')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cliente')
                    ->relationship('cliente', 'nome'),
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
            'index' => Pages\ListCommessas::route('/'),
            'create' => Pages\CreateCommessa::route('/create'),
            'edit' => Pages\EditCommessa::route('/{record}/edit'),
        ];
    }
}

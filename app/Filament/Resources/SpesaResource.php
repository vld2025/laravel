<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpesaResource\Pages;
use App\Models\Spesa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SpesaResource extends Resource
{
    protected static ?string $model = Spesa::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    
    protected static ?string $modelLabel = 'Spesa';
    
    protected static ?string $pluralModelLabel = 'Spese';
    
    protected static ?string $navigationGroup = 'Gestione Personale';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dati Spesa')
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                        
                        Forms\Components\Select::make('anno')
                            ->options(array_combine(
                                range(date('Y') - 2, date('Y') + 1),
                                range(date('Y') - 2, date('Y') + 1)
                            ))
                            ->default(date('Y'))
                            ->required()
                            ->label('Anno'),
                        
                        Forms\Components\Select::make('mese')
                            ->options(Spesa::getMesi())
                            ->default(date('n'))
                            ->required()
                            ->label('Mese'),
                        
                        Forms\Components\FileUpload::make('file')
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->maxSize(50 * 1024) // 50MB
                            ->directory('spese')
                            ->label('File Spesa'),
                        
                        Forms\Components\TextInput::make('descrizione')
                            ->maxLength(255)
                            ->label('Descrizione'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anno')
                    ->label('Anno')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('mese')
                    ->label('Mese')
                    ->formatStateUsing(fn (string $state): string => Spesa::getMesi()[$state] ?? $state)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utente')
                    ->searchable()
                    ->visible(fn () => auth()->user()?->canViewAllData()),
                
                Tables\Columns\TextColumn::make('descrizione')
                    ->label('Descrizione')
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Caricato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('anno')
                    ->options(array_combine(
                        range(date('Y') - 2, date('Y') + 1),
                        range(date('Y') - 2, date('Y') + 1)
                    )),
                Tables\Filters\SelectFilter::make('mese')
                    ->options(Spesa::getMesi()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Spesa $record) => auth()->user()?->canViewAllData() || $record->user_id === auth()->id()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Spesa $record) => auth()->user()?->canViewAllData() || $record->user_id === auth()->id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->canViewAllData()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // User vede solo le proprie spese
        if (!auth()->user()?->canViewAllData()) {
            $query->where('user_id', auth()->id());
        }
        
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpesas::route('/'),
            'create' => Pages\CreateSpesa::route('/create'),
            'edit' => Pages\EditSpesa::route('/{record}/edit'),
        ];
    }
}

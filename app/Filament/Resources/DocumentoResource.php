<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentoResource\Pages;
use App\Models\Documento;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentoResource extends Resource
{
    protected static ?string $model = Documento::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $modelLabel = 'Documento';
    
    protected static ?string $pluralModelLabel = 'Documenti';
    
    protected static ?string $navigationGroup = 'Gestione Personale';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Documento')
                    ->schema([
                        Forms\Components\Select::make('tipo')
                            ->options(Documento::getTipi())
                            ->required()
                            ->live()
                            ->label('Tipo Documento'),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Utente')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn (Forms\Get $get) => $get('tipo') !== 'aziendale')
                            ->required(fn (Forms\Get $get) => $get('tipo') !== 'aziendale'),
                        
                        Forms\Components\Hidden::make('caricato_da')
                            ->default(auth()->id()),
                        
                        Forms\Components\TextInput::make('nome')
                            ->required()
                            ->maxLength(255)
                            ->label('Nome Documento'),
                        
                        Forms\Components\FileUpload::make('file')
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->maxSize(50 * 1024) // 50MB
                            ->directory('documenti')
                            ->label('File'),
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
                
                Tables\Columns\BadgeColumn::make('tipo')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'busta_paga',
                        'primary' => 'personale',
                        'warning' => 'aziendale',
                    ])
                    ->formatStateUsing(fn (string $state) => Documento::getTipi()[$state] ?? $state),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utente')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('caricatoDa.name')
                    ->label('Caricato da')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Caricato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options(Documento::getTipi())
                    ->label('Tipo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Documento $record) => $record->canUserAccess(auth()->user())),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Documento $record) => auth()->user()?->canViewAllData()),
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
        
        // User vede solo i documenti che può accedere
        if (!auth()->user()?->canViewAllData()) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('tipo', 'aziendale');
            });
        }
        
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocumentos::route('/'),
            'create' => Pages\CreateDocumento::route('/create'),
            'edit' => Pages\EditDocumento::route('/{record}/edit'),
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

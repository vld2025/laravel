<?php
namespace App\Filament\Resources;
use App\Filament\Resources\DocumentoResource\Pages;
use App\Filament\Resources\DocumentoResource\RelationManagers;
use App\Models\Documento;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentoResource extends Resource
{
    protected static ?string $model = Documento::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Documenti';
    protected static ?string $modelLabel = 'Documento';
    protected static ?string $pluralModelLabel = 'Documenti';

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        
        if ($user->isUser()) {
            return $form->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
                Forms\Components\Hidden::make('caricato_da')
                    ->default(fn () => auth()->id()),
                Forms\Components\Hidden::make('tipo')
                    ->default('personale'),
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome documento'),
                Forms\Components\FileUpload::make('file')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->disk('public')
                    ->directory('documenti')
                    ->downloadable()
                    ->openable()
                    ->label('File documento'),
            ]);
        }
        
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->options(User::pluck('name', 'id'))
                ->required()
                ->searchable()
                ->label('Utente')
                ->placeholder('Seleziona un utente'),
                
            Forms\Components\Select::make('caricato_da')
                ->options(User::pluck('name', 'id'))
                ->required()
                ->label('Caricato da')
                ->default(fn () => auth()->id()),
                
            Forms\Components\Select::make('tipo')
                ->options([
                    'busta_paga' => 'Busta Paga',
                    'aziendale' => 'Aziendale',
                    'personale' => 'Personale'
                ])
                ->required(),
                
            Forms\Components\TextInput::make('nome')
                ->required()
                ->maxLength(255)
                ->label('Nome documento'),
                
            Forms\Components\FileUpload::make('file')
                ->required()
                ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                ->disk('public')
                ->directory('documenti')
                ->downloadable()
                ->openable()
                ->label('File documento'),
        ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        
        if ($user->isUser()) {
            return $table
                ->query(Documento::query()->where(function($query) {
                    $userId = auth()->id();
                    $query->where('user_id', $userId)
                          ->orWhere('tipo', 'aziendale');
                }))
                ->columns([
                    Tables\Columns\TextColumn::make('caricatoDa.name')
                        ->sortable()
                        ->label('Caricato da')
                        ->toggleable(),
                    Tables\Columns\TextColumn::make('tipo')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'busta_paga' => 'success',
                            'aziendale' => 'warning',
                            'personale' => 'info',
                            default => 'gray'
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'busta_paga' => 'Busta Paga',
                            'aziendale' => 'Aziendale',
                            'personale' => 'Personale',
                            default => $state
                        })
                        ->sortable(),
                    Tables\Columns\TextColumn::make('nome')
                        ->searchable()
                        ->label('Nome documento')
                        ->toggleable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime('d/m/Y H:i')
                        ->sortable()
                        ->label('Caricato il')
                        ->toggleable(),
                ])
                ->filters([
                    Tables\Filters\SelectFilter::make('tipo')
                        ->options([
                            'busta_paga' => 'Busta Paga',
                            'aziendale' => 'Aziendale',
                            'personale' => 'Personale'
                        ])
                        ->label('Tipo documento'),
                    Tables\Filters\SelectFilter::make('caricato_da')
                        ->relationship('caricatoDa', 'name')
                        ->label('Caricato da'),
                ])
                ->actions([
                    Tables\Actions\Action::make('download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn (Documento $record): string => asset('storage/' . $record->file))
                        ->openUrlInNewTab(),
                ])
                ->bulkActions([])
                ->defaultSort('created_at', 'desc');
        }
        
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->label('Utente'),
                Tables\Columns\TextColumn::make('caricatoDa.name')
                    ->sortable()
                    ->label('Caricato da'),
                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'busta_paga' => 'success',
                        'aziendale' => 'warning',
                        'personale' => 'info',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'busta_paga' => 'Busta Paga',
                        'aziendale' => 'Aziendale',
                        'personale' => 'Personale',
                        default => $state
                    }),
                Tables\Columns\TextColumn::make('nome')
                    ->searchable()
                    ->label('Nome documento'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Caricato il')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'busta_paga' => 'Busta Paga',
                        'aziendale' => 'Aziendale',
                        'personale' => 'Personale'
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->label('Utente'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Documento $record): string => asset('storage/' . $record->file))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocumenti::route('/'),
            'create' => Pages\CreateDocumento::route('/create'),
            'edit' => Pages\EditDocumento::route('/{record}/edit'),
        ];
    }
}

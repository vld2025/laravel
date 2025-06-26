<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpesaResource\Pages;
use App\Models\Spesa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
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
            ->headerActions([
                // Azione PDF Mese Corrente
                
                // Azione Configurazione Automazione Email
                Tables\Actions\Action::make('configura_automazione')
                    ->label('Configura Automazione Email')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('info')
                    ->visible(fn () => auth()->user()?->canViewAllData())
                    ->url('/admin/automazione-pdfs')
                    ->openUrlInNewTab(),
                
                // Azione PDF Personalizzato
                Tables\Actions\Action::make('pdf_personalizzato')
                    ->label('PDF')
                    ->icon('heroicon-o-calendar')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('anno')
                            ->label('Anno')
                            ->options(array_combine(
                                range(date('Y') - 2, date('Y') + 1),
                                range(date('Y') - 2, date('Y') + 1)
                            ))
                            ->default(date('Y'))
                            ->required(),
                        
                        Forms\Components\Select::make('mese')
                            ->label('Mese')
                            ->options(Spesa::getMesi())
                            ->default(date('n'))
                            ->required(),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Utente')
                            ->options(fn () => auth()->user()->canViewAllData() 
                                ? \App\Models\User::pluck('name', 'id')->toArray()
                                : [auth()->id() => auth()->user()->name]
                            )
                            ->default(auth()->id())
                            ->required()
                            ->visible(fn () => auth()->user()->canViewAllData()),
                    ])
                    ->action(function (array $data) {
                        $url = route('pdf.spese-mensili', [
                            'user_id' => $data['user_id'] ?? auth()->id(),
                            'anno' => $data['anno'],
                            'mese' => $data['mese'],
                            'download' => true
                        ]);
                        
                        Notification::make()
                            ->title('PDF Generato')
                            ->body('Il PDF delle spese è stato generato con successo.')
                            ->success()
                            ->send();
                        
                        return redirect($url);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Spesa $record) => auth()->user()?->canViewAllData() || $record->user_id === auth()->id()),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Spesa $record) => auth()->user()?->canViewAllData() || $record->user_id === auth()->id()),
                
                // Azione PDF per singola spesa (by utente/mese)
                // Azione Anteprima file singolo
                Tables\Actions\Action::make('anteprima_file')
                    ->label('Anteprima')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (Spesa $record) => asset('storage/' . $record->file))
                    ->openUrlInNewTab(),
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

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::query();
        if (!auth()->user()?->canViewAllData()) {
            $query->where('user_id', auth()->id());
        }
        $count = $query->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getNavigationBadge() ? "primary" : null;
    }
}

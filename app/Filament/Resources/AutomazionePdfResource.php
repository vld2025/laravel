<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutomazionePdfResource\Pages;
use App\Models\AutomazionePdf;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class AutomazionePdfResource extends Resource
{
    protected static ?string $model = AutomazionePdf::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $modelLabel = 'Automazione PDF';
    
    protected static ?string $pluralModelLabel = 'Automazione PDF';
    
    protected static ?string $navigationGroup = 'Amministrazione';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configurazione Base')
                    ->schema([
                        Forms\Components\Toggle::make('attiva')
                            ->label('Automazione Attiva')
                            ->helperText('Abilita/disabilita l\'invio automatico dei PDF mensili'),
                        
                        Forms\Components\Select::make('giorno_invio')
                            ->label('Giorno di Invio del Mese')
                            ->options(array_combine(range(1, 31), range(1, 31)))
                            ->default(1)
                            ->required()
                            ->helperText('Giorno del mese in cui inviare i PDF (1-31)'),
                        
                        Forms\Components\TimePicker::make('ora_invio')
                            ->label('Ora di Invio')
                            ->default('09:00')
                            ->required()
                            ->seconds(false)
                            ->helperText('Ora precisa dell\'invio (formato 24h)'),
                    ])->columns(3),
                
                Forms\Components\Section::make('Configurazione Email')
                    ->schema([
                        Forms\Components\TagsInput::make('email_destinatari')
                            ->label('Email Destinatari')
                            ->required()
                            ->placeholder('Inserisci email e premi Enter')
                            ->helperText('Inserisci tutte le email che riceveranno i PDF'),
                        
                        Forms\Components\TextInput::make('email_oggetto')
                            ->label('Oggetto Email')
                            ->required()
                            ->default('Spese Mensili VLD Service - {mese} {anno}')
                            ->helperText('Usa {mese} e {anno} per valori dinamici'),
                        
                        Forms\Components\Textarea::make('email_messaggio')
                            ->label('Messaggio Email')
                            ->rows(4)
                            ->default('In allegato trovate il riepilogo delle spese mensili con tutti i documenti allegati.')
                            ->helperText('Messaggio che accompagna l\'email con i PDF'),
                    ])->columns(1),
                
                Forms\Components\Section::make('Filtri Utenti')
                    ->schema([
                        Forms\Components\Select::make('utenti_inclusi')
                            ->label('Utenti da Includere')
                            ->multiple()
                            ->options(User::where('role', 'user')->pluck('name', 'id'))
                            ->helperText('Lascia vuoto per includere tutti gli utenti'),
                        
                        Forms\Components\Toggle::make('solo_con_spese')
                            ->label('Solo Utenti con Spese')
                            ->default(true)
                            ->helperText('Invia PDF solo per utenti che hanno spese nel mese'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Informazioni Esecuzione')
                    ->schema([
                        Forms\Components\Placeholder::make('prossima_esecuzione')
                            ->label('Prossima Esecuzione')
                            ->content(fn ($record) => $record?->prossima_esecuzione?->format('d/m/Y H:i') ?? 'Non programmata'),
                        
                        Forms\Components\Placeholder::make('ultima_esecuzione')
                            ->label('Ultima Esecuzione')
                            ->content(fn ($record) => $record?->ultima_esecuzione?->format('d/m/Y H:i') ?? 'Mai eseguita'),
                        
                        Forms\Components\Placeholder::make('stato')
                            ->label('Stato')
                            ->content(fn ($record) => $record?->attiva ? '✅ Attiva' : '❌ Disattivata'),
                    ])->columns(3)
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('attiva')
                    ->label('Stato')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('giorno_invio')
                    ->label('Giorno')
                    ->suffix('° del mese'),
                
                Tables\Columns\TextColumn::make('ora_invio_formatted')
                    ->label('Ora'),
                
                Tables\Columns\TextColumn::make('email_destinatari_formatted')
                    ->label('Destinatari')
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('prossima_esecuzione')
                    ->label('Prossima Esecuzione')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Non programmata'),
                
                Tables\Columns\TextColumn::make('ultima_esecuzione')
                    ->label('Ultima Esecuzione')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Mai eseguita'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('attiva')
                    ->label('Stato'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('test_invio')
                    ->label('Test Invio')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->action(function (AutomazionePdf $record) {
                        // TODO: Implementare test invio
                        Notification::make()
                            ->title('Test Invio')
                            ->body('Funzionalità in fase di implementazione')
                            ->info()
                            ->send();
                    }),
                
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
            'index' => Pages\ListAutomazionePdfs::route('/'),
            'create' => Pages\CreateAutomazionePdf::route('/create'),
            'edit' => Pages\EditAutomazionePdf::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewAllData() ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::where('attiva', true)->count();
        return $active > 0 ? (string) $active : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getNavigationBadge() ? 'success' : null;
    }
}

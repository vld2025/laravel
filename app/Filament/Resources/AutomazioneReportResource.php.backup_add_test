<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutomazioneReportResource\Pages;
use App\Models\AutomazioneReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AutomazioneReportResource extends Resource
{
    protected static ?string $model = AutomazioneReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Automazioni';

    protected static ?string $modelLabel = 'Automazione Report';

    protected static ?string $pluralModelLabel = 'Automazioni Report';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configurazione Base')
                    ->schema([
                        Forms\Components\TextInput::make('nome')
                            ->required()
                            ->maxLength(255)
                            ->label('Nome Configurazione')
                            ->placeholder('Es: Invio Report Giornaliero'),

                        Forms\Components\Toggle::make('attivo')
                            ->label('Attivo')
                            ->helperText('Attiva/disattiva l\'automazione')
                            ->default(false),

                        Forms\Components\TimePicker::make('ora_invio')
                            ->required()
                            ->label('Ora di Invio')
                            ->helperText('Ora giornaliera per l\'invio automatico')
                            ->default('18:00'),
                    ])->columns(3),

                Forms\Components\Section::make('Destinatari e Lingue')
                    ->schema([
                        Forms\Components\TagsInput::make('email_destinatari')
                            ->required()
                            ->label('Email Destinatari')
                            ->helperText('Inserisci le email dei destinatari')
                            ->placeholder('email@esempio.com'),

                        Forms\Components\CheckboxList::make('lingue')
                            ->required()
                            ->label('Lingue Report')
                            ->options(AutomazioneReport::getAvailableLanguages())
                            ->default(['it'])
                            ->helperText('Seleziona le lingue per i report')
                            ->columns(2),
                    ])->columns(1),

                Forms\Components\Section::make('Opzioni Avanzate')
                    ->schema([
                        Forms\Components\Toggle::make('raggruppa_per_giorno')
                            ->label('Raggruppa per Giorno')
                            ->helperText('Raggruppa tutti i report del giorno in una singola email')
                            ->default(true),

                        Forms\Components\Toggle::make('includi_dettagli_ore')
                            ->label('Includi Dettagli Ore')
                            ->helperText('Mostra ore, chilometri e dettagli tecnici nelle email')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Personalizzazione Prompt AI')
                    ->schema([
                        Forms\Components\Textarea::make('prompt_personalizzato')
                            ->label('Prompt Personalizzato (Opzionale)')
                            ->rows(6)
                            ->helperText('Personalizza il prompt per la generazione dei report AI. Lascia vuoto per usare il prompt predefinito. Usa {data}, {tecnico}, {cliente}, etc. come placeholder.')
                            ->placeholder("Esempio: Genera un report professionale per il lavoro svolto in data {data} dal tecnico {tecnico} presso il cliente {cliente}...")
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Note')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label('Note')
                            ->rows(3)
                            ->placeholder('Note aggiuntive sulla configurazione...'),
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

                Tables\Columns\IconColumn::make('attivo')
                    ->label('Stato')
                    ->boolean()
                    ->trueIcon('heroicon-o-play')
                    ->falseIcon('heroicon-o-pause')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('ora_invio')
                    ->label('Ora Invio')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('email_destinatari')
                    ->label('Destinatari')
                    ->getStateUsing(fn (AutomazioneReport $record): string => 
                        count($record->email_destinatari) . ' email'
                    )
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('lingue')
                    ->label('Lingue')
                    ->getStateUsing(fn (AutomazioneReport $record): string => 
                        implode(', ', array_map('strtoupper', $record->lingue))
                    )
                    ->badge()
                    ->color('warning'),

                Tables\Columns\IconColumn::make('includi_dettagli_ore')
                    ->label('Dettagli')
                    ->boolean()
                    ->trueIcon('heroicon-o-clock')
                    ->falseIcon('heroicon-o-minus'),

                Tables\Columns\TextColumn::make('ultimo_invio')
                    ->label('Ultimo Invio')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Mai inviato'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('attivo')
                    ->label('Stato')
                    ->trueLabel('Solo attivi')
                    ->falseLabel('Solo inattivi')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutomazioneReports::route('/'),
            'create' => Pages\CreateAutomazioneReport::route('/create'),
            'edit' => Pages\EditAutomazioneReport::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('attivo', true)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getNavigationBadge() ? "success" : null;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewAllData() ?? false;
    }
}

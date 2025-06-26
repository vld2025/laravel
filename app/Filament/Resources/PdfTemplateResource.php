<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PdfTemplateResource\Pages;
use App\Models\PdfTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PdfTemplateResource extends Resource
{
    protected static ?string $model = PdfTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Amministrazione';
    
    protected static ?string $modelLabel = 'Template PDF';
    
    protected static ?string $pluralModelLabel = 'Template PDF';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Base')
                    ->schema([
                        Forms\Components\TextInput::make('nome')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('Nome Template')
                            ->placeholder('es. report_giornalieri_custom'),
                            
                        Forms\Components\TextInput::make('descrizione')
                            ->label('Descrizione')
                            ->placeholder('Descrizione del template'),
                            
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo Template')
                            ->options(PdfTemplate::getTipiDisponibili())
                            ->required(),
                            
                        Forms\Components\Toggle::make('attivo')
                            ->label('Attivo')
                            ->default(true),
                    ])->columns(2),
                
                Forms\Components\Section::make('Impostazioni Pagina')
                    ->schema([
                        Forms\Components\Select::make('formato_pagina')
                            ->label('Formato Pagina')
                            ->options(PdfTemplate::getFormatiPagina())
                            ->default('A4'),
                            
                        Forms\Components\Select::make('orientamento')
                            ->label('Orientamento')
                            ->options(PdfTemplate::getOrientamenti())
                            ->default('portrait'),
                            
                        Forms\Components\Section::make('Margini (mm)')
                            ->schema([
                                Forms\Components\TextInput::make('margini.top')
                                    ->label('Superiore')
                                    ->numeric()
                                    ->default(20),
                                Forms\Components\TextInput::make('margini.right')
                                    ->label('Destro')
                                    ->numeric()
                                    ->default(20),
                                Forms\Components\TextInput::make('margini.bottom')
                                    ->label('Inferiore')
                                    ->numeric()
                                    ->default(20),
                                Forms\Components\TextInput::make('margini.left')
                                    ->label('Sinistro')
                                    ->numeric()
                                    ->default(20),
                            ])->columns(4),
                    ]),
                
                Forms\Components\Section::make('Template HTML')
                    ->schema([
                        Forms\Components\Placeholder::make('help')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Variabili Disponibili:</h4>
                                    <ul class="list-disc list-inside text-sm space-y-1">
                                        <li><code>{{ $data }}</code> - Data del report</li>
                                        <li><code>{{ $identificativo }}</code> - Nome tecnico</li>
                                        <li><code>@foreach($reports as $report)</code> - Loop sui report</li>
                                        <li><code>{{ $report->user->name }}</code> - Nome utente</li>
                                        <li><code>{{ $report->cliente->nome }}</code> - Nome cliente</li>
                                        <li><code>@if($includi_dettagli_ore)</code> - Condizione ore/km</li>
                                    </ul>
                                </div>
                            ')),
                            
                        Forms\Components\Textarea::make('template_html')
                            ->label('Codice HTML/Blade')
                            ->required()
                            ->rows(20)
                            ->columnSpanFull()
                            ->extraAttributes(['style' => 'font-family: monospace']),
                    ]),
                
                Forms\Components\Section::make('CSS Personalizzato')
                    ->schema([
                        Forms\Components\Textarea::make('css_personalizzato')
                            ->label('CSS Aggiuntivo')
                            ->rows(10)
                            ->placeholder('/* CSS personalizzato qui */')
                            ->extraAttributes(['style' => 'font-family: monospace']),
                    ])->collapsed(),
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
                    
                Tables\Columns\TextColumn::make('descrizione')
                    ->label('Descrizione')
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => 
                        PdfTemplate::getTipiDisponibili()[$state] ?? $state
                    ),
                    
                Tables\Columns\IconColumn::make('attivo')
                    ->label('Attivo')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('formato_pagina')
                    ->label('Formato'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ultima Modifica')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options(PdfTemplate::getTipiDisponibili()),
                    
                Tables\Filters\TernaryFilter::make('attivo')
                    ->label('Stato'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Anteprima')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (PdfTemplate $record): string => 
                        route('pdf-template.preview', $record)
                    )
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplica')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('warning')
                    ->action(function (PdfTemplate $record) {
                        $newTemplate = $record->replicate();
                        $newTemplate->nome = $record->nome . '_copia';
                        $newTemplate->descrizione = $record->descrizione . ' (Copia)';
                        $newTemplate->attivo = false;
                        $newTemplate->save();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Template duplicato')
                            ->success()
                            ->send();
                    }),
                    
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
            'index' => Pages\ListPdfTemplates::route('/'),
            'create' => Pages\CreatePdfTemplate::route('/create'),
            'edit' => Pages\EditPdfTemplate::route('/{record}/edit'),
        ];
    }
    
    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewAllData() ?? false;
    }
}

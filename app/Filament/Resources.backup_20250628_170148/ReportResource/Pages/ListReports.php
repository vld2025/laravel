<?php
namespace App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Committente;
use App\Exports\ReportMensileExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms;
use Filament\Notifications\Notification;
class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->visible(fn () => auth()->user()?->canViewAllData())
                ->form([
                    Forms\Components\Select::make('committente_id')
                        ->label('Committente')
                        ->required()
                        ->options(Committente::pluck('nome', 'id'))
                        ->searchable(),
                    Forms\Components\Select::make('anno')
                        ->label('Anno')
                        ->required()
                        ->options(array_combine(range(2020, 2030), range(2020, 2030)))
                        ->default(date('Y')),
                    Forms\Components\Select::make('mese')
                        ->label('Mese')
                        ->required()
                        ->options([
                            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
                            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
                            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                        ])
                        ->default(date('n'))
                ])
                ->action(function (array $data) {
                    $committente = Committente::find($data['committente_id']);
                    $mesi = [
                        1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
                        5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
                        9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                    ];
                    $nomeFile = "Report_{" . $committente->nome . "}_{" . $mesi[$data['mese']] . "}_{" . $data['anno'] . "}.xlsx";
                    Notification::make()
                        ->title('Export completato!')
                        ->body("File Excel generato: " . $nomeFile)
                        ->success()
                        ->send();
                    return Excel::download(
                        new ReportMensileExport($data['committente_id'], $data['anno'], $data['mese']),
                        $nomeFile
                    );
                }),
            Actions\CreateAction::make(),
        ];
    }
}

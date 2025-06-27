<?php

namespace App\Exports;

use App\Models\Report;
use App\Models\ImpostazioneFattura;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class ReportMensileExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell, WithEvents
{
    protected $committente_id;
    protected $anno;
    protected $mese;
    protected $data_collection;

    public function __construct($committente_id, $anno, $mese)
    {
        $this->committente_id = $committente_id;
        $this->anno = $anno;
        $this->mese = $mese;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function collection()
    {
        $this->data_collection = Report::with(['user', 'committente', 'cliente', 'commessa'])
            ->whereHas('commessa.cliente', function($q) { 
                $q->where('committente_id', $this->committente_id); 
            })
            ->whereYear('data', $this->anno)
            ->whereMonth('data', $this->mese)
            ->orderBy('data')
            ->orderBy('user_id')
            ->get();

        return $this->data_collection;
    }

    public function headings(): array
    {
        return [
            'Data',
            'Tecnico',
            'Cliente',
            'Commessa',
            'Ore Lavorate',
            'Ore Viaggio',
            'Totale Ore',
            'Km Auto',
            'Trasferta',
            'Notturno',
            'Festivo'
        ];
    }

    public function map($report): array
    {
        // Giorni della settimana in italiano
        $giorni = [
            'Monday' => 'Lunedì',
            'Tuesday' => 'Martedì',
            'Wednesday' => 'Mercoledì',
            'Thursday' => 'Giovedì',
            'Friday' => 'Venerdì',
            'Saturday' => 'Sabato',
            'Sunday' => 'Domenica'
        ];

        $giorno_settimana = $giorni[$report->data->format('l')] ?? $report->data->format('l');
        $data_formattata = $giorno_settimana . ' ' . $report->data->format('d/m/Y');

        return [
            $data_formattata,
            $report->user->name,
            $report->cliente->nome,
            $report->commessa->nome,
            $report->ore_lavorate,
            $report->ore_viaggio,
            $report->ore_lavorate + $report->ore_viaggio,
            $report->km_auto,
            $report->trasferta ? 'Sì' : 'No',
            $report->notturno ? 'Sì' : 'No',
            $report->festivo ? 'Sì' : 'No'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // FREEZE DELLA PRIMA RIGA (intestazioni fisse)
                $sheet->freezePane('A2');

                // Calcola ultima riga e riga totali
                $lastRow = $this->data_collection ? $this->data_collection->count() + 1 : 2;
                $totalRow = $lastRow + 1;

                // Calcola i totali
                $totale_ore_lavorate = $this->data_collection ? $this->data_collection->sum('ore_lavorate') : 0;
                $totale_ore_viaggio = $this->data_collection ? $this->data_collection->sum('ore_viaggio') : 0;
                $totale_ore = $totale_ore_lavorate + $totale_ore_viaggio;
                $totale_km = $this->data_collection ? $this->data_collection->sum('km_auto') : 0;
                $totale_trasferte = $this->data_collection ? $this->data_collection->where('trasferta', true)->count() : 0;

                // Aggiungi riga totali
                $sheet->setCellValue('A' . $totalRow, 'TOTALI');
                $sheet->setCellValue('E' . $totalRow, $totale_ore_lavorate);
                $sheet->setCellValue('F' . $totalRow, $totale_ore_viaggio);
                $sheet->setCellValue('G' . $totalRow, $totale_ore);
                $sheet->setCellValue('H' . $totalRow, $totale_km);
                $sheet->setCellValue('I' . $totalRow, $totale_trasferte); // Conteggio trasferte (spostato in colonna I)
                $sheet->setCellValue('J' . $totalRow, ''); // Colonna Notturno vuota
                $sheet->setCellValue('K' . $totalRow, ''); // Colonna Festivo vuota
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->data_collection ? $this->data_collection->count() + 1 : 2;
        $totalRow = $lastRow + 1;

        return [
            // Header con sfondo grigio e testo bold
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ]
            ],
            // TUTTA la riga totali con sfondo giallo (A-K)
            'A' . $totalRow . ':K' . $totalRow => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF3C7']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ]
            ],
            // Bordi per tutti i dati
            'A1:K' . $totalRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ]
            ]
        ];
    }
}

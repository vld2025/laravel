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
    protected $periodo_inizio;
    protected $periodo_fine;
    protected $data_collection;

    public function __construct($committente_id, $anno, $mese)
    {
        $this->committente_id = $committente_id;
        $this->anno = $anno;
        $this->mese = $mese;
        
        $this->calcolaPeriodoFatturazione();
    }

    public function startCell(): string
    {
        return 'A1';
    }

    private function calcolaPeriodoFatturazione()
    {
        $impostazioni = ImpostazioneFattura::where('committente_id', $this->committente_id)->first();
        $giorno_fatturazione = $impostazioni ? $impostazioni->giorno_fatturazione : 20;

        $mese_precedente = $this->mese == 1 ? 12 : $this->mese - 1;
        $anno_precedente = $this->mese == 1 ? $this->anno - 1 : $this->anno;

        $this->periodo_inizio = Carbon::create($anno_precedente, $mese_precedente, $giorno_fatturazione)->addDay();
        $this->periodo_fine = Carbon::create($this->anno, $this->mese, $giorno_fatturazione);
    }

    public function collection()
    {
        $this->data_collection = Report::with(['user', 'committente', 'cliente', 'commessa'])
            ->where('committente_id', $this->committente_id)
            ->whereBetween('data', [$this->periodo_inizio, $this->periodo_fine])
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
            'Notturno',
            'Trasferta'
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
            $report->notturno ? 'Sì' : 'No',
            $report->trasferta ? 'Sì' : 'No'
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

                // Aggiungi riga totali
                $sheet->setCellValue('A' . $totalRow, 'TOTALI');
                $sheet->setCellValue('E' . $totalRow, $totale_ore_lavorate);
                $sheet->setCellValue('F' . $totalRow, $totale_ore_viaggio);
                $sheet->setCellValue('G' . $totalRow, $totale_ore);
                $sheet->setCellValue('H' . $totalRow, $totale_km);
                $sheet->setCellValue('I' . $totalRow, ''); // Colonna Notturno vuota
                $sheet->setCellValue('J' . $totalRow, ''); // Colonna Trasferta vuota
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
            // TUTTA la riga totali con sfondo giallo (A-J)
            'A' . $totalRow . ':J' . $totalRow => [
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
            'A1:J' . $totalRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ]
            ]
        ];
    }
}

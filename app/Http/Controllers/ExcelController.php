<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportMensileExport;
use App\Models\Committente;
use App\Models\ImpostazioneFattura;
use Carbon\Carbon;

class ExcelController extends Controller
{
    public function exportReportMensile(Request $request)
    {
        $request->validate([
            'committente_id' => 'required|exists:committenti,id',
            'anno' => 'required|integer|min:2020|max:2030',
            'mese' => 'required|integer|min:1|max:12'
        ]);

        $committente = Committente::findOrFail($request->committente_id);
        $anno = $request->anno;
        $mese = $request->mese;

        // Calcola il nome del file
        $mesi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];

        $nomeFile = "Report_{$committente->nome}_{$mesi[$mese]}_{$anno}.xlsx";

        // Esporta l'Excel
        return Excel::download(
            new ReportMensileExport($request->committente_id, $anno, $mese),
            $nomeFile
        );
    }

    public function formExport()
    {
        $committenti = Committente::orderBy('nome')->get();
        $anni = range(2020, 2030);
        $mesi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];

        return view('export.form', compact('committenti', 'anni', 'mesi'));
    }

    public function previewPeriodo(Request $request)
    {
        $request->validate([
            'committente_id' => 'required|exists:committenti,id',
            'anno' => 'required|integer|min:2020|max:2030',
            'mese' => 'required|integer|min:1|max:12'
        ]);

        // Calcola il periodo di fatturazione
        $impostazioni = ImpostazioneFattura::where('committente_id', $request->committente_id)->first();
        $giorno_fatturazione = $impostazioni ? $impostazioni->giorno_fatturazione : 20;

        $mese_precedente = $request->mese == 1 ? 12 : $request->mese - 1;
        $anno_precedente = $request->mese == 1 ? $request->anno - 1 : $request->anno;

        $periodo_inizio = Carbon::create($anno_precedente, $mese_precedente, $giorno_fatturazione)->addDay();
        $periodo_fine = Carbon::create($request->anno, $request->mese, $giorno_fatturazione);

        return response()->json([
            'periodo_inizio' => $periodo_inizio->format('d/m/Y'),
            'periodo_fine' => $periodo_fine->format('d/m/Y'),
            'giorno_fatturazione' => $giorno_fatturazione
        ]);
    }
}

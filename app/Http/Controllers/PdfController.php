<?php
namespace App\Http\Controllers;
use App\Models\Spesa;
use App\Models\User;
use App\Models\Report;
use App\Services\BackupService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{
    private $backupService;
    
    public function __construct()
    {
        $this->backupService = new BackupService();
    }
    
    /**
     * Genera PDF spese mensili per un utente
     */
    public function generateSpeseMensili(Request $request)
    {
        $userId = $request->get('user_id', auth()->id());
        $anno = $request->get('anno', date('Y'));
        $mese = $request->get('mese', date('n'));
        
        // Verifica permessi
        if (!auth()->user()->canViewAllData() && $userId != auth()->id()) {
            abort(403, 'Non autorizzato a visualizzare le spese di altri utenti');
        }
        
        $user = User::findOrFail($userId);
        
        // Recupera le spese del mese
        $spese = Spesa::where('user_id', $userId)
            ->where('anno', $anno)
            ->where('mese', $mese)
            ->orderBy('created_at')
            ->get();
            
        // Nomi dei mesi
        $mesiNomi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];
        
        $meseNome = $mesiNomi[$mese];
        
        // Genera PDF
        $pdf = Pdf::loadView('pdf.spese-mensili', compact(
            'user', 'spese', 'anno', 'mese', 'meseNome'
        ));
        
        $pdf->setPaper('A4', 'portrait');
        
        // Nome file
        $fileName = sprintf(
            'spese_%s_%s_%s.pdf',
            str_replace(' ', '_', strtolower($user->name)),
            $mese,
            $anno
        );
        
        // Backup su NAS
        try {
            $this->backupService->backupPDF($pdf->output(), 'Spese', $fileName);
        } catch (\Exception $e) {
            Log::error('Errore backup PDF spese: ' . $e->getMessage());
        }
        
        // Se richiesto il download diretto
        if ($request->get('download', true)) {
            return $pdf->download($fileName);
        }
        
        // Salva il file e restituisce il path
        $filePath = "pdf/spese/{$fileName}";
        Storage::disk('public')->put($filePath, $pdf->output());
        
        return response()->json([
            'success' => true,
            'file_path' => $filePath,
            'download_url' => Storage::disk('public')->url($filePath),
            'file_name' => $fileName
        ]);
    }
    
    /**
     * Genera PDF spese per tutti gli utenti (solo admin/manager)
     */
    public function generateSpeseMensiliTuttiUtenti(Request $request)
    {
        // Solo admin e manager possono generare PDF per tutti
        if (!auth()->user()->canViewAllData()) {
            abort(403, 'Non autorizzato');
        }
        
        $anno = $request->get('anno', date('Y'));
        $mese = $request->get('mese', date('n'));
        $users = User::where('role', 'user')->get();
        $generatedFiles = [];
        
        foreach ($users as $user) {
            $spese = Spesa::where('user_id', $user->id)
                ->where('anno', $anno)
                ->where('mese', $mese)
                ->get();
                
            if ($spese->count() > 0) {
                // Nomi dei mesi
                $mesiNomi = [
                    1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
                    5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
                    9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                ];
                
                $meseNome = $mesiNomi[$mese];
                
                // Genera PDF
                $pdf = Pdf::loadView('pdf.spese-mensili', compact(
                    'user', 'spese', 'anno', 'mese', 'meseNome'
                ));
                
                $pdf->setPaper('A4', 'portrait');
                
                // Nome file
                $fileName = sprintf(
                    'spese_%s_%s_%s.pdf',
                    str_replace(' ', '_', strtolower($user->name)),
                    $mese,
                    $anno
                );
                
                $filePath = "pdf/spese/{$fileName}";
                Storage::disk('public')->put($filePath, $pdf->output());
                
                // Backup su NAS
                try {
                    $this->backupService->backupPDF($pdf->output(), 'Spese', $fileName);
                } catch (\Exception $e) {
                    Log::error('Errore backup PDF spese: ' . $e->getMessage());
                }
                
                $generatedFiles[] = [
                    'user_name' => $user->name,
                    'file_path' => $filePath,
                    'download_url' => Storage::disk('public')->url($filePath),
                    'file_name' => $fileName,
                    'spese_count' => $spese->count()
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'files' => $generatedFiles,
            'message' => count($generatedFiles) . ' file PDF generati'
        ]);
    }
    
    /**
     * Download di un PDF esistente
     */
    public function downloadPdf($filePath)
    {
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File non trovato');
        }
        
        return Storage::disk('public')->download($filePath);
    }
    
    /**
     * Genera PDF fattura con opzione Swiss QR Bill
     */
    public function generateFattura(Request $request, $fatturaId)
    {
        $fattura = \App\Models\Fattura::with(['committente'])->findOrFail($fatturaId);
        $impostazioni = \App\Models\ImpostazioneFattura::where('committente_id', $fattura->committente_id)->first();
        
        if (!$impostazioni) {
            abort(404, 'Impostazioni fatturazione non trovate per questo committente');
        }
        
        // Verifica permessi
        if (!auth()->user()->canViewAllData()) {
            abort(403, 'Non autorizzato a generare fatture');
        }
        
        $useQrBill = ($request->get("qr_bill", false) || $request->route()->getName() === "pdf.fattura-qr") && $impostazioni->swiss_qr_bill;
        $qrCode = null;
        
        // Genera Swiss QR Bill se richiesto
        if ($useQrBill) {
            $qrCode = $this->generateSwissQrBill($fattura, $impostazioni);
        }
        
        // Array mesi in italiano
        $mesi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];
        
        // Genera PDF
        $pdf = Pdf::loadView('pdf.fattura', [
            'fattura' => $fattura,
            'impostazioni' => $impostazioni,
            'qrCode' => $qrCode,
            'mesi' => $mesi
        ]);
        
        $pdf->setPaper('A4');
        $filename = "fattura_{$fattura->numero}" . ($useQrBill ? '_qr' : '') . ".pdf";
        
        // Backup su NAS
        try {
            $this->backupService->backupPDF($pdf->output(), 'Fatture', $filename);
        } catch (\Exception $e) {
            Log::error('Errore backup PDF fattura: ' . $e->getMessage());
        }
        
        return $pdf->download($filename);
    }
    
    /**
     * Genera Swiss QR Bill per la fattura
     */
    private function generateSwissQrBill(\App\Models\Fattura $fattura, \App\Models\ImpostazioneFattura $impostazioni): string
    {
        $qrBillService = new \App\Services\SwissQrBillService();
        
        // Prepara i dati per il QR Bill
        $qrData = [
            'iban' => $impostazioni->iban,
            'creditor_name' => $impostazioni->qr_creditor_name ?? $impostazioni->committente->nome,
            'creditor_address' => $impostazioni->qr_creditor_address,
            'creditor_postal_code' => $impostazioni->qr_creditor_postal_code,
            'creditor_city' => $impostazioni->qr_creditor_city,
            'creditor_country' => $impostazioni->qr_creditor_country,
            'amount' => number_format($fattura->totale, 2, '.', ''),
            'currency' => 'CHF',
            'debtor_name' => $fattura->committente->nome,
            'debtor_address' => $fattura->committente->indirizzo ?? '',
            'debtor_postal_code' => '',
            'debtor_city' => '',
            'debtor_country' => 'CH',
            'reference' => $qrBillService->generateQrReference($fattura->committente_id, str_replace('-', '', $fattura->numero)),
            'additional_info' => $impostazioni->qr_additional_info ?? "Fattura {$fattura->numero}",
            'billing_info' => $impostazioni->qr_billing_info ?? ''
        ];
        
        return $qrBillService->generateQrBill($qrData);
    }

    /**
     * Genera PDF reports mensili per tutti gli utenti
     */
    public function generateReportsMensiliTuttiUtenti(Request $request)
    {
        // Solo admin e manager possono generare PDF per tutti
        if (!auth()->user()->canViewAllData()) {
            abort(403, 'Non autorizzato');
        }

        $anno = $request->get('anno', date('Y'));
        $mese = $request->get('mese', date('n'));
        $users = User::where('role', 'user')->get();
        $generatedFiles = [];

        foreach ($users as $user) {
            $reports = Report::where('user_id', $user->id)
                ->whereYear('data', $anno)
                ->whereMonth('data', $mese)
                ->with(['committente', 'cliente', 'commessa'])
                ->get();

            if ($reports->count() > 0) {
                // Nomi dei mesi
                $mesiNomi = [
                    1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
                    5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
                    9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                ];

                $meseNome = $mesiNomi[$mese];

                // Genera PDF
                $pdf = Pdf::loadView('pdf.reports-mensili', compact(
                    'user', 'reports', 'anno', 'mese', 'meseNome'
                ));

                $pdf->setPaper('A4', 'portrait');

                // Nome file
                $fileName = sprintf(
                    'reports_%s_%s_%s.pdf',
                    str_replace(' ', '_', strtolower($user->name)),
                    $mese,
                    $anno
                );

                $filePath = "pdf/reports/{$fileName}";
                Storage::disk('public')->put($filePath, $pdf->output());

                // Backup su NAS
                try {
                    $this->backupService->backupPDF($pdf->output(), 'Reports', $fileName);
                } catch (\Exception $e) {
                    Log::error('Errore backup PDF reports: ' . $e->getMessage());
                }

                $generatedFiles[] = [
                    'user_name' => $user->name,
                    'file_path' => $filePath,
                    'download_url' => Storage::disk('public')->url($filePath),
                    'file_name' => $fileName,
                    'reports_count' => $reports->count()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'files' => $generatedFiles,
            'message' => count($generatedFiles) . ' file PDF reports generati'
        ]);
    }
}

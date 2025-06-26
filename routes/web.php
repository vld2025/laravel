<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route PDF - protette da autenticazione
Route::middleware(['auth'])->group(function () {
    Route::get('/pdf/spese-mensili', [App\Http\Controllers\PdfController::class, 'generateSpeseMensili'])
        ->name('pdf.spese-mensili');
    
    Route::get('/pdf/spese-tutti-utenti', [App\Http\Controllers\PdfController::class, 'generateSpeseMensiliTuttiUtenti'])
        ->name('pdf.spese-tutti-utenti');
    
    Route::get('/pdf/download/{filePath}', [App\Http\Controllers\PdfController::class, 'downloadPdf'])
        ->name('pdf.download')
        ->where('filePath', '.*');
    
    // Route per fatture PDF
    Route::get('/pdf/fattura/{fattura}', [App\Http\Controllers\PdfController::class, 'generateFattura'])
        ->name('pdf.fattura');
    
    Route::get('/pdf/fattura/{fattura}/qr', [App\Http\Controllers\PdfController::class, 'generateFattura'])
        ->name('pdf.fattura-qr')
        ->defaults('qr_bill', true);
});

// Route per anteprima template PDF
Route::get('/pdf-template/{template}/preview', function (\App\Models\PdfTemplate $template) {
    // Dati di esempio per l'anteprima
    $sampleData = [
        'data' => now(),
        'identificativo' => 'ANTEPRIMA TEMPLATE',
        'reports' => \App\Models\Report::with(['user', 'cliente', 'commessa'])
            ->limit(3)
            ->get(),
        'includi_dettagli_ore' => true,
        'reportContent' => [
            1 => 'Questo è un esempio di report generato da AI per l\'anteprima del template.'
        ]
    ];
    
    // Renderizza il template
    $html = $template->renderizzaTemplate($sampleData);
    
    // Genera PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
    $pdf->setPaper($template->formato_pagina, $template->orientamento);
    
    if ($template->margini) {
        $pdf->setOptions([
            'margin_top' => $template->margini['top'] ?? 20,
            'margin_right' => $template->margini['right'] ?? 20,
            'margin_bottom' => $template->margini['bottom'] ?? 20,
            'margin_left' => $template->margini['left'] ?? 20,
        ]);
    }
    
    return $pdf->stream('anteprima_template.pdf');
})->name('pdf-template.preview')->middleware(['auth']);

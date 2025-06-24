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
});

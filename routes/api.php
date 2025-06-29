
// ChatGPT Document Scanner
Route::post('/process-document-chatgpt', function (Illuminate\Http\Request $request) {
    $aiService = app(\App\Services\AIService::class);
    
    $request->validate([
        'image' => 'required|string',
        'task' => 'required|string'
    ]);
    
    if ($request->task !== 'document_scan_enhance') {
        return response()->json(['success' => false, 'error' => 'Task non supportato']);
    }
    
    $result = $aiService->processDocumentScan($request->image);
    
    return response()->json($result);
})->middleware(['web', 'auth']);

Route::post('/api/process-document-simple', function(Request $request) {
    $file = $request->file('document_image');
    $aiService = app(\App\Services\AIService::class);
    
    // Salva temporaneamente
    $path = $file->store('temp', 'public');
    
    // Processa con ChatGPT (ritaglio + miglioramento)
    $result = $aiService->processDocumentScan($path);
    
    // Pulisci temp
    Storage::disk('public')->delete($path);
    
    return response()->json($result);
})->middleware(['web', 'auth']);

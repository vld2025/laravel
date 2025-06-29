<div class="w-full">
    @if($fileUrl && $fileType)
        <div class="border rounded-lg overflow-hidden bg-gray-50">
            @if(in_array(strtolower($fileType), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                {{-- Anteprima immagine --}}
                <div class="relative">
                    <img src="{{ $fileUrl }}" 
                         alt="{{ $documento->nome }}" 
                         class="w-full h-auto max-h-96 object-contain mx-auto"
                         loading="lazy">
                    
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <p class="text-white text-sm font-medium">{{ $documento->nome }}</p>
                        <p class="text-white/80 text-xs">Immagine • {{ strtoupper($fileType) }}</p>
                    </div>
                </div>
                
            @elseif(strtolower($fileType) === 'pdf')
                {{-- Anteprima PDF --}}
                <div class="p-6 text-center">
                    <div class="flex flex-col items-center space-y-4">
                        <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $documento->nome }}</h3>
                            <p class="text-sm text-gray-500">Documento PDF</p>
                        </div>
                        
                        <a href="{{ $fileUrl }}" 
                           target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Apri PDF
                        </a>
                    </div>
                </div>
                
                <div class="hidden md:block border-t">
                    <iframe src="{{ $fileUrl }}" 
                            class="w-full h-96" 
                            frameborder="0">
                        <p>Il tuo browser non supporta la visualizzazione PDF. 
                           <a href="{{ $fileUrl }}" target="_blank">Clicca qui per aprire il file</a>
                        </p>
                    </iframe>
                </div>
                
            @else
                {{-- Altri tipi di file --}}
                <div class="p-6 text-center">
                    <div class="flex flex-col items-center space-y-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $documento->nome }}</h3>
                            <p class="text-sm text-gray-500">File • {{ strtoupper($fileType) }}</p>
                        </div>
                        
                        <a href="{{ $fileUrl }}" 
                           download="{{ $documento->nome }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Scarica File
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="mt-4 flex flex-col sm:flex-row gap-2">
            <a href="{{ $fileUrl }}" 
               target="_blank"
               class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Apri
            </a>
            
            <a href="{{ $fileUrl }}" 
               download="{{ $documento->nome }}"
               class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Scarica
            </a>
        </div>
        
    @else
        <div class="p-8 text-center border border-dashed border-gray-300 rounded-lg">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">File non disponibile</h3>
            <p class="text-gray-500">Il file collegato a questo documento non è più disponibile.</p>
        </div>
    @endif
</div>

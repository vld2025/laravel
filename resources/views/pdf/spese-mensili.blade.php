@if(count($spese) > 0)
    @foreach($spese as $index => $spesa)
        @php
            $filePath = storage_path('app/public/' . $spesa->file);
            $fileExtension = strtolower(pathinfo($spesa->file, PATHINFO_EXTENSION));
        @endphp
        @if(file_exists($filePath))
            @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                <img src="data:image/{{ $fileExtension === 'jpg' ? 'jpeg' : $fileExtension }};base64,{{ base64_encode(file_get_contents($filePath)) }}" style="width:100%;height:auto;margin:0;padding:0;display:block;">
            @elseif($fileExtension === 'pdf')
                @php
                    try {
                        $imagick = new Imagick();
                        $imagick->setResolution(300, 300);
                        $imagick->readImage($filePath);
                        $imagick->resetIterator();
                        foreach ($imagick as $pageImage) {
                            $pageImage->setImageFormat('jpeg');
                            $pageImage->setImageCompressionQuality(95);
                            $imageData = base64_encode($pageImage->getImageBlob());
                            echo '<img src="data:image/jpeg;base64,' . $imageData . '" style="width:100%;height:auto;margin:0;padding:0;display:block;">';
                        }
                        $imagick->clear();
                        $imagick->destroy();
                    } catch (Exception $e) {
                        // Silenzioso
                    }
                @endphp
            @endif
        @endif
    @endforeach
@endif

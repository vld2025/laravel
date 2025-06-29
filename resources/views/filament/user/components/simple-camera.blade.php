<!-- Camera Standard -->
<div id="simple-camera" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: #000; z-index: 9999;">
    <video id="camera-video" style="width: 100%; height: 100%; object-fit: cover;" autoplay muted playsinline></video>
    
    <!-- Pulsanti minimalisti -->
    <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 20px; align-items: center;">
        <button id="close-camera" style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.2); color: white; border: none; font-size: 20px;">✕</button>
        <button id="capture-btn" style="width: 70px; height: 70px; border-radius: 50%; background: white; border: 4px solid #ccc; font-size: 30px; color: #333;">📷</button>
        <button id="switch-camera" style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.2); color: white; border: none; font-size: 20px;">🔄</button>
    </div>
</div>

<script>
let currentStream = null;
let facingMode = 'environment';

function openDocumentScanner() {
    document.getElementById('simple-camera').style.display = 'block';
    document.body.style.overflow = 'hidden';
    startCamera();
}

async function startCamera() {
    try {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
        
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { 
                facingMode: facingMode,
                width: { ideal: 1920 },
                height: { ideal: 1080 }
            }
        });
        
        currentStream = stream;
        document.getElementById('camera-video').srcObject = stream;
    } catch (err) {
        alert('Errore camera: ' + err.message);
        closeCamera();
    }
}

function closeCamera() {
    document.getElementById('simple-camera').style.display = 'none';
    document.body.style.overflow = '';
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
    }
}

function switchCamera() {
    facingMode = facingMode === 'environment' ? 'user' : 'environment';
    startCamera();
}

async function capturePhoto() {
    const video = document.getElementById('camera-video');
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);
    
    // Converti e invia per processing
    canvas.toBlob(async (blob) => {
        try {
            const formData = new FormData();
            formData.append('document_image', blob);
            
            const response = await fetch('/api/process-document-simple', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success && window.saveScannedDocument) {
                window.saveScannedDocument(result.enhanced_image);
            } else {
                // Fallback: usa foto originale
                const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                if (window.saveScannedDocument) {
                    window.saveScannedDocument(dataUrl);
                }
            }
        } catch (error) {
            // Fallback: usa foto originale
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            if (window.saveScannedDocument) {
                window.saveScannedDocument(dataUrl);
            }
        }
        
        closeCamera();
    }, 'image/jpeg', 0.9);
}

// Event listeners
document.getElementById('capture-btn').addEventListener('click', capturePhoto);
document.getElementById('close-camera').addEventListener('click', closeCamera);
document.getElementById('switch-camera').addEventListener('click', switchCamera);
</script>

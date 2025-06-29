<!-- Document Scanner Component - Fixed UI -->
<div id="document-scanner" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.95); z-index: 9999; font-family: system-ui, -apple-system, sans-serif;">
    <div style="position: relative; width: 100%; height: 100%; display: flex; flex-direction: column;">
        
        <!-- Header Scanner -->
        <div style="background: rgba(0,0,0,0.8); color: white; padding: 15px 20px; text-align: center; position: relative; z-index: 10001;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Scansiona Documento</h3>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.8;">Posiziona il documento nel riquadro verde</p>
        </div>
        
        <!-- Camera Container -->
        <div style="flex: 1; position: relative; display: flex; align-items: center; justify-content: center; background: #000; overflow: hidden;">
            <!-- Video Camera -->
            <video id="scanner-video" 
                   style="width: 100%; height: 100%; object-fit: cover;" 
                   autoplay 
                   muted 
                   playsinline>
            </video>
            
            <!-- Canvas nascosto -->
            <canvas id="scanner-canvas" style="display: none;"></canvas>
            
            <!-- Guide Overlay -->
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 85%; height: 65%; border: 3px solid #10b981; border-radius: 12px; background: rgba(16, 185, 129, 0.1); pointer-events: none;">
                <!-- Corner indicators -->
                <div style="position: absolute; top: -6px; left: -6px; width: 12px; height: 12px; background: #10b981; border-radius: 50%; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);"></div>
                <div style="position: absolute; top: -6px; right: -6px; width: 12px; height: 12px; background: #10b981; border-radius: 50%; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);"></div>
                <div style="position: absolute; bottom: -6px; left: -6px; width: 12px; height: 12px; background: #10b981; border-radius: 50%; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);"></div>
                <div style="position: absolute; bottom: -6px; right: -6px; width: 12px; height: 12px; background: #10b981; border-radius: 50%; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);"></div>
                
                <!-- Center text -->
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; text-align: center; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                    <div style="background: rgba(0,0,0,0.6); padding: 8px 16px; border-radius: 20px; font-size: 14px;">
                        📄 Posiziona qui il documento
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Controls Bar - FIXED POSITION -->
        <div style="background: rgba(0,0,0,0.9); padding: 20px; display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 10001; min-height: 80px;">
            
            <!-- Pulsante Annulla -->
            <button id="scanner-cancel" 
                    style="background: #ef4444; color: white; border: none; padding: 12px 20px; border-radius: 25px; font-size: 14px; font-weight: 600; cursor: pointer; min-width: 80px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); transition: all 0.2s;"
                    onmousedown="this.style.transform='scale(0.95)'"
                    onmouseup="this.style.transform='scale(1)'"
                    onmouseleave="this.style.transform='scale(1)'">
                ❌ Annulla
            </button>
            
            <!-- Pulsante Capture CENTRALE e GRANDE -->
            <button id="scanner-capture" 
                    style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 0; border-radius: 50%; font-size: 24px; cursor: pointer; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4); transition: all 0.2s; position: relative;"
                    onmousedown="this.style.transform='scale(0.9)'"
                    onmouseup="this.style.transform='scale(1)'"
                    onmouseleave="this.style.transform='scale(1)'">
                📷
                <div style="position: absolute; inset: -4px; border: 3px solid rgba(255,255,255,0.3); border-radius: 50%; animation: pulse 2s infinite;"></div>
            </button>
            
            <!-- Pulsante Switch Camera (se disponibile) -->
            <button id="scanner-switch" 
                    style="background: #6b7280; color: white; border: none; padding: 12px 20px; border-radius: 25px; font-size: 14px; font-weight: 600; cursor: pointer; min-width: 80px; box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3); transition: all 0.2s;"
                    onmousedown="this.style.transform='scale(0.95)'"
                    onmouseup="this.style.transform='scale(1)'"
                    onmouseleave="this.style.transform='scale(1)'">
                🔄 Ruota
            </button>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="crop-preview" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.95); z-index: 9999; font-family: system-ui, -apple-system, sans-serif;">
    <div style="position: relative; width: 100%; height: 100%; display: flex; flex-direction: column;">
        
        <div style="background: rgba(0,0,0,0.8); color: white; padding: 15px 20px; text-align: center;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Anteprima Documento</h3>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.8;">Controlla il risultato prima di salvare</p>
        </div>
        
        <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 20px; background: #111;">
            <img id="cropped-preview" 
                 style="max-width: 100%; max-height: 100%; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); object-fit: contain;">
        </div>
        
        <div style="background: rgba(0,0,0,0.9); padding: 20px; display: flex; justify-content: space-around; align-items: center;">
            <button id="preview-retry" 
                    style="background: #ef4444; color: white; border: none; padding: 15px 25px; border-radius: 25px; font-size: 16px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                🔄 Riprova
            </button>
            <button id="preview-save" 
                    style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 15px 25px; border-radius: 25px; font-size: 16px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);">
                ✅ Salva
            </button>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.05); }
    100% { opacity: 1; transform: scale(1); }
}
</style>

<script>
let currentStream = null;
let scannedImage = null;
let facingMode = 'environment'; // 'user' per frontale, 'environment' per posteriore

function openDocumentScanner() {
    document.getElementById('document-scanner').style.display = 'block';
    document.body.style.overflow = 'hidden'; // Previeni scroll
    startCamera();
}

async function startCamera() {
    try {
        // Stop previous stream
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
        
        const constraints = {
            video: { 
                facingMode: facingMode,
                width: { ideal: 1920, max: 1920 },
                height: { ideal: 1080, max: 1080 },
                aspectRatio: { ideal: 16/9 }
            }
        };
        
        const stream = await navigator.mediaDevices.getUserMedia(constraints);
        currentStream = stream;
        
        const video = document.getElementById('scanner-video');
        video.srcObject = stream;
        
        // Assicurati che il video si avvii
        video.onloadedmetadata = () => {
            video.play();
        };
        
    } catch (err) {
        console.error('Errore camera:', err);
        showErrorMessage('❌ Impossibile accedere alla camera');
        closeScanner();
    }
}

function stopCamera() {
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
    }
}

function closeScanner() {
    document.getElementById('document-scanner').style.display = 'none';
    document.getElementById('crop-preview').style.display = 'none';
    document.body.style.overflow = ''; // Ripristina scroll
    stopCamera();
}

function captureDocument() {
    const video = document.getElementById('scanner-video');
    const canvas = document.getElementById('scanner-canvas');
    const ctx = canvas.getContext('2d');
    
    if (video.videoWidth === 0) {
        showErrorMessage('❌ Camera non pronta');
        return;
    }
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);
    
    // Crop intelligente (centro 80% dell'immagine)
    const croppedDataUrl = cropDocument(canvas);
    showCropPreview(croppedDataUrl);
}

function cropDocument(canvas) {
    const ctx = canvas.getContext('2d');
    const cropPercent = 0.1; // 10% di margine
    
    const cropX = canvas.width * cropPercent;
    const cropY = canvas.height * cropPercent * 1.5; // Più margine sopra/sotto
    const cropWidth = canvas.width * (1 - cropPercent * 2);
    const cropHeight = canvas.height * (1 - cropPercent * 3);
    
    const croppedCanvas = document.createElement('canvas');
    const croppedCtx = croppedCanvas.getContext('2d');
    
    croppedCanvas.width = cropWidth;
    croppedCanvas.height = cropHeight;
    
    croppedCtx.drawImage(canvas, cropX, cropY, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight);
    
    // Migliora contrasto
    const imageData = croppedCtx.getImageData(0, 0, cropWidth, cropHeight);
    enhanceImage(imageData);
    croppedCtx.putImageData(imageData, 0, 0);
    
    return croppedCanvas.toDataURL('image/jpeg', 0.9);
}

function enhanceImage(imageData) {
    const data = imageData.data;
    const contrast = 1.2;
    const brightness = 10;
    
    for (let i = 0; i < data.length; i += 4) {
        // Applica contrasto e luminosità
        data[i] = Math.min(255, Math.max(0, contrast * (data[i] - 128) + 128 + brightness));     // R
        data[i + 1] = Math.min(255, Math.max(0, contrast * (data[i + 1] - 128) + 128 + brightness)); // G
        data[i + 2] = Math.min(255, Math.max(0, contrast * (data[i + 2] - 128) + 128 + brightness)); // B
    }
}

function showCropPreview(dataUrl) {
    scannedImage = dataUrl;
    document.getElementById('document-scanner').style.display = 'none';
    document.getElementById('crop-preview').style.display = 'block';
    document.getElementById('cropped-preview').src = dataUrl;
    stopCamera();
}

function switchCamera() {
    facingMode = facingMode === 'environment' ? 'user' : 'environment';
    startCamera();
}

function showErrorMessage(message) {
    const toast = document.createElement('div');
    toast.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #ef4444; color: white; padding: 20px 30px; border-radius: 12px; z-index: 10002; font-weight: 600; font-size: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.5);';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => document.body.removeChild(toast), 3000);
}

// Event listeners
document.getElementById('scanner-cancel').addEventListener('click', closeScanner);
document.getElementById('scanner-capture').addEventListener('click', captureDocument);
document.getElementById('scanner-switch').addEventListener('click', switchCamera);

document.getElementById('preview-retry').addEventListener('click', () => {
    document.getElementById('crop-preview').style.display = 'none';
    openDocumentScanner();
});

document.getElementById('preview-save').addEventListener('click', () => {
    if (scannedImage && window.saveScannedDocument) {
        window.saveScannedDocument(scannedImage);
    }
    closeScanner();
});

// Esponi funzione globale
window.openDocumentScanner = openDocumentScanner;
</script>

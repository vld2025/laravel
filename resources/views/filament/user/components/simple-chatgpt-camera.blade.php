<!-- Camera Semplice + ChatGPT -->
<div id="simple-camera" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: #000; z-index: 9999;">
    <video id="camera-video" style="width: 100%; height: 100%; object-fit: cover;" autoplay playsinline></video>
    
    <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);">
        <button id="capture-btn" style="width: 70px; height: 70px; border-radius: 50%; background: white; border: none; font-size: 30px;">📷</button>
    </div>
    
    <button id="close-camera" style="position: absolute; top: 20px; right: 20px; background: rgba(0,0,0,0.5); color: white; border: none; padding: 10px; border-radius: 50%;">❌</button>
</div>

<script>
function openDocumentScanner() {
    document.getElementById('simple-camera').style.display = 'block';
    startSimpleCamera();
}

async function startSimpleCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
    document.getElementById('camera-video').srcObject = stream;
}

document.getElementById('capture-btn').onclick = function() {
    const video = document.getElementById('camera-video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    // Invia a ChatGPT per processing
    canvas.toBlob(async (blob) => {
        const formData = new FormData();
        formData.append('document_image', blob);
        
        const response = await fetch('/api/process-document-simple', {
            method: 'POST', 
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        
        const result = await response.json();
        if (result.success) {
            // Usa il documento migliorato
            window.saveScannedDocument(result.enhanced_image);
        }
    });
    
    document.getElementById('simple-camera').style.display = 'none';
};

document.getElementById('close-camera').onclick = () => {
    document.getElementById('simple-camera').style.display = 'none';
};
</script>

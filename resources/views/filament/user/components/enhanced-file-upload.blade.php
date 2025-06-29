<!-- Enhanced File Upload Component -->
<div x-data="enhancedFileUpload()" x-init="init()">
    <!-- Il contenuto originale del FileUpload viene mantenuto -->
    <div class="file-upload-content">
        {{ $slot ?? '' }}
    </div>

    <!-- Modal per opzioni upload -->
    <div x-show="showModal" 
         x-transition.opacity
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="closeModal()">
        
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 transform transition-all"
             x-show="showModal"
             x-transition.scale.origin.center>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-6 text-center">
                Carica Documento
            </h3>
            
            <div class="space-y-3">
                <!-- Opzione Scansione -->
                <button @click="startScanning()" 
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-4 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Scansiona Documento</span>
                </button>
                
                <!-- Opzione Galleria -->
                <button @click="openGallery()" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-4 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Scegli da Galleria</span>
                </button>
                
                <!-- Opzione Annulla -->
                <button @click="closeModal()" 
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                    Annulla
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function enhancedFileUpload() {
    return {
        showModal: false,
        originalFileInput: null,
        
        init() {
            this.$nextTick(() => {
                this.initializeFileInput();
            });
        },
        
        initializeFileInput() {
            // Trova l'input file originale
            this.originalFileInput = this.$el.querySelector('input[type="file"]');
            
            if (!this.originalFileInput) {
                console.warn('File input non trovato');
                return;
            }
            
            // Trova l'area di upload e sostituisci il comportamento
            const uploadArea = this.findUploadArea();
            if (uploadArea) {
                this.overrideClickBehavior(uploadArea);
            }
        },
        
        findUploadArea() {
            const selectors = [
                '[data-testid="file-upload-dropzone"]',
                '.fi-fo-file-upload-dropzone',
                '.fi-fo-file-upload-ctn',
                '.fi-fo-file-upload'
            ];
            
            for (const selector of selectors) {
                const element = this.$el.querySelector(selector);
                if (element) return element;
            }
            
            return this.originalFileInput?.closest('.fi-fo-file-upload');
        },
        
        overrideClickBehavior(uploadArea) {
            // Rimuovi event listeners esistenti clonando l'elemento
            const newUploadArea = uploadArea.cloneNode(true);
            uploadArea.parentNode.replaceChild(newUploadArea, uploadArea);
            
            // Aggiungi il nuovo comportamento
            newUploadArea.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.showUploadOptions();
            });
            
            // Mantieni il drag & drop funzionante
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                newUploadArea.addEventListener(eventName, (e) => {
                    if (eventName === 'drop') {
                        // Lascia che Filament gestisca il drop normalmente
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
        },
        
        showUploadOptions() {
            this.showModal = true;
        },
        
        closeModal() {
            this.showModal = false;
        },
        
        startScanning() {
            this.closeModal();
            
            try {
                if (typeof openDocumentScanner === 'function') {
                    openDocumentScanner();
                } else {
                    throw new Error('Document scanner non disponibile');
                }
            } catch (error) {
                console.error('Errore nell\'avvio del scanner:', error);
                this.showNotification('Scanner non disponibile', 'error');
            }
        },
        
        openGallery() {
            this.closeModal();
            if (this.originalFileInput) {
                this.originalFileInput.click();
            }
        },
        
        saveScannedDocument(dataUrl, filename = 'scanned-document.jpg') {
            fetch(dataUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Errore nel processare l\'immagine');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const file = new File([blob], filename, { type: 'image/jpeg' });
                    return this.setFileToInput(file);
                })
                .then(() => {
                    this.showNotification('Documento scansionato con successo!', 'success');
                })
                .catch(error => {
                    console.error('Errore nel salvare il documento:', error);
                    this.showNotification('Errore nel salvare il documento', 'error');
                });
        },
        
        setFileToInput(file) {
            if (!this.originalFileInput) {
                throw new Error('File input non disponibile');
            }
            
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            this.originalFileInput.files = dataTransfer.files;
            
            // Trigger degli eventi per Filament
            const events = ['change', 'input'];
            events.forEach(eventType => {
                const event = new Event(eventType, { 
                    bubbles: true, 
                    cancelable: true 
                });
                this.originalFileInput.dispatchEvent(event);
            });
        },
        
        showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-emerald-500',
                error: 'bg-red-500',
                info: 'bg-blue-500'
            };
            
            const icons = {
                success: '✅',
                error: '❌',
                info: 'ℹ️'
            };
            
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-50 max-w-sm transform transition-all duration-300 translate-x-full`;
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <span class="text-lg">${icons[type]}</span>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animazione di entrata
            requestAnimationFrame(() => {
                notification.classList.remove('translate-x-full');
            });
            
            // Rimozione automatica
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    };
}

// Funzione globale per il callback del scanner
window.saveScannedDocument = function(dataUrl, filename) {
    // Trova l'istanza Alpine.js attiva
    const uploadComponents = document.querySelectorAll('[x-data*="enhancedFileUpload"]');
    
    for (const component of uploadComponents) {
        if (component._x_dataStack && component._x_dataStack[0].saveScannedDocument) {
            component._x_dataStack[0].saveScannedDocument(dataUrl, filename);
            break;
        }
    }
};
</script>

@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 16px; min-height: 500px;">
                
                <div class="card-header border-0 text-center py-4" style="background-color: #67768A;">
                    <h5 class="text-white fw-bold mb-0" style="letter-spacing: 1px;">
                        <i class="fas fa-ticket-alt me-2" style="color: #F4A637;"></i> NUEVO REPORTE
                    </h5>
                </div>

                <div class="card-body p-4 p-md-5 position-relative">
                    
                    <form id="ticketForm" method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="alert mb-4 d-flex align-items-center" style="background-color: #f0f4f8; border-left: 4px solid #61b0a5;">
                            <i class="fas fa-info-circle fa-2x me-3" style="color: #61b0a5;"></i>
                            <div>
                                <h6 class="fw-bold mb-0" style="color: #67768A;">Módulo Seleccionado: <span class="text-uppercase text-dark">{{ request('categoria') }}</span></h6>
                                <small class="text-muted">Si el problema es de otro sistema, <a href="{{ route('cliente.tickets.crear') }}" class="fw-bold text-decoration-none" style="color: #F4A637;">regresa al catálogo</a>.</small>
                            </div>
                        </div>
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">

                        {{-- Validación de Identidad (Solo Lectura) --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">TU NOMBRE</label>
                                <input type="text" class="form-control form-control-lg bg-light border-0 text-muted" value="{{ auth()->user()->name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">TU CORREO</label>
                                <input type="email" class="form-control form-control-lg bg-light border-0 text-muted" value="{{ auth()->user()->email }}" readonly>
                            </div>
                        </div>

                        {{-- Descripción WYSIWYG --}}
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold text-danger">DESCRIPCIÓN DIRECTA DEL ERROR *</label>
                            <textarea id="bodyTextarea" name="body" class="form-control form-control-lg bg-light border-0" rows="5" placeholder="Ej. Al intentar imprimir una factura en SAP, me sale el 'Error 504'." required></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            @if(empty(auth()->user()->anydesk))
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">DIRECCIÓN ANYDESK</label>
                                <input type="text" name="anydesk" class="form-control form-control-lg bg-light border-0" placeholder="Ej. 123 456 789" required>
                            </div>
                            @else
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">DIRECCIÓN ANYDESK</label>
                                <input type="text" class="form-control form-control-lg bg-light border-0 text-muted" value="{{ auth()->user()->anydesk }}" readonly>
                            </div>
                            @endif

                            {{-- Evidencia Adjunta Clásica --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">O ADJUNTAR EVIDENCIA (OPCIONAL)</label>
                                <input type="file" id="attachment" name="attachment" class="form-control form-control-lg bg-light border-0" accept="image/*">
                                <span id="paste-success" class="text-success small fw-bold d-none mt-1"><i class="fas fa-check-circle me-1"></i> ¡Imagen capturada en adjunto!</span>
                                
                                <div id="preview-container" class="d-none mt-3 text-center">
                                    <img id="image-preview" src="" class="img-fluid shadow-sm" style="max-height: 140px; border-radius: 8px; border: 2px solid #61b0a5;">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn w-100 py-3 mt-3 text-white fw-bold shadow-lg slot-btn" style="background-color: #F4A637; border-radius: 12px; font-size: 1.2rem; letter-spacing: 2px;">
                            <i class="fas fa-play me-2"></i> GENERAR TICKET
                        </button>
                    </form>

                    {{-- PANTALLA DE CASINO --}}
                    <div id="casino-overlay" class="d-none flex-column justify-content-center align-items-center text-center h-100 py-5">
                        <h4 class="fw-bold mb-4" style="color: #67768A; letter-spacing: 2px;">PROCESANDO REQUERIMIENTO...</h4>
                        <div id="big-slot-display" class="bg-white shadow-sm fw-bold d-flex align-items-center justify-content-center" style="font-size: 4.5rem; height: 140px; width: 100%; max-width: 400px; border-radius: 20px; letter-spacing: 15px; color: #0f172a; border: 6px solid #e2e8f0; transition: all 0.4s ease;">🎰🎲🎰</div>
                        <h5 id="slot-message" class="fw-bold mt-5" style="color: #F4A637; font-size: 1.5rem; animation: pulseText 0.8s infinite alternate;">¡Girando rodillos!</h5>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .slot-blur { animation: slotBlur 0.05s infinite alternate; color: #64748b !important; }
    .slot-success { border-color: #10b981 !important; color: #10b981 !important; background: #f0fdf4 !important; box-shadow: 0 0 30px rgba(16, 185, 129, 0.4) !important; transform: scale(1.05); }
    @keyframes slotBlur { 0% { filter: blur(0px); transform: translateY(0); } 100% { filter: blur(4px); transform: translateY(-5px); } }
    @keyframes pulseText { from { opacity: 0.6; transform: scale(0.98); } to { opacity: 1; transform: scale(1.02); } }
    .slot-btn { transition: all 0.2s ease; }
    .slot-btn:active { transform: translateY(4px); box-shadow: 0 0px 0px rgba(0,0,0,0) !important; }
    .form-control:focus { box-shadow: 0 0 0 3px rgba(244, 166, 55, 0.25); background-color: #ffffff !important; }
    
    /* Ajuste para los bordes del TinyMCE */
    .tox-tinymce { border-radius: 8px !important; border: none !important; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<!-- Inyección del Editor TinyMCE vía CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. INICIALIZACIÓN DEL EDITOR WYSIWYG
        tinymce.init({
            selector: '#bodyTextarea',
            plugins: 'image paste link lists',
            toolbar: 'bold italic underline | numlist bullist | link image | undo redo',
            menubar: false,
            paste_data_images: true, // ESTO PERMITE PEGAR IMÁGENES CON CTRL+V DENTRO DEL EDITOR
            height: 250,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave(); // Fuerza a guardar el contenido HTML en el textarea original
                });
            }
        });

        const form = document.getElementById('ticketForm');
        const casinoOverlay = document.getElementById('casino-overlay');
        const display = document.getElementById('big-slot-display');
        const slotMessage = document.getElementById('slot-message');
        const dulces = ["🍬", "🍭", "🍫", "🍩", "🧁", "🍪", "🍧", "🍨", "🍒", "🍓"];

        // 2. MANTENEMOS EL EVENTO 'PASTE' PROTEGIDO POR SI CLICAN AFUERA DEL EDITOR
        document.addEventListener('paste', function(e) {
            // Si el usuario está pegando dentro del iFrame de TinyMCE, ignoramos esto para no estorbarle al WYSIWYG
            if (e.target.closest('.tox-tinymce') || e.target.tagName === 'IFRAME') return;

            let fileInput = document.getElementById('attachment');
            let pasteSuccessMsg = document.getElementById('paste-success');
            let previewContainer = document.getElementById('preview-container');
            let imagePreview = document.getElementById('image-preview');
            
            if (!fileInput || !previewContainer || !imagePreview) return;

            let clipboardData = e.clipboardData || window.clipboardData;
            if (!clipboardData) return;

            let file = null;

            if (clipboardData.items) {
                for (let i = 0; i < clipboardData.items.length; i++) {
                    if (clipboardData.items[i].type.indexOf("image") !== -1) {
                        file = clipboardData.items[i].getAsFile();
                        break;
                    }
                }
            }

            if (file) {
                let finalFile = new File([file], "evidencia_" + Date.now() + ".png", { type: file.type || 'image/png' });
                let dataTransfer = new DataTransfer();
                dataTransfer.items.add(finalFile);
                fileInput.files = dataTransfer.files;
                
                let reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(finalFile);
                
                if (pasteSuccessMsg) {
                    pasteSuccessMsg.classList.remove('d-none');
                    setTimeout(() => pasteSuccessMsg.classList.add('d-none'), 5000);
                }
            }
        });

        // 3. ANIMACIÓN DEL CASINO
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); 
                form.classList.add('d-none');
                if (casinoOverlay) {
                    casinoOverlay.classList.remove('d-none');
                    casinoOverlay.classList.add('d-flex');
                }
                if (display) display.classList.add("slot-blur");

                let spinInterval = setInterval(() => {
                    let randomStr = "";
                    for (let i = 0; i < 3; i++) { randomStr += dulces[Math.floor(Math.random() * dulces.length)]; }
                    if (display) display.innerText = randomStr;
                }, 40);

                setTimeout(() => {
                    clearInterval(spinInterval);
                    if (display) {
                        display.classList.remove("slot-blur");
                        display.classList.add("slot-success");
                        const randomNum = String(Math.floor(Math.random() * 999)).padStart(3, '0');
                        display.innerText = `TK-${randomNum}`;
                    }
                    if (slotMessage) {
                        slotMessage.innerText = "¡BINGO! TICKET GENERADO";
                        slotMessage.style.color = "#10b981";
                        slotMessage.style.animation = "none"; 
                    }

                    setTimeout(() => { form.submit(); }, 1200);
                }, 2500);
            });
        }
    });
</script>
@endpush
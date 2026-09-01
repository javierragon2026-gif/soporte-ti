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

                        {{-- Descripción Restringida --}}
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold text-danger">DESCRIPCIÓN DIRECTA DEL ERROR *</label>
                            <textarea id="bodyTextarea" name="body" class="form-control form-control-lg bg-light border-0" rows="5" placeholder="Ej. Al intentar imprimir una factura en SAP, me sale el 'Error 504'. &#10;&#10;Por favor, omite historias largas. Ve directo al grano: qué intentabas hacer y qué falló." required></textarea>
                            <small class="text-muted mt-1 d-block"><i class="fas fa-keyboard me-1"></i> <b>Tip:</b> Si tomaste una captura de pantalla, simplemente presiona <b>Ctrl+V</b> aquí mismo para adjuntarla de forma automática.</small>
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

                            {{-- Evidencia Adjunta con Previsualización --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">ADJUNTAR EVIDENCIA</label>
                                <input type="file" id="attachment" name="attachment" class="form-control form-control-lg bg-light border-0" accept="image/*">
                                <span id="paste-success" class="text-success small fw-bold d-none mt-1"><i class="fas fa-check-circle me-1"></i> ¡Imagen capturada correctamente!</span>
                                
                                {{-- Contenedor dinámico para la imagen pegada --}}
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

@section('scripts')
<style>
    .slot-blur { animation: slotBlur 0.05s infinite alternate; color: #64748b !important; }
    .slot-success { border-color: #10b981 !important; color: #10b981 !important; background: #f0fdf4 !important; box-shadow: 0 0 30px rgba(16, 185, 129, 0.4) !important; transform: scale(1.05); }
    @keyframes slotBlur { 0% { filter: blur(0px); transform: translateY(0); } 100% { filter: blur(4px); transform: translateY(-5px); } }
    @keyframes pulseText { from { opacity: 0.6; transform: scale(0.98); } to { opacity: 1; transform: scale(1.02); } }
    .slot-btn { transition: all 0.2s ease; }
    .slot-btn:active { transform: translateY(4px); box-shadow: 0 0px 0px rgba(0,0,0,0) !important; }
    .form-control:focus { box-shadow: 0 0 0 3px rgba(244, 166, 55, 0.25); background-color: #ffffff !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('ticketForm');
        const casinoOverlay = document.getElementById('casino-overlay');
        const display = document.getElementById('big-slot-display');
        const slotMessage = document.getElementById('slot-message');
        const dulces = ["🍬", "🍭", "🍫", "🍩", "🧁", "🍪", "🍧", "🍨", "🍒", "🍓"];

        // Lógica ROBUSTA para la previsualización del portapapeles
        window.addEventListener('paste', function(e) {
            let fileInput = document.getElementById('attachment');
            let pasteSuccessMsg = document.getElementById('paste-success');
            let previewContainer = document.getElementById('preview-container');
            let imagePreview = document.getElementById('image-preview');
            
            // 1. Obtenemos los items del portapapeles (no los files)
            let items = (e.clipboardData || e.originalEvent.clipboardData).items;
            let blob = null;

            // 2. Buscamos si hay alguna imagen entre lo que se pegó
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf("image") === 0) {
                    blob = items[i].getAsFile();
                    break;
                }
            }

            // 3. Si encontramos una imagen cruda, la convertimos en un archivo real
            if (blob !== null) {
                // Le asignamos un nombre forzoso para que el input file lo acepte
                let file = new File([blob], "captura_evidencia.png", { type: blob.type });

                // Cargamos el archivo en el input oculto
                let dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                
                // Renderizamos la miniatura en pantalla
                let reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
                
                // Mostramos notificación verde
                pasteSuccessMsg.classList.remove('d-none');
                setTimeout(() => pasteSuccessMsg.classList.add('d-none'), 5000);
            }
        });

        // Soporte de vista previa si usan el botón "Examinar" en lugar de Ctrl+V
        document.getElementById('attachment').addEventListener('change', function() {
            let previewContainer = document.getElementById('preview-container');
            let imagePreview = document.getElementById('image-preview');
            
            if (this.files && this.files[0] && this.files[0].type.startsWith('image/')) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                previewContainer.classList.add('d-none');
            }
        });

        // Animación del Casino
        form.addEventListener('submit', function(e) {
            e.preventDefault(); 
            form.classList.add('d-none');
            casinoOverlay.classList.remove('d-none');
            casinoOverlay.classList.add('d-flex');
            display.classList.add("slot-blur");

            let spinInterval = setInterval(() => {
                let randomStr = "";
                for (let i = 0; i < 3; i++) { randomStr += dulces[Math.floor(Math.random() * dulces.length)]; }
                display.innerText = randomStr;
            }, 40);

            setTimeout(() => {
                clearInterval(spinInterval);
                display.classList.remove("slot-blur");
                display.classList.add("slot-success");
                const randomNum = String(Math.floor(Math.random() * 999)).padStart(3, '0');
                display.innerText = `TK-${randomNum}`;
                slotMessage.innerText = "¡BINGO! TICKET GENERADO";
                slotMessage.style.color = "#10b981";
                slotMessage.style.animation = "none"; 

                setTimeout(() => { form.submit(); }, 1200);
            }, 2500);
        });
    });
</script>
@endsection
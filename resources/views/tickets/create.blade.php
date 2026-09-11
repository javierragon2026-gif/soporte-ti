@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 16px; min-height: 500px;">

                {{-- Encabezado Fijo --}}
                <div class="card-header border-0 d-flex justify-content-between align-items-center py-4 px-5" style="background-color: #67768A;">
                    <h5 class="text-white fw-bold mb-0" style="letter-spacing: 1px;">
                        <i class="fas fa-tools me-2" style="color: #F4A637;"></i> CREACIÓN DE TICKET TI
                    </h5>
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-light fw-bold">
                        <i class="fas fa-th-large me-2"></i> BANDEJA DE TICKETS
                    </a>
                </div>

                <div class="card-body p-4 p-md-5 position-relative">

                    {{-- 1. FORMULARIO PRINCIPAL DE SISTEMAS --}}
                    <form id="ticketForm" method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                        @csrf

                        <h6 class="fw-bold mb-3" style="color: #67768A;"><i class="fas fa-user-tag me-2" style="color: #61b0a5;"></i>Datos del Solicitante</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">NOMBRE COMPLETO *</label>
                                <input type="text" name="requester[name]" class="form-control form-control-lg bg-light border-0" placeholder="Ej. Juan Pérez" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">CORREO ELECTRÓNICO *</label>
                                <input type="email" name="requester[email]" class="form-control form-control-lg bg-light border-0" placeholder="juan@tuempresa.com" required>
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 mb-4">

                        <h6 class="fw-bold mb-3" style="color: #67768A;"><i class="fas fa-file-signature me-2" style="color: #F4A637;"></i>Detalles del Requerimiento</h6>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">ASUNTO *</label>
                            <input type="text" name="title" class="form-control form-control-lg bg-light border-0" placeholder="Breve descripción del problema" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-danger">DESCRIPCIÓN DETALLADA *</label>
                            <textarea id="bodyTextarea" name="body" class="form-control form-control-lg bg-light border-0" rows="5" placeholder="Explica a detalle el requerimiento..."></textarea>
                            <small class="text-muted mt-1 d-block"><i class="fas fa-keyboard me-1"></i> <b>Tip:</b> Si tienes una captura de pantalla, simplemente presiona <b>Ctrl+V</b> para adjuntarla al instante.</small>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">ETIQUETAS</label>
                                <input type="text" name="tags" id="tags" class="form-control form-control-lg bg-light border-0" placeholder="ej. red, impresora">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">ESTADO INICIAL</label>
                                <select name="status" class="form-select form-select-lg bg-light border-0">
                                    <option value="1">Nuevo / Abierto</option>
                                    <option value="2">Pendiente</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">ASIGNAR A EQUIPO</label>
                                <select name="team_id" class="form-select form-select-lg bg-light border-0">
                                    <option value="">Sin asignar (Bandeja General)</option>
                                    {{-- Aquí iterarás los equipos si lo requieres --}}
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">ADJUNTAR EVIDENCIA O DOCUMENTOS</label>
                            <div id="drop-zone" class="border border-2 border-dashed rounded p-3 text-center" style="background-color: #f8f9fa; border-color: #cdd5df; cursor: pointer;">
                                <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color: #61b0a5;"></i>
                                <p class="mb-1 text-muted fw-bold small">Arrastra archivos o haz clic (También Ctrl+V)</p>
                                <input type="file" id="attachments" name="attachments[]" class="d-none" multiple>
                            </div>
                            <div id="file-list" class="mt-2 text-start d-flex flex-wrap gap-1"></div>
                            <span id="paste-success" class="text-success small fw-bold d-none mt-1"><i class="fas fa-check-circle me-1"></i> ¡Imagen pegada correctamente!</span>
                        </div>

                        <button type="submit" class="btn w-100 py-3 mt-2 text-white fw-bold shadow-lg slot-btn" style="background-color: #F4A637; border-radius: 12px; font-size: 1.2rem; letter-spacing: 2px;">
                            <i class="fas fa-save me-2"></i> REGISTRAR TICKET EN SISTEMA
                        </button>
                    </form>

                    {{-- 2. PANTALLA DE CASINO (Oculta por defecto) --}}
                    <div id="casino-overlay" class="d-none flex-column justify-content-center align-items-center text-center h-100 py-5">
                        <h4 class="fw-bold mb-4" style="color: #67768A; letter-spacing: 2px;">REGISTRANDO EN BASE DE DATOS...</h4>
                        
                        <div id="big-slot-display" class="bg-white shadow-sm fw-bold d-flex align-items-center justify-content-center" style="font-size: 4.5rem; height: 140px; width: 100%; max-width: 400px; border-radius: 20px; letter-spacing: 15px; color: #0f172a; border: 6px solid #e2e8f0; transition: all 0.4s ease;">
                            🎰🎲🎰
                        </div>
                        
                        <h5 id="slot-message" class="fw-bold mt-5" style="color: #F4A637; font-size: 1.5rem; animation: pulseText 0.8s infinite alternate;">
                            ¡Girando rodillos!
                        </h5>
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
    .form-control:focus, .form-select:focus { box-shadow: 0 0 0 3px rgba(244, 166, 55, 0.25); background-color: #ffffff !important; }
    .tox-tinymce { border-radius: 8px !important; border: none !important; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .border-dashed { border-style: dashed !important; }
    .file-badge { background-color: #e2e8f0; border-radius: 4px; padding: 4px 8px; font-size: 0.75rem; color: #475569; font-weight: bold; }
</style>

<!-- CDN de TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Inicializar TinyMCE
        tinymce.init({
            selector: '#bodyTextarea',
            menubar: false,
            plugins: 'lists link image',
            toolbar: 'bold italic underline | numlist bullist | link image',
            language: 'es',
            height: 250,
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save(); // Sincroniza en tiempo real
                });
            }
        });

        // 2. Drag & Drop y Paste Logic
        const form = document.getElementById('ticketForm');
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('attachments');
        const fileList = document.getElementById('file-list');
        const pasteSuccessMsg = document.getElementById('paste-success');
        let dt = new DataTransfer(); 

        const updateFileList = () => {
            fileList.innerHTML = '';
            for (let i = 0; i < dt.files.length; i++) {
                let span = document.createElement('span');
                span.className = 'file-badge';
                span.innerText = dt.files[i].name;
                fileList.appendChild(span);
            }
            fileInput.files = dt.files;
        };

        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.borderColor = "#F4A637"; });
        dropZone.addEventListener('dragleave', () => dropZone.style.borderColor = "#cdd5df");
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = "#cdd5df";
            for (let file of e.dataTransfer.files) { dt.items.add(file); }
            updateFileList();
        });
        
        fileInput.addEventListener('change', (e) => {
            for (let file of e.target.files) { dt.items.add(file); }
            updateFileList();
        });

        document.addEventListener('paste', function(e) {
            if (e.target.closest('.tox-tinymce') || e.target.tagName === 'IFRAME') return;
            if (e.clipboardData && e.clipboardData.files.length > 0) {
                for (let file of e.clipboardData.files) { 
                    if (file.type.startsWith('image/')) {
                        let finalFile = new File([file], "evidencia_" + Date.now() + ".png", { type: file.type });
                        dt.items.add(finalFile); 
                    }
                }
                updateFileList();
                pasteSuccessMsg.classList.remove('d-none');
                setTimeout(() => pasteSuccessMsg.classList.add('d-none'), 4000);
            }
        });

        // 3. Animación Tragamonedas y Envío AJAX
        const casinoOverlay = document.getElementById('casino-overlay');
        const display = document.getElementById('big-slot-display');
        const slotMessage = document.getElementById('slot-message');
        const dulces = ["🍬", "🍭", "🍫", "🍩", "🧁", "🍪", "🍧", "🍨", "🍒", "🍓"];

        form.addEventListener('submit', async function(e) {
            e.preventDefault(); 
            tinymce.triggerSave();

            form.classList.add('d-none');
            casinoOverlay.classList.remove('d-none');
            casinoOverlay.classList.add('d-flex');
            display.classList.add("slot-blur");

            let spinInterval = setInterval(() => {
                let randomStr = "";
                for (let i = 0; i < 3; i++) { randomStr += dulces[Math.floor(Math.random() * dulces.length)]; }
                display.innerText = randomStr;
            }, 40);

            try {
                let formData = new FormData(form);
                for (let i = 0; i < dt.files.length; i++) {
                    formData.append('attachments[]', dt.files[i]);
                }

                let response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                let data = await response.json();

                clearInterval(spinInterval);
                display.classList.remove("slot-blur");
                display.classList.add("slot-success");
                
                // Mostrar el código real devuelto por la Base de Datos
                display.innerText = data.ticket_id || data.ticket_code || "OK";
                slotMessage.innerText = "¡TICKET REGISTRADO EN SISTEMA!";
                slotMessage.style.color = "#10b981";
                slotMessage.style.animation = "none";

                // Redirigir a la bandeja principal de TI
                setTimeout(() => { window.location.href = '{{ route("tickets.index") }}'; }, 2000);

            } catch (error) {
                clearInterval(spinInterval);
                display.classList.remove("slot-blur");
                display.innerText = "ERROR";
                slotMessage.innerText = "Problema al registrar en base de datos.";
                slotMessage.style.color = "#e74c3c";
            }
        });
    });
</script>
@endpush
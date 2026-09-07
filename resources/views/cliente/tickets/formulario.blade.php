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

                        <form id="ticketForm" method="POST" action="{{ route('tickets.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="alert mb-4 d-flex align-items-center"
                                style="background-color: #f0f4f8; border-left: 4px solid #61b0a5;">
                                <i class="fas fa-info-circle fa-2x me-3" style="color: #61b0a5;"></i>
                                <div>
                                    <h6 class="fw-bold mb-0" style="color: #67768A;">Módulo Seleccionado: <span
                                            class="text-uppercase text-dark">{{ request('categoria') }}</span></h6>
                                    <small class="text-muted">Si el problema es de otro sistema, <a
                                            href="{{ route('cliente.tickets.crear') }}" class="fw-bold text-decoration-none"
                                            style="color: #F4A637;">regresa al catálogo</a>.</small>
                                </div>
                            </div>
                            <input type="hidden" name="categoria" value="{{ request('categoria') }}">

                            {{-- Validación de Identidad (Solo Lectura) --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">TU NOMBRE</label>
                                    <input type="text" class="form-control form-control-lg bg-light border-0 text-muted"
                                        value="{{ auth()->user()->name }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">TU CORREO</label>
                                    <input type="email" class="form-control form-control-lg bg-light border-0 text-muted"
                                        value="{{ auth()->user()->email }}" readonly>
                                </div>
                            </div>

                            {{-- Descripción WYSIWYG --}}
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold text-danger">DESCRIPCIÓN DIRECTA DEL ERROR
                                    *</label>
                                <textarea id="bodyTextarea" name="body" class="form-control form-control-lg bg-light border-0" rows="5"
                                    placeholder="Ej. Al intentar imprimir una factura en SAP, me sale el 'Error 504'." required></textarea>
                            </div>

                            <div class="row g-3 mb-4">
                                @if (empty(auth()->user()->anydesk))
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">DIRECCIÓN ANYDESK</label>
                                        <input type="text" name="anydesk"
                                            class="form-control form-control-lg bg-light border-0"
                                            placeholder="Ej. 123 456 789" required>
                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">DIRECCIÓN ANYDESK</label>
                                        <input type="text" name="anydesk"
                                            class="form-control form-control-lg bg-light border-0 text-muted"
                                            value="{{ auth()->user()->anydesk }}" readonly>
                                    </div>
                                @endif

                                {{-- Evidencia Adjunta: DRAG & DROP MÚLTIPLE --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">ADJUNTAR EVIDENCIA (OPCIONAL)</label>
                                    <div id="drop-zone" class="border border-2 border-dashed rounded p-3 text-center"
                                        style="background-color: #f8f9fa; border-color: #cdd5df; cursor: pointer;">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color: #61b0a5;"></i>
                                        <p class="mb-1 text-muted fw-bold small">Arrastra archivos, haz clic, o Ctrl+V</p>
                                        <input type="file" id="attachments" name="attachments[]" class="d-none" multiple>
                                    </div>
                                    <div id="file-list" class="mt-2 text-start d-flex flex-wrap gap-1"></div>
                                </div>
                            </div>

                            <button type="submit" class="btn w-100 py-3 mt-3 text-white fw-bold shadow-lg slot-btn"
                                style="background-color: #F4A637; border-radius: 12px; font-size: 1.2rem; letter-spacing: 2px;">
                                <i class="fas fa-play me-2"></i> GENERAR TICKET
                            </button>
                        </form>

                        {{-- PANTALLA DE CASINO --}}
                        <div id="casino-overlay"
                            class="d-none flex-column justify-content-center align-items-center text-center h-100 py-5">
                            <h4 class="fw-bold mb-4" style="color: #67768A; letter-spacing: 2px;">PROCESANDO
                                REQUERIMIENTO...</h4>
                            <div id="big-slot-display"
                                class="bg-white shadow-sm fw-bold d-flex align-items-center justify-content-center"
                                style="font-size: 4.5rem; height: 140px; width: 100%; max-width: 400px; border-radius: 20px; letter-spacing: 15px; color: #0f172a; border: 6px solid #e2e8f0; transition: all 0.4s ease;">
                                🎰🎲🎰</div>
                            <h5 id="slot-message" class="fw-bold mt-5"
                                style="color: #F4A637; font-size: 1.5rem; animation: pulseText 0.8s infinite alternate;">
                                ¡Girando rodillos!</h5>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        .slot-blur {
            animation: slotBlur 0.05s infinite alternate;
            color: #64748b !important;
        }

        .slot-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
            background: #f0fdf4 !important;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.4) !important;
            transform: scale(1.05);
        }

        @keyframes slotBlur {
            0% {
                filter: blur(0px);
                transform: translateY(0);
            }

            100% {
                filter: blur(4px);
                transform: translateY(-5px);
            }
        }

        @keyframes pulseText {
            from {
                opacity: 0.6;
                transform: scale(0.98);
            }

            to {
                opacity: 1;
                transform: scale(1.02);
            }
        }

        .slot-btn {
            transition: all 0.2s ease;
        }

        .slot-btn:active {
            transform: translateY(4px);
            box-shadow: 0 0px 0px rgba(0, 0, 0, 0) !important;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(244, 166, 55, 0.25);
            background-color: #ffffff !important;
        }

        .tox-tinymce {
            border-radius: 8px !important;
            border: none !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .border-dashed {
            border-style: dashed !important;
        }

        .file-badge {
            background-color: #e2e8f0;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 0.75rem;
            color: #475569;
            font-weight: bold;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. INICIALIZACIÓN DE TINYMCE (Tú código intacto)
            tinymce.init({
                selector: '#bodyTextarea',
                plugins: 'image paste link lists',
                toolbar: 'bold italic underline | numlist bullist | link image | undo redo',
                menubar: false,
                paste_data_images: true,
                height: 250,
                setup: function(editor) {
                    editor.on('change', function() {
                        tinymce.triggerSave();
                    });
                }
            });

            // 2. LÓGICA DRAG & DROP Y PASTE
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('attachments');
            const fileList = document.getElementById('file-list');
            let dt = new DataTransfer();

            const updateFileList = () => {
                fileList.innerHTML = '';
                for (let i = 0; i < dt.files.length; i++) {
                    let span = document.createElement('span');
                    span.className = 'file-badge';
                    span.innerHTML = `<i class="fas fa-file-alt me-1"></i> ${dt.files[i].name}`;
                    fileList.appendChild(span);
                }
                fileInput.files = dt.files;
            };

            dropZone.addEventListener('click', () => fileInput.click());
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = "#F4A637";
            });
            dropZone.addEventListener('dragleave', () => dropZone.style.borderColor = "#cdd5df");
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = "#cdd5df";
                for (let file of e.dataTransfer.files) {
                    dt.items.add(file);
                }
                updateFileList();
            });

            fileInput.addEventListener('change', (e) => {
                for (let file of e.target.files) {
                    dt.items.add(file);
                }
                updateFileList();
            });

            // Capturar Ctrl+V globalmente (evitando TinyMCE)
            document.addEventListener('paste', function(e) {
                if (e.target.closest('.tox-tinymce') || e.target.tagName === 'IFRAME') return;
                let clipboardData = e.clipboardData || window.clipboardData;
                if (!clipboardData) return;

                for (let i = 0; i < clipboardData.items.length; i++) {
                    if (clipboardData.items[i].type.indexOf("image") !== -1) {
                        let file = clipboardData.items[i].getAsFile();
                        let finalFile = new File([file], "evidencia_pegada_" + Date.now() + ".png", {
                            type: file.type
                        });
                        dt.items.add(finalFile);
                        updateFileList();
                        break;
                    }
                }
            });

            // 3. ANIMACIÓN Y ENVÍO POR AJAX (Para recibir el ID de la BD)
            const form = document.getElementById('ticketForm');
            const casinoOverlay = document.getElementById('casino-overlay');
            const display = document.getElementById('big-slot-display');
            const slotMessage = document.getElementById('slot-message');
            const dulces = ["🍬", "🍭", "🍫", "🍩", "🧁", "🍪", "🍧", "🍨", "🍒", "🍓"];

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                tinymce.triggerSave(); // Forzamos guardar el HTML de TinyMCE al textarea

                form.classList.add('d-none');
                casinoOverlay.classList.remove('d-none');
                casinoOverlay.classList.add('d-flex');
                display.classList.add("slot-blur");

                let spinInterval = setInterval(() => {
                    let randomStr = "";
                    for (let i = 0; i < 3; i++) {
                        randomStr += dulces[Math.floor(Math.random() * dulces.length)];
                    }
                    display.innerText = randomStr;
                }, 40);

                try {
                    let formData = new FormData(form);
                    // Adjuntamos los archivos del Drag & Drop
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

                    // Muestra el ID real que devolvió Laravel
                    display.innerText = data.ticket_id;
                    slotMessage.innerText = "¡TICKET ASIGNADO CORRECTAMENTE!";
                    slotMessage.style.color = "#10b981";

                    // Redirigir al usuario final a su historial después de 2 segundos
                    setTimeout(() => {
                        window.location.href = '{{ route('cliente.tickets.index') }}';
                    }, 2000);
                } catch (error) {
                    clearInterval(spinInterval);
                    display.classList.remove("slot-blur");
                    display.innerText = "ERROR";
                    slotMessage.innerText = "Error de conexión.";
                }
            });
        });
    </script>
@endpush

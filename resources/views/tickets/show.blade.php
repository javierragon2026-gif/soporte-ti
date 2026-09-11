@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4 px-4">
        <div class="row g-4">

            <!-- ==========================================
                     PANEL IZQUIERDO: HILO DEL TICKET Y CHAT
                =========================================== -->
            <div class="col-lg-8">
                <!-- Encabezado del Ticket -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm fw-bold shadow-sm"
                        style="border-radius: 8px;">
                        <i class="fas fa-arrow-left me-1"></i> Volver a Bandeja
                    </a>
                    <span class="badge px-3 py-2 shadow-sm" style="background-color: #67768A; font-size: 0.9rem;">
                        TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white p-4 border-bottom-0">
                        <h4 class="fw-bold mb-1" style="color: #2c3e50;">{{ $ticket->title }}</h4>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-calendar-alt me-1"></i> Creado el
                            {{ $ticket->created_at->format('d M, Y h:i A') }}
                        </p>
                    </div>

                    <div class="card-body p-4 bg-light">
                        <!-- Solicitud Original -->
                        <div class="p-4 bg-white shadow-sm mb-4 position-relative"
                            style="border-radius: 12px; border-left: 5px solid #F4A637;">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm me-2"
                                        style="width: 40px; height: 40px; background-color: #F4A637;">
                                        {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong style="color: #2c3e50; display: block;">{{ $ticket->user->name }}</strong>
                                        <span class="badge bg-light text-secondary border mt-1">Solicitante Original</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-dark" style="font-size: 0.95rem;">
                                {!! $ticket->body !!}
                            </div>

                            <!-- Adjuntos Originales -->
                            @if ($ticket->attachments && $ticket->attachments->count() > 0)
                                <div class="mt-4 pt-3 border-top">
                                    <strong class="small text-muted d-block mb-2"><i class="fas fa-paperclip me-1"></i>
                                        Evidencia Adjunta:</strong>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($ticket->attachments as $attachment)
                                            <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank"
                                                class="btn btn-sm bg-light border shadow-sm text-secondary fw-bold">
                                                <i class="fas fa-image me-1 text-primary"></i>
                                                {{ $attachment->name ?? 'Ver Archivo' }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Hilo de Seguimiento y Notas -->
                        <h6 class="fw-bold text-muted mb-3 ms-2 text-uppercase"
                            style="font-size: 0.8rem; letter-spacing: 1px;">
                            <i class="fas fa-history me-1"></i> Historial de Interacciones
                        </h6>

                        @foreach ($ticket->comments as $comment)
                            @php
                                // Lógica inteligente para saber quién es quién
                                $isAdmin = $comment->user->admin ?? false;
                                $isAuthor = $comment->user_id === $ticket->user_id;

                                // Colores según el rol
                                $borderColor = $comment->private ? '#e74c3c' : ($isAdmin ? '#61b0a5' : '#F4A637');
                                $bgColor = $comment->private ? '#fff5f5' : '#ffffff';
                            @endphp

                            <div class="p-4 shadow-sm mb-4 position-relative"
                                style="border-radius: 12px; background-color: {{ $bgColor }}; border-left: 5px solid {{ $borderColor }};">
                                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                    <div>
                                        <strong style="color: {{ $borderColor }}; font-size: 1.1rem;">
                                            {{ $comment->user->name }}
                                        </strong>

                                        <!-- Etiquetas de Identidad -->
                                        @if ($isAdmin)
                                            <span class="badge ms-2" style="background-color: #61b0a5;">Sistemas TI</span>
                                        @endif
                                        @if ($isAuthor)
                                            <span class="badge bg-secondary ms-1">Autor</span>
                                        @endif
                                        @if ($comment->private)
                                            <span class="badge bg-danger ms-1"><i class="fas fa-eye-slash me-1"></i>Nota Interna</span>
                                        @endif
                                    </div>
                                    <span class="text-muted small"><i class="far fa-clock me-1"></i>
                                        {{ $comment->created_at->format('d/m/Y h:i A') }}</span>
                                </div>

                                <div class="text-dark">
                                    {!! $comment->body !!}
                                </div>

                                <!-- Adjuntos del Comentario -->
                                @if ($comment->attachments && $comment->attachments->count() > 0)
                                    <div class="mt-3 pt-3 border-top">
                                        <strong class="small text-muted d-block mb-2">Adjuntos:</strong>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($comment->attachments as $attachment)
                                                <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank"
                                                    class="btn btn-sm bg-light border shadow-sm text-secondary">
                                                    <i class="fas fa-paperclip me-1"></i>
                                                    {{ $attachment->name ?? 'Ver Archivo' }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Caja de Respuesta (Solo si no está cerrado) -->
                @if ($ticket->status != 5)
                    <div class="card border-0 shadow-sm mb-4"
                        style="border-radius: 12px; border-top: 4px solid #61b0a5 !important;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3" style="color: #67768A; text-transform: uppercase; font-size: 0.9rem;">
                                <i class="fas fa-reply me-2" style="color: #61b0a5;"></i> Añadir Actualización al Hilo
                            </h6>
                            <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}"
                                enctype="multipart/form-data" id="comment-form">
                                @csrf

                                <div class="mb-3">
                                    <textarea id="adminBodyTextarea" name="body" class="form-control bg-light border-0" rows="5"
                                        placeholder="Escribe aquí la solución, avances o requerimientos..."></textarea>
                                </div>

                                <!-- Zona de Adjuntos -->
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">ADJUNTAR ARCHIVO O PEGAR IMAGEN (CTRL+V)</label>
                                    <input type="file" id="adminAttachment" name="attachments[]"
                                        class="form-control form-control-lg bg-light border-0" multiple>
                                    <span id="admin-paste-success" class="text-success small fw-bold d-none mt-1"><i
                                            class="fas fa-check-circle me-1"></i> ¡Imagen pegada correctamente!</span>
                                </div>

                                <!-- Estado implícito para el controlador de comentarios -->
                                <input type="hidden" name="new_status" value="{{ $ticket->status }}">

                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-3 pt-3 border-top gap-3">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="private" id="privateNote"
                                            value="1" role="switch">
                                        <label class="form-check-label text-muted fw-bold small" for="privateNote"
                                            style="color: #e74c3c !important;">
                                            Hacer Nota Privada (Oculta para el usuario)
                                        </label>
                                    </div>

                                    <button type="submit" class="btn text-white fw-bold shadow-sm px-4 py-2"
                                        style="background-color: #61b0a5; border-radius: 8px;">
                                        <i class="fas fa-paper-plane me-2"></i> Enviar Comentario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary text-center fw-bold shadow-sm" style="border-radius: 12px;">
                        <i class="fas fa-lock me-2"></i> Este ticket ha sido cerrado. No se admiten más respuestas.
                    </div>
                @endif
            </div>

            <!-- ==========================================
                     PANEL DERECHO: CENTRO DE CONTROL DE TI
                =========================================== -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="border-radius: 12px; top: 20px;">
                    <div class="card-header bg-white p-4 border-bottom-0">
                        <h5 class="fw-bold mb-0" style="color: #67768A;">
                            <i class="fas fa-sliders-h me-2" style="color: #F4A637;"></i> Panel de Control
                        </h5>
                    </div>

                    <div class="card-body p-4 bg-light">
                        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                            @csrf
                            @method('PUT')

                            <!-- 1. Estado del Ticket -->
                            <div class="mb-4">
                                <label class="fw-bold text-muted small mb-2"><i class="fas fa-thermometer-half me-1"></i> ESTATUS ACTUAL</label>
                                <select name="status" class="form-select border-0 shadow-sm fw-bold text-secondary"
                                    style="height: 45px; border-radius: 8px;">
                                    <option value="1" {{ $ticket->status == 1 ? 'selected' : '' }}>🔵 Nuevo / Abierto</option>
                                    <option value="2" {{ $ticket->status == 2 ? 'selected' : '' }}>🟠 En Proceso</option>
                                    <option value="3" {{ $ticket->status == 3 ? 'selected' : '' }}>🟡 Pendiente (Falta info)</option>
                                    <option value="4" {{ $ticket->status == 4 ? 'selected' : '' }}>🟢 Resuelto</option>
                                    <option value="5" {{ $ticket->status == 5 ? 'selected' : '' }}>⚫ Cerrado</option>
                                </select>
                            </div>

                            <!-- 2. Categoría Real -->
                            <div class="mb-4">
                                <label class="fw-bold text-muted small mb-2"><i class="fas fa-tags me-1"></i> CATEGORÍA / MÓDULO</label>
                                <select name="categoria" class="form-select border-0 shadow-sm fw-bold text-secondary"
                                    style="height: 45px; border-radius: 8px;">
                                    @php $currentCat = $ticket->categoria ?? 'SOPORTE'; @endphp
                                    <option value="SAP" {{ $currentCat == 'SAP' ? 'selected' : '' }}>SAP</option>
                                    <option value="WMS" {{ $currentCat == 'WMS' ? 'selected' : '' }}>WMS</option>
                                    <option value="SOPORTE" {{ $currentCat == 'SOPORTE' ? 'selected' : '' }}>Soporte Técnico</option>
                                    <option value="EQUIPOS" {{ $currentCat == 'EQUIPOS' ? 'selected' : '' }}>Infraestructura / Equipos</option>
                                    <option value="RETAIL" {{ $currentCat == 'RETAIL' ? 'selected' : '' }}>Retail One</option>
                                    <option value="OTROS" {{ $currentCat == 'OTROS' ? 'selected' : '' }}>Otros</option>
                                </select>
                                <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Modifica esto si el usuario se equivocó.</small>
                            </div>

                            <!-- 3. Prioridad -->
                            <div class="mb-4">
                                <label class="fw-bold text-muted small mb-2"><i class="fas fa-exclamation-triangle me-1"></i> PRIORIDAD</label>
                                <select name="priority" class="form-select border-0 shadow-sm fw-bold text-secondary"
                                    style="height: 45px; border-radius: 8px;">
                                    <option value="1" {{ $ticket->priority == 1 ? 'selected' : '' }}>🟢 Baja</option>
                                    <option value="2" {{ $ticket->priority == 2 ? 'selected' : '' }}>🟡 Normal</option>
                                    <option value="3" {{ $ticket->priority == 3 ? 'selected' : '' }}>🔴 Alta / Urgente</option>
                                </select>
                            </div>

                            <!-- 4. Asignación (Agente TI) -->
                            <div class="mb-4">
                                <label class="fw-bold text-muted small mb-2"><i class="fas fa-user-shield me-1"></i> ASIGNADO A</label>
                                <select name="user_id" class="form-select border-0 shadow-sm fw-bold text-secondary"
                                    style="height: 45px; border-radius: 8px;">
                                    <option value="">Bandeja General (Sin asignar)</option>
                                    @foreach (\App\Models\User::where('admin', 1)->get() as $agente)
                                        <option value="{{ $agente->id }}" {{ $ticket->user_id == $agente->id ? 'selected' : '' }}>
                                            {{ $agente->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <hr class="text-muted opacity-25 mb-4">

                            <button type="submit" class="btn w-100 py-3 text-white fw-bold shadow-sm"
                                style="background-color: #F4A637; border-radius: 12px; font-size: 1.1rem;">
                                <i class="fas fa-sync-alt me-2"></i> ACTUALIZAR TICKET
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

{{-- Aquí está el cambio vital: Pasamos de @section a @push --}}
@push('scripts')
    <style>
        .tox-tinymce {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .form-check-input:checked {
            background-color: #e74c3c !important;
            border-color: #e74c3c !important;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(231, 76, 60, 0.25);
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Inicialización de TinyMCE
            tinymce.init({
                selector: '#adminBodyTextarea',
                plugins: 'image paste link lists',
                toolbar: 'bold italic underline | numlist bullist | link image | undo redo',
                menubar: false,
                paste_data_images: true, // Permite Ctrl+V directo en el editor
                height: 250,
                setup: function(editor) {
                    editor.on('change', function() {
                        tinymce.triggerSave();
                    });
                }
            });

            // 2. Lógica para atrapar "Ctrl+V" fuera del editor y mandarlo al input file
            document.addEventListener('paste', function(e) {
                // Si están pegando dentro de TinyMCE, ignoramos esto para no estorbar al editor
                if (e.target.closest('.tox-tinymce') || e.target.tagName === 'IFRAME') return;

                let fileInput = document.getElementById('adminAttachment');
                let pasteSuccessMsg = document.getElementById('admin-paste-success');

                if (!fileInput) return;

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
                    
                    // Si ya tenían un archivo cargado antes, lo conservamos en la lista
                    if (fileInput.files.length) {
                        for (let f of fileInput.files) {
                            dataTransfer.items.add(f);
                        }
                    }
                    dataTransfer.items.add(finalFile);
                    
                    fileInput.files = dataTransfer.files;

                    if (pasteSuccessMsg) {
                        pasteSuccessMsg.classList.remove('d-none');
                        setTimeout(() => pasteSuccessMsg.classList.add('d-none'), 5000);
                    }
                }
            });
        });
    </script>
@endpush
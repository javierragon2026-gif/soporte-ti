@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Encabezado Limpio -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold" style="color: #67768A;"><i class="fas fa-tools me-2" style="color: #F4A637;"></i> Gestión de Reporte</h4>
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Volver a Bandeja
                </a>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}: {{ $ticket->title }}</h5>
                        <span class="badge shadow-sm" style="background-color: {{ $ticket->status == 1 ? '#61b0a5' : ($ticket->status == 5 ? '#67768A' : '#F4A637') }}; font-size: 0.9rem;">
                            {{ method_exists($ticket, 'statusName') ? $ticket->statusName() : $ticket->status }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4 bg-light">
                    <!-- Ticket Original -->
                    <div class="p-4 bg-white shadow-sm mb-4" style="border-radius: 8px; border-left: 4px solid #F4A637;">
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <strong style="color: #67768A;"><i class="fas fa-user-circle me-1"></i> {{ $ticket->user->name }} (Cliente)</strong>
                            <span class="text-muted small"><i class="far fa-clock me-1"></i> {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="text-dark">
                            {!! $ticket->body !!}
                        </div>
                        
                        @if($ticket->attachments && $ticket->attachments->count() > 0)
                        <div class="mt-3 pt-3 border-top">
                            <strong class="small text-muted d-block mb-2">Evidencia Adjunta:</strong>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($ticket->attachments as $attachment)
                                    <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-paperclip me-1"></i> {{ $attachment->name ?? 'Ver Archivo' }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Hilo de Comentarios (Soporte ve TODO) -->
                    @foreach($ticket->comments as $comment)
                        <div class="p-4 shadow-sm mb-4 {{ $comment->private ? 'bg-warning bg-opacity-10' : 'bg-white' }}" style="border-radius: 8px; border-left: 4px solid {{ $comment->private ? '#e74c3c' : ($comment->user_id == $ticket->user_id ? '#F4A637' : '#61b0a5') }};">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <strong style="color: {{ $comment->user_id == $ticket->user_id ? '#67768A' : '#61b0a5' }};">
                                    <i class="fas fa-{{ $comment->user_id == $ticket->user_id ? 'user-circle' : 'headset' }} me-1"></i> 
                                    {{ $comment->user->name }} {{ $comment->user_id == $ticket->user_id ? '(Cliente)' : '(Soporte TI)' }}
                                    @if($comment->private)
                                        <span class="badge bg-danger ms-2"><i class="fas fa-lock me-1"></i> Nota Privada</span>
                                    @endif
                                </strong>
                                <span class="text-muted small"><i class="far fa-clock me-1"></i> {{ $comment->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="text-dark">
                                {!! $comment->body !!}
                            </div>
                            
                            @if($comment->attachments && $comment->attachments->count() > 0)
                            <div class="mt-3 pt-3 border-top">
                                <strong class="small text-muted d-block mb-2">Evidencia Adjunta:</strong>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($comment->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-paperclip me-1"></i> {{ $attachment->name ?? 'Ver Archivo' }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Caja de Respuesta Administrador -->
            @if(method_exists($ticket, 'canBeEdited') ? $ticket->canBeEdited() : $ticket->status != 5)
            
                <div class="card border-0 shadow-sm" style="border-radius: 12px; border-top: 4px solid #F4A637 !important;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3" style="color: #67768A; text-transform: uppercase; font-size: 0.9rem;">
                            <i class="fas fa-reply me-2" style="color: #F4A637;"></i>Añadir Respuesta o Seguimiento
                        </h6>
                        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" enctype="multipart/form-data" id="comment-form">
                            @csrf
                            
                            <div class="mb-3">
                                <textarea id="adminBodyTextarea" name="body" class="form-control bg-light border-0" rows="5" placeholder="Escribe aquí tu actualización del caso..." required>@if(auth()->user()->settings?->tickets_signature)<br><br>{{ auth()->user()->settings?->tickets_signature }}@endif</textarea>
                            </div>
                            
                            {{-- Evidencia Adjunta Extra --}}
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">ADJUNTAR EVIDENCIA EXTRA (OPCIONAL)</label>
                                <input type="file" id="adminAttachment" name="attachment" class="form-control form-control-lg bg-light border-0" accept="image/*">
                                <span id="admin-paste-success" class="text-success small fw-bold d-none mt-1"><i class="fas fa-check-circle me-1"></i> ¡Imagen capturada en adjunto!</span>
                            </div>

                            <input type="hidden" name="new_status" id="new_status" value="{{ $ticket->status }}">
                            
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-3 pt-3 border-top gap-3">
                                {{-- Switch moderno para notas privadas --}}
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="private" id="privateNote" value="1" role="switch">
                                    <label class="form-check-label text-muted fw-semibold small" for="privateNote">
                                        Nota Privada (Solo visible para TI)
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn text-white fw-bold shadow-sm px-4 py-2" style="background-color: #F4A637; border-radius: 8px;">
                                    <i class="fas fa-paper-plane me-2"></i> Enviar Respuesta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary text-center fw-bold" role="alert">
                    <i class="fas fa-lock me-2"></i> Este ticket ha sido cerrado y ya no admite más comentarios.
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .tox-tinymce { border-radius: 8px !important; border: none !important; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .form-check-input:checked { background-color: #F4A637 !important; border-color: #F4A637 !important; }
    .form-check-input:focus { box-shadow: 0 0 0 0.25rem rgba(244, 166, 55, 0.25); }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#adminBodyTextarea',
            plugins: 'image paste link lists',
            toolbar: 'bold italic underline | numlist bullist | link image | undo redo',
            menubar: false,
            paste_data_images: true, 
            height: 250,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            }
        });

        document.addEventListener('paste', function(e) {
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
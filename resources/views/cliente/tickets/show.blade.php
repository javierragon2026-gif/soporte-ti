@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Encabezado Limpio -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold" style="color: #67768A;"><i class="fas fa-ticket-alt me-2" style="color: #F4A637;"></i> Seguimiento de tu Reporte</h4>
                <a href="{{ route('cliente.tickets.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Volver al Historial
                </a>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}: {{ $ticket->title }}</h5>
                        <span class="badge shadow-sm" style="background-color: {{ $ticket->status == 1 ? '#61b0a5' : ($ticket->status == 5 ? '#67768A' : '#F4A637') }}; font-size: 0.9rem;">
                            {{ $ticket->statusName() }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4 bg-light">
                    <!-- Ticket Original -->
                    <div class="p-4 bg-white shadow-sm mb-4" style="border-radius: 8px; border-left: 4px solid #F4A637;">
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <strong style="color: #67768A;"><i class="fas fa-user-circle me-1"></i> {{ $ticket->user->name }} (Tú)</strong>
                            <span class="text-muted small"><i class="far fa-clock me-1"></i> {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="text-dark">
                            {{-- Procesamos como HTML por TinyMCE --}}
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

                    <!-- Hilo de Comentarios (FILTRADO: Solo Notas Públicas) -->
                    @foreach($ticket->comments->where('private', 0) as $comment)
                        <div class="p-4 shadow-sm mb-4 bg-white" style="border-radius: 8px; border-left: 4px solid {{ $comment->user_id == $ticket->user_id ? '#F4A637' : '#61b0a5' }};">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <strong style="color: {{ $comment->user_id == $ticket->user_id ? '#67768A' : '#61b0a5' }};">
                                    <i class="fas fa-{{ $comment->user_id == $ticket->user_id ? 'user-circle' : 'headset' }} me-1"></i> 
                                    {{ $comment->user->name }} {{ $comment->user_id == $ticket->user_id ? '(Tú)' : '(Soporte TI)' }}
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

            <!-- Caja de Respuesta Cliente -->
            @if($ticket->status != 5) {{-- 5 es STATUS_CLOSED --}}
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-top: 4px solid #61b0a5 !important;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color: #67768A;">
                        <i class="fas fa-reply me-2" style="color: #61b0a5;"></i>¿Necesitas agregar más información?
                    </h6>
                    <form method="POST" action="{{ route('requester.comments.store', $ticket) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <textarea id="bodyTextarea" name="body" class="form-control bg-light border-0" rows="5" placeholder="Responde a Sistemas aquí..." required></textarea>
                        </div>
                        
                        {{-- Evidencia Adjunta Extra --}}
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">ADJUNTAR EVIDENCIA EXTRA (OPCIONAL)</label>
                            <input type="file" id="attachment" name="attachment" class="form-control form-control-lg bg-light border-0" accept="image/*">
                            <span id="paste-success" class="text-success small fw-bold d-none mt-1"><i class="fas fa-check-circle me-1"></i> ¡Imagen capturada en adjunto!</span>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn text-white fw-bold px-4 py-2 shadow-sm" style="background-color: #61b0a5; border-radius: 8px;">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Respuesta
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
</style>

<!-- Inyección del Editor TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#bodyTextarea',
            plugins: 'image paste link lists',
            toolbar: 'bold italic underline | numlist bullist | link image | undo redo',
            menubar: false,
            paste_data_images: true, // Permite Ctrl+V de recortes visuales directos en el editor
            height: 250,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            }
        });

        // Salvaguarda para el input normal de imagen 
        document.addEventListener('paste', function(e) {
            if (e.target.closest('.tox-tinymce') || e.target.tagName === 'IFRAME') return;

            let fileInput = document.getElementById('attachment');
            let pasteSuccessMsg = document.getElementById('paste-success');
            
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
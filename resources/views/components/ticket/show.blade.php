@if( $ticket->canBeEdited() )
        {{-- Llamada al panel de asignación que ya arreglamos --}}
        @include('components.assignActions', ["endpoint" => "tickets", "object" => $ticket])
        
        <div class="mt-4 p-4 bg-white shadow-sm border-start border-4 mb-4" style="border-radius: 12px; border-color: #F4A637 !important;">
            <h6 class="fw-bold mb-3" style="color: #67768A; text-transform: uppercase; font-size: 0.9rem;">
                <i class="fas fa-reply me-2" style="color: #F4A637;"></i>Añadir Respuesta o Seguimiento
            </h6>
            
            {{-- Formulario nativo con soporte para adjuntar archivos --}}
            <form method="POST" action="{{ route('comments.store', $ticket) }}" enctype="multipart/form-data" id="comment-form">
                @csrf
                
                {{-- Área de texto para la respuesta --}}
                <textarea id="comment-text-area" name="body" class="form-control bg-light border-0 mb-3 shadow-sm" rows="4" placeholder="Escribe aquí tu actualización del caso..." required>@if(auth()->user()->settings->tickets_signature)&#13;&#13;{{ auth()->user()->settings->tickets_signature }}@endif</textarea>
                
                {{-- Componente de archivos adjuntos --}}
                <div class="mb-3">
                    @include('components.uploadAttachment', ["attachable" => $ticket, "type" => "tickets"])
                </div>
                
                {{-- Campo oculto para mantener el estado actual --}}
                <input type="hidden" name="new_status" id="new_status" value="{{ $ticket->status }}">
                
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    @if($ticket->isEscalated())
                        <button type="submit" class="btn text-white fw-bold shadow-sm px-4" style="background-color: #e74c3c; border-radius: 8px;">
                            <i class="fas fa-exclamation-circle me-2"></i> Guardar Nota Escalada
                        </button>
                    @else
                        {{-- Switch moderno para notas privadas --}}
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="private" id="privateNote" value="1" role="switch">
                            <label class="form-check-label text-muted fw-semibold small" for="privateNote">
                                Nota Privada (Solo visible para TI)
                            </label>
                        </div>
                        
                        <button type="submit" class="btn text-white fw-bold shadow-sm px-4" style="background-color: #F4A637; border-radius: 8px;">
                            <i class="fas fa-paper-plane me-2"></i> Comentar como {{ $ticket->statusName() }}
                        </button>
                    @endif
                </div>
            </form>
        </div>
    @endif
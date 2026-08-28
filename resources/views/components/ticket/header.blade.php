{{-- Información principal del Ticket --}}
<div id="ticket-info" class="p-4 bg-white shadow-sm mb-4 border-start border-5" style="border-radius: 12px; border-color: #F4A637 !important;">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <span class="badge" style="background-color: #67768A; font-size: 0.85rem;">{{ $ticket->statusName() }}</span>
            <span class="badge bg-light text-dark border border-secondary-subtle ms-1" style="font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle" style="color: #F4A637;"></i> Prioridad {{ $ticket->priorityName() }}
            </span>
            <span class="text-muted small ms-3">
                <i class="far fa-clock"></i> Registrado hace {{ $ticket->created_at?->diffForHumans() }}
            </span>
        </div>
        <button class="btn btn-sm text-white fw-bold shadow-sm" style="background-color: #F4A637;" onclick="document.getElementById('ticket-info').style.display='none'; document.getElementById('ticket-edit').style.display='block';">
            <i class="fas fa-pencil-alt me-1"></i> Editar
        </button>
    </div>
    
    <div class="mt-2">
        <h4 class="fw-bold" style="color: #67768A;">{{ $ticket->title ?? 'Requerimiento sin título' }}</h4>
        <p class="text-muted mb-0" style="font-size: 1.05rem;">{{ $ticket->body ?? 'Sin descripción detallada.' }}</p>
    </div>
</div>

{{-- Formulario de Edición Rápida (Oculto por defecto) --}}
<div id="ticket-edit" style="display: none;" class="p-4 bg-light shadow-sm mb-4 border" style="border-radius: 12px;">
    <form method="POST" action="{{ route('tickets.update', $ticket) }}">
        @csrf
        @method('PUT')
        
        <h6 class="fw-bold mb-3" style="color: #67768A;"><i class="fas fa-edit me-2"></i>Editar Requerimiento</h6>
        
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">NIVEL DE PRIORIDAD</label>
                <select name="priority" class="form-select form-select-lg border-0 shadow-sm">
                    <option value="1" @if($ticket->priority == 1) selected @endif>Baja</option>
                    <option value="2" @if($ticket->priority == 2) selected @endif>Normal</option>
                    <option value="3" @if($ticket->priority == 3) selected @endif>Alta</option>
                </select>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn text-white fw-bold shadow-sm px-4" style="background-color: #F4A637;">
                <i class="fas fa-save me-2"></i>Guardar Cambios
            </button>
            <button type="button" class="btn btn-outline-secondary fw-bold px-4" onclick="document.getElementById('ticket-edit').style.display='none'; document.getElementById('ticket-info').style.display='block';">
                Cancelar
            </button>
        </div>
    </form>
</div>
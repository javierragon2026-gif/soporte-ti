{{-- Lógica visual para el escalamiento de tickets --}}
@if($ticket->isEscalated())
    @if($ticket->status < App\Models\Ticket::STATUS_CLOSED)
        <div class="p-3 bg-danger text-white mt-4 mb-4 rounded shadow-sm">
            <form method="POST" action="{{ route('tickets.escalate.destroy', $ticket) }}">
                @csrf
                @method('DELETE')
                <i class="fas fa-flag me-2"></i> Este requerimiento ha sido escalado para revisión urgente.
                <button type="submit" class="btn btn-light btn-sm ms-3 fw-bold">
                    <i class="fas fa-flag-slash me-1"></i> Retirar Escalamiento
                </button>
            </form>
        </div>
    @endif
@else
    <div class="mt-3 text-end">
        <form method="POST" action="{{ route('tickets.escalate.store', $ticket) }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fas fa-flag me-1"></i> Escalar a Nivel Superior
            </button>
        </form>
    </div>
@endif
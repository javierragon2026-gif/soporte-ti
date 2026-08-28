{{-- Si existen tickets fusionados, mostramos este bloque --}}
@if(optional($ticket->mergedTickets)->isNotEmpty())
    <div class="mt-4 mb-3 p-3 bg-white shadow-sm border-start border-4" style="border-radius: 12px; border-color: #67768A !important;">
        <h6 class="fw-bold mb-3" style="color: #67768A; font-size: 0.9rem; text-transform: uppercase;">
            <i class="fas fa-link me-2" style="color: #F4A637;"></i>Tickets Fusionados
        </h6>
        <div class="d-flex flex-wrap gap-2">
            @foreach($ticket->mergedTickets as $merged)
                <a href="{{ route('tickets.show', $merged) }}" class="badge text-decoration-none px-3 py-2 shadow-sm" style="background-color: #F4A637; color: white; font-size: 0.85rem;">
                    #{{ $merged->id }} <i class="fas fa-external-link-alt ms-1"></i>
                </a>
            @endforeach
        </div>
    </div>
@endif
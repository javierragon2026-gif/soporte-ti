@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold" style="color: #67768A;"><i class="fas fa-history me-2"></i> Mi Historial de Tickets</h4>
        <a href="{{ route('cliente.tickets.crear') }}" class="btn text-white fw-bold shadow-sm" style="background-color: #F4A637;">Nuevo Reporte</a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="py-3">Módulo / Asunto</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3">Atendido por</th>
                        <th class="py-3">Fecha de Creación</th>
                        <th class="px-4 py-3 text-end">Seguimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="px-4 fw-bold" style="color: #F4A637;">TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="fw-bold text-dark">{{ $ticket->title }}</td>
                        <td>
                            <span class="badge px-3 py-2 shadow-sm fw-bold" 
                                  style="background-color: {{ $ticket->statusBadgeBg() }}; color: {{ $ticket->statusBadgeColor() }}; border: 1px solid {{ $ticket->statusBadgeColor() }}22;">
                                {{ $ticket->statusName() }}
                            </span>
                        </td>
                        <td>
                            @if($ticket->agent && $ticket->agent->id && $ticket->agent->name !== 'Sin asignar')
                                <span class="text-dark small fw-semibold">
                                    <i class="fas fa-headset me-1" style="color: #61b0a5;"></i> {{ $ticket->agent->name }}
                                </span>
                            @else
                                <span class="text-muted small fst-italic">
                                    <i class="far fa-clock me-1"></i> Por asignar
                                </span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $ticket->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="px-4 text-end">
                            <a href="{{ route('cliente.tickets.show', $ticket) }}" class="btn btn-sm text-white fw-bold shadow-sm" style="background-color: #61b0a5; border-radius: 6px;">
                                Ver Detalle <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No has reportado ningún problema aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
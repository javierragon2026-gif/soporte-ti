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
                        <th class="py-3">Módulo/Asunto</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3">Fecha de Creación</th>
                        <th class="px-4 py-3 text-end">Seguimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="px-4 fw-bold text-muted">#{{ $ticket->id }}</td>
                        <td class="fw-bold">{{ $ticket->title }}</td>
                        <td>
                            @if($ticket->status == 1) <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif($ticket->status == 3) <span class="badge bg-success">Resuelto</span>
                            @else <span class="badge bg-primary">En Revisión</span> @endif
                        </td>
                        <td class="text-muted small">{{ $ticket->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 text-end">
                            <a href="{{ route('cliente.tickets.show', $ticket) }}" class="btn btn-sm" style="background-color: #61b0a5; color: white;">Ver Detalle</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No has reportado ningún problema aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
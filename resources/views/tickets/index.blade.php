@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold" style="color: #67768A;">
            <i class="fas fa-list me-2" style="color: #F4A637;"></i> Bandeja de Requerimientos
        </h4>
        <a href="{{ route('tickets.create') }}" class="btn text-white fw-bold shadow-sm" style="background-color: #F4A637;">
            <i class="fas fa-plus me-2"></i> Nuevo Ticket
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light" style="color: #67768A;">
                    <tr>
                        <th class="px-4 py-3 border-0">ID</th>
                        <th class="py-3 border-0">Asunto</th>
                        <th class="py-3 border-0">Asignado A</th>
                        <th class="py-3 border-0">Estado</th>
                        <th class="py-3 border-0">Fecha</th>
                        <th class="px-4 py-3 border-0 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="px-4 fw-bold text-muted">#{{ $ticket->id }}</td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none fw-bold" style="color: #2c3e50;">
                                {{ $ticket->title }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $ticket->user ? $ticket->user->name : 'Bandeja General' }}
                            </span>
                        </td>
                        <td>
                            @if($ticket->status == 1)
                                <span class="badge bg-warning text-dark">Abierto</span>
                            @elseif($ticket->status == 4 || $ticket->status == 5)
                                <span class="badge bg-success">Resuelto</span>
                            @else
                                <span class="badge bg-primary">En Proceso</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '--' }}
                        </td>
                        <td class="px-4 text-end">
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <h5>No hay tickets registrados</h5>
                            <p>No se encontraron requerimientos en esta bandeja.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Paginación nativa de Laravel --}}
        @if(method_exists($tickets, 'hasPages') && $tickets->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $tickets->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
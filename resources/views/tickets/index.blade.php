@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: #67768A; font-weight: 700;">Panel de Tickets</h2>
            <a href="{{ route('tickets.create') }}" class="btn text-white"
                style="background-color: #F4A637; font-weight: 600;">
                <i class="fas fa-plus me-1"></i> Nuevo Ticket
            </a>
            

        </div>

        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #67768A; color: white;">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Asignado</th>
                                <th>Fecha</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#{{ $ticket->id }}</td>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                            class="text-decoration-none fw-semibold" style="color: #333;">
                                            {{ $ticket->title ?? 'Sin asunto' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($ticket->status == 1)
                                            <span class="badge bg-success">Abierto</span>
                                        @elseif($ticket->status == 2)
                                            <span class="badge bg-warning text-dark">En proceso</span>
                                        @else
                                            <span class="badge bg-secondary">Cerrado</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->user->name ?? 'Sin asignar' }}</td>
                                    <td class="text-muted" style="font-size: 0.85rem;">
                                        {{ $ticket->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-ticket-alt fa-3x mb-3 d-block" style="color: #ccc;"></i>
                                        No hay tickets registrados en el sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Paginación --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $tickets->links() }}
        </div>
    </div>
@endsection

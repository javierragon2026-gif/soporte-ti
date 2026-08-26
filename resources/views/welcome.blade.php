@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-md-5">

    {{-- Encabezado de Bienvenida --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold mb-1" style="color: #67768A;">Panel de Control TI</h2>
            <p class="text-muted mb-0">Bienvenido de nuevo, <span class="fw-semibold" style="color: #F4A637;">{{ auth()->user()->name ?? 'Agente' }}</span></p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('tickets.create') }}" class="btn text-white px-4 py-2 shadow-sm" style="background-color: #F4A637; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-plus me-2"></i>Crear Ticket
            </a>
        </div>
    </div>

    {{-- Tarjetas de Métricas Principales --}}
    <div class="row g-3 mb-4">
        {{-- Card: Abiertos / Pendientes --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: #ffffff; border-radius: 12px; border-left: 5px solid #F4A637 !important;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small fw-bold text-uppercase">Tickets Abiertos</span>
                        <h3 class="fw-bold my-1" style="color: #67768A;">
                            {{ \App\Models\Ticket::where('status', 1)->count() }}
                        </h3>
                        <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">Requieren atención</span>
                    </div>
                    <div class="p-3 rounded-circle" style="background-color: rgba(244, 166, 55, 0.15);">
                        <i class="fas fa-exclamation-circle fa-2x" style="color: #F4A637;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Sin Asignar --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: #ffffff; border-radius: 12px; border-left: 5px solid #e74c3c !important;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small fw-bold text-uppercase">Sin Asignar</span>
                        <h3 class="fw-bold my-1" style="color: #67768A;">
                            {{ \App\Models\Ticket::whereNull('user_id')->where('status', '!=', 3)->count() }}
                        </h3>
                        <span class="badge bg-danger" style="font-size: 0.75rem;">Bandeja general</span>
                    </div>
                    <div class="p-3 rounded-circle" style="background-color: rgba(231, 76, 60, 0.15);">
                        <i class="fas fa-inbox fa-2x" style="color: #e74c3c;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Mis Tickets Asignados --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: #ffffff; border-radius: 12px; border-left: 5px solid #67768A !important;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small fw-bold text-uppercase">Mis Asignaciones</span>
                        <h3 class="fw-bold my-1" style="color: #67768A;">
                            {{ \App\Models\Ticket::where('user_id', auth()->id())->where('status', '!=', 3)->count() }}
                        </h3>
                        <span class="badge text-white" style="background-color: #67768A; font-size: 0.75rem;">En seguimiento</span>
                    </div>
                    <div class="p-3 rounded-circle" style="background-color: rgba(103, 118, 138, 0.15);">
                        <i class="fas fa-user-check fa-2x" style="color: #67768A;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Resueltos / Cerrados --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: #ffffff; border-radius: 12px; border-left: 5px solid #2ecc71 !important;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small fw-bold text-uppercase">Resueltos</span>
                        <h3 class="fw-bold my-1" style="color: #67768A;">
                            {{ \App\Models\Ticket::where('status', 3)->count() }}
                        </h3>
                        <span class="badge bg-success" style="font-size: 0.75rem;">Completados</span>
                    </div>
                    <div class="p-3 rounded-circle" style="background-color: rgba(46, 204, 113, 0.15);">
                        <i class="fas fa-check-circle fa-2x" style="color: #2ecc71;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sección Inferior: Tickets Recientes y Accesos Rápidos --}}
    <div class="row g-4">
        {{-- Tabla de Últimos Tickets --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: #67768A;">
                        <i class="fas fa-history me-2" style="color: #F4A637;"></i>Requerimientos Recientes
                    </h5>
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-link text-decoration-none fw-semibold" style="color: #F4A637;">
                        Ver todos <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #f8f9fa; color: #67768A; font-size: 0.85rem;">
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Asunto</th>
                                    <th>Estado</th>
                                    <th>Asignado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Ticket::with('user')->latest('created_at')->take(5)->get() as $ticket)
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted">#{{ $ticket->id }}</td>
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none fw-semibold" style="color: #333;">
                                                {{ $ticket->title ?? 'Sin Asunto' }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($ticket->status == 1)
                                                <span class="badge bg-warning text-dark">Abierto</span>
                                            @elseif($ticket->status == 2)
                                                <span class="badge bg-primary">En Proceso</span>
                                            @else
                                                <span class="badge bg-success">Cerrado</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->user->name ?? 'Sin asignar' }}</td>
                                        <td class="text-muted small">{{ $ticket->created_at?->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No hay requerimientos pendientes por mostrar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Lateral de Estado Rápido --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: #ffffff;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold" style="color: #67768A;">
                        <i class="fas fa-tools me-2" style="color: #F4A637;"></i>Mesa de Ayuda
                    </h5>
                </div>
                <div class="card-body">
                    <div class="p-3 mb-3 rounded" style="background-color: #FFF8F0; border: 1px solid #FFDCA8;">
                        <h6 class="fw-bold mb-1" style="color: #67768A;">Disponibilidad de TI</h6>
                        <p class="small text-muted mb-0">Horario operativo: 08:00 - 18:00 hrs. Los tickets creados fuera de horario pasan a cola automática.</p>
                    </div>

                    <div class="list-group list-group-flush">
                        <a href="{{ route('tickets.index') }}?unassigned=true" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-inbox me-2" style="color: #67768A;"></i>Tomar ticket sin asignar</span>
                            <i class="fas fa-chevron-right text-muted small"></i>
                        </a>
                        <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-user-cog me-2" style="color: #67768A;"></i>Ajustes de mi cuenta</span>
                            <i class="fas fa-chevron-right text-muted small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
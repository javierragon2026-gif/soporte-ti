@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold" style="color: #67768A;"><i class="fas fa-laptop-house me-2"></i> Mis Solicitudes de Home Office</h4>
        <a href="{{ route('cliente.home-office.create') }}" class="btn text-white fw-bold shadow-sm" style="background-color: #F4A637;">
            <i class="fas fa-plus me-1"></i> Nueva Solicitud
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="py-3">Periodo (Fechas)</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3">Equipo Asignado</th>
                        <th class="px-4 py-3 text-end">Salida / Ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="px-4 fw-bold text-dark">
                            @if($req->request_type == 'shared_loan')
                                <i class="fas fa-laptop me-1 text-primary"></i> Préstamo de Equipo
                            @else
                                <i class="fas fa-door-open me-1 text-warning"></i> Pase de Salida (Propio)
                            @endif
                        </td>
                        <td class="text-muted small">
                            <strong>{{ $req->scheduled_start_date->format('d/m/Y') }}</strong> al <strong>{{ $req->scheduled_end_date->format('d/m/Y') }}</strong>
                        </td>
                        <td>
                            @php
                                $badgeColor = match($req->status) {
                                    'pending' => 'bg-warning text-dark',
                                    'approved' => 'bg-info text-white',
                                    'active' => 'bg-primary text-white',
                                    'returned' => 'bg-success text-white',
                                    'cancelled' => 'bg-danger text-white',
                                    default => 'bg-secondary text-white'
                                };
                                $statusLabel = match($req->status) {
                                    'pending' => 'Pendiente',
                                    'approved' => 'Aprobado (Por recoger)',
                                    'active' => 'Activo (Entregado)',
                                    'returned' => 'Devuelto (Completado)',
                                    'cancelled' => 'Cancelado',
                                    default => 'Desconocido'
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }} shadow-sm">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            @if($req->device)
                                <span class="badge bg-light text-dark border"><i class="fas fa-desktop me-1"></i> {{ $req->device->name }}</span>
                            @else
                                <span class="text-muted fst-italic small">N/A o Por asignar</span>
                            @endif
                        </td>
                        <td class="px-4 text-end small">
                            @if($req->checkout_at)
                                <span class="d-block text-success" title="Entregado por {{ $req->checkoutAgent->name ?? 'TI' }}">
                                    <i class="fas fa-sign-out-alt"></i> Salida: {{ $req->checkout_at->format('d/m H:i') }}
                                </span>
                            @endif
                            @if($req->checkin_at)
                                <span class="d-block text-primary mt-1" title="Recibido por {{ $req->checkinAgent->name ?? 'TI' }}">
                                    <i class="fas fa-sign-in-alt"></i> Ingreso: {{ $req->checkin_at->format('d/m H:i') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No has realizado ninguna solicitud de Home Office.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        {{ $requests->links() }}
    </div>
</div>
@endsection


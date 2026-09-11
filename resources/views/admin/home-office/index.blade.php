@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold" style="color: #67768A;"><i class="fas fa-inbox me-2" style="color: #F4A637;"></i> Bandeja de Control: Home Office</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="px-4 py-3">ID Req.</th>
                        <th class="py-3">Colaborador</th>
                        <th class="py-3">Tipo / Equipo</th>
                        <th class="py-3">Fechas Programadas</th>
                        <th class="py-3">Estado</th>
                        <th class="px-4 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="px-4 fw-bold text-dark">HO-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <strong class="text-primary">{{ $req->user->name }}</strong>
                            <span class="d-block small text-muted"><i class="fas fa-envelope me-1"></i> {{ $req->user->email }}</span>
                        </td>
                        <td>
                            @if($req->request_type == 'shared_loan')
                                <span class="badge bg-light text-primary border mb-1"><i class="fas fa-laptop me-1"></i> Préstamo</span>
                            @else
                                <span class="badge bg-light text-warning text-dark border mb-1"><i class="fas fa-door-open me-1"></i> Pase</span>
                            @endif
                            <br>
                            <span class="small text-muted fw-bold">{{ $req->device->name ?? 'Sin equipo asignado' }}</span>
                        </td>
                        <td class="small">
                            <span class="d-block"><i class="fas fa-calendar-alt text-muted me-1"></i> S: {{ $req->scheduled_start_date->format('d/m/Y') }}</span>
                            
                            @php 
                                $isOverdue = ($req->status === 'active' || $req->status === 'approved') && \Carbon\Carbon::parse($req->scheduled_end_date)->isPast() && !\Carbon\Carbon::parse($req->scheduled_end_date)->isToday(); 
                            @endphp
                            
                            <span class="d-block mt-1 {{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                <i class="fas fa-calendar-check {{ $isOverdue ? 'text-danger' : 'text-muted' }} me-1"></i> I: {{ $req->scheduled_end_date->format('d/m/Y') }}
                            </span>
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
                                    'pending' => 'Por Aprobar',
                                    'approved' => 'Aprobado / Pend. Salida',
                                    'active' => 'Activo (Fuera)',
                                    'returned' => 'Completado',
                                    'cancelled' => 'Cancelado',
                                    default => 'Desconocido'
                                };
                            @endphp
                            
                            @if($isOverdue)
                                <span class="badge bg-danger shadow-sm mb-1 d-block" style="font-size: 0.75rem;"><i class="fas fa-exclamation-circle"></i> ATRASADO</span>
                            @endif
                            
                            <span class="badge {{ $badgeColor }} shadow-sm d-block">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-4 text-end">
                            <a href="{{ route('admin.home-office.show', $req) }}" class="btn btn-sm text-white fw-bold shadow-sm" style="background-color: #61b0a5; border-radius: 6px;" title="Gestionar">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a href="{{ route('admin.home-office.edit', $req) }}" class="btn btn-sm btn-outline-secondary fw-bold shadow-sm" style="border-radius: 6px;" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.home-office.destroy', $req) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro histórico?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold shadow-sm" style="border-radius: 6px;" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No hay solicitudes de Home Office activas.</td></tr>
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


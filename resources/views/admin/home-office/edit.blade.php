@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark"><i class="fas fa-edit me-2" style="color: #61b0a5;"></i> Editar Solicitud: HO-{{ str_pad($homeOffice->id, 4, '0', STR_PAD_LEFT) }}</h4>
        <a href="{{ route('admin.home-office.show', $homeOffice) }}" class="btn btn-outline-secondary btn-sm fw-bold">Cancelar</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px; max-width: 600px;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.home-office.update', $homeOffice) }}">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Colaborador</label>
                    <input type="text" class="form-control bg-light" value="{{ $homeOffice->user->name }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Fecha de Salida</label>
                    <input type="date" name="scheduled_start_date" class="form-control" value="{{ $homeOffice->scheduled_start_date->format('Y-m-d') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Fecha de Regreso (Devolución)</label>
                    <input type="date" name="scheduled_end_date" class="form-control" value="{{ $homeOffice->scheduled_end_date->format('Y-m-d') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Estado</label>
                    <select name="status" class="form-select" required>
                        <option value="pending" {{ $homeOffice->status == 'pending' ? 'selected' : '' }}>Por Aprobar</option>
                        <option value="approved" {{ $homeOffice->status == 'approved' ? 'selected' : '' }}>Aprobado / Pend. Salida</option>
                        <option value="active" {{ $homeOffice->status == 'active' ? 'selected' : '' }}>Activo (Fuera)</option>
                        <option value="returned" {{ $homeOffice->status == 'returned' ? 'selected' : '' }}>Devuelto (Completado)</option>
                        <option value="cancelled" {{ $homeOffice->status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                @if($homeOffice->request_type == 'shared_loan')
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Equipo Asignado</label>
                        <select name="device_id" class="form-select">
                            <option value="">-- Sin equipo asignado --</option>
                            @foreach($availableDevices as $d)
                                <option value="{{ $d->id }}" {{ $homeOffice->device_id == $d->id ? 'selected' : '' }}>
                                    {{ $d->name }} ({{ $d->serial_number }})
                                </option>
                            @endforeach
                        </select>
                        <span class="small text-muted d-block mt-1">Si cambias el equipo, el anterior se marcará como Disponible.</span>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary fw-bold w-100 shadow-sm" style="background-color: #61b0a5; border-color: #61b0a5;">
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>
</div>
@endsection


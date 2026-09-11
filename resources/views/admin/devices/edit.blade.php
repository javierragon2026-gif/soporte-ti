@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark"><i class="fas fa-edit me-2" style="color: #61b0a5;"></i> Editar Equipo</h4>
        <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">Volver al Inventario</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px; max-width: 600px;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.devices.update', $device) }}">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Nombre del Equipo</label>
                    <input type="text" name="name" class="form-control" value="{{ $device->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Tipo</label>
                    <select name="type" class="form-select" required>
                        <option value="shared" {{ $device->type == 'shared' ? 'selected' : '' }}>Compartido (Para Préstamos de HO)</option>
                        <option value="assigned" {{ $device->type == 'assigned' ? 'selected' : '' }}>Asignado Fijo</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Número de Serie</label>
                    <input type="text" name="serial_number" class="form-control" value="{{ $device->serial_number }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Estado Actual</label>
                    <select name="status" class="form-select" required>
                        <option value="available" {{ $device->status == 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="loaned" {{ $device->status == 'loaned' ? 'selected' : '' }}>Prestado / En Uso</option>
                        <option value="maintenance" {{ $device->status == 'maintenance' ? 'selected' : '' }}>En Mantenimiento</option>
                        <option value="retired" {{ $device->status == 'retired' ? 'selected' : '' }}>Dado de Baja</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">Notas Adicionales</label>
                    <textarea name="notes" class="form-control" rows="2">{{ $device->notes }}</textarea>
                </div>

                <button type="submit" class="btn text-white fw-bold w-100 shadow-sm" style="background-color: #61b0a5; border-color: #61b0a5;">
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>
</div>
@endsection


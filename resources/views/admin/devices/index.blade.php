@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold" style="color: #67768A;"><i class="fas fa-boxes me-2"></i> Inventario de Equipos (Home Office)</h4>
        <a href="#" class="btn text-white fw-bold shadow-sm" style="background-color: #61b0a5;" data-bs-toggle="modal" data-bs-target="#newDeviceModal">
            <i class="fas fa-plus me-1"></i> Registrar Equipo
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="px-4 py-3">ID / Activo</th>
                        <th class="py-3">Nombre del Equipo</th>
                        <th class="py-3">Número de Serie</th>
                        <th class="py-3">Tipo</th>
                        <th class="py-3">Estado Actual</th>
                        <th class="px-4 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                    <tr>
                        <td class="px-4 fw-bold text-muted">INV-{{ str_pad($device->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="fw-bold text-dark">{{ $device->name }}</td>
                        <td class="text-muted">{{ $device->serial_number ?? 'N/A' }}</td>
                        <td>
                            @if($device->type == 'shared')
                                <span class="badge bg-light text-primary border"><i class="fas fa-share-alt me-1"></i> Préstamo Compartido</span>
                            @else
                                <span class="badge bg-light text-secondary border"><i class="fas fa-user-lock me-1"></i> Asignado Fijo</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = match($device->status) {
                                    'available' => 'bg-success',
                                    'loaned' => 'bg-warning text-dark',
                                    'maintenance' => 'bg-danger',
                                    'retired' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                                $statusName = match($device->status) {
                                    'available' => 'Disponible',
                                    'loaned' => 'En Préstamo',
                                    'maintenance' => 'En Mantenimiento',
                                    'retired' => 'Dado de Baja',
                                    default => 'Desconocido'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} shadow-sm">{{ $statusName }}</span>
                        </td>
                        <td class="px-4 text-end">
                            <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-outline-secondary fw-bold shadow-sm" style="border-radius: 6px;" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.devices.destroy', $device) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este equipo del inventario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold shadow-sm" style="border-radius: 6px;" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No hay equipos registrados en el inventario.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        {{ $devices->links() }}
    </div>
</div>

<!-- Modal Nuevo Equipo -->
<div class="modal fade" id="newDeviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" style="color: #67768A;"><i class="fas fa-laptop me-2"></i> Registrar Nuevo Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <form action="{{ route('admin.devices.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">NOMBRE (EJ. LAPTOP PRÉSTAMO 05)</label>
                        <input type="text" name="name" class="form-control border-0 shadow-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">NÚMERO DE SERIE / SERVICE TAG</label>
                        <input type="text" name="serial_number" class="form-control border-0 shadow-sm">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-muted small">TIPO DE EQUIPO</label>
                            <select name="type" class="form-select border-0 shadow-sm" required>
                                <option value="shared">Comodín (Préstamo)</option>
                                <option value="assigned">Asignado a un usuario</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-muted small">ESTADO INICIAL</label>
                            <select name="status" class="form-select border-0 shadow-sm" required>
                                <option value="available">Disponible</option>
                                <option value="loaned">Ya prestado / Asignado</option>
                                <option value="maintenance">En Mantenimiento</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">NOTAS / CARACTERÍSTICAS</label>
                        <textarea name="notes" class="form-control border-0 shadow-sm" rows="2"></textarea>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn text-white fw-bold shadow-sm" style="background-color: #61b0a5;">
                            Guardar Equipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


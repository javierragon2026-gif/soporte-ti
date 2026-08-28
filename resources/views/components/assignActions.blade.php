{{-- Panel de Asignación y Etiquetas --}}
<div class="mt-4 p-4 bg-white shadow-sm border-start border-4 mb-4" style="border-radius: 12px; border-color: #67768A !important;">
    <h6 class="fw-bold mb-3" style="color: #67768A; text-transform: uppercase; font-size: 0.9rem;">
        <i class="fas fa-users-cog me-2" style="color: #F4A637;"></i>Asignación y Detalles
    </h6>

    {{-- Formulario HTML nativo apuntando a la ruta dinámica --}}
    <form method="POST" action="{{ route($endpoint . '.assign', $object) }}">
        @csrf

        <div class="row g-3 align-items-center mb-3">
            <div class="col-md-3">
                <label for="tags" class="col-form-label text-muted fw-bold small">ETIQUETAS:</label>
            </div>
            <div class="col-md-9">
                <input type="text" id="tags" name="tags" class="form-control bg-light border-0 shadow-sm" 
                       value="{{ method_exists($object, 'tagsString') ? $object->tagsString() : '' }}" 
                       placeholder="Ej. red, impresora, urgente">
            </div>
        </div>

        {{-- Validamos si el usuario tiene permiso para reasignar el equipo --}}
        @can("assignToTeam", $object)
            {{-- Nota: Dejamos el include original preparado. Si falla, lo adaptaremos luego --}}
            @include('components.assignTeamField', ["team" => $object->team])
        @endcan

        <div class="row g-3 align-items-center mb-3">
            <div class="col-md-3">
                <label for="user_id" class="col-form-label text-muted fw-bold small">ASIGNADO A:</label>
            </div>
            <div class="col-md-9">
                <select name="user_id" id="user_id" class="form-select bg-light border-0 shadow-sm">
                    <option value="">Sin Asignar (Bandeja General)</option>
                    
                    {{-- Iteramos sobre los usuarios registrados usando Eloquent nativo --}}
                    @foreach(\App\Models\User::all() as $agente)
                        <option value="{{ $agente->id }}" {{ $object->user_id == $agente->id ? 'selected' : '' }}>
                            {{ $agente->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="text-end mt-3 pt-3 border-top">
            <button type="submit" class="btn text-white fw-bold px-4" style="background-color: #67768A; border-radius: 8px;">
                <i class="fas fa-save me-2"></i>Actualizar Asignación
            </button>
        </div>
    </form>
</div>
<div class="sidebar p-3" style="background-color: #67768A; min-height: 100vh; color: #fff;">
    
    {{-- SECCIÓN: TICKETS --}}
    <h4 class="mt-2 mb-3 fw-bold" style="color: #F4A637; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
        <i class="fas fa-inbox me-2"></i> Mesa de Ayuda
    </h4>
    <ul class="nav flex-column mb-4">
        <li class="nav-item mb-1">
            <a class="nav-link text-white d-flex align-items-center" href="{{ route('tickets.index') }}" style="transition: 0.3s;">
                <i class="fas fa-folder-open me-3 w-20px"></i> Abiertos
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link text-white d-flex align-items-center" href="{{ route('tickets.index') }}?unassigned=true" style="transition: 0.3s;">
                <i class="fas fa-user-slash me-3 w-20px"></i> Sin Asignar
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link text-white d-flex align-items-center" href="{{ route('tickets.index') }}?mine=true" style="transition: 0.3s;">
                <i class="fas fa-user-check me-3 w-20px"></i> Mis Tickets
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link text-white d-flex align-items-center" href="{{ route('tickets.index') }}?closed=true" style="transition: 0.3s;">
                <i class="fas fa-check-circle me-3 w-20px"></i> Resueltos / Cerrados
            </a>
        </li>
    </ul>

    {{-- SECCIÓN: ADMINISTRACIÓN --}}
    <h4 class="mt-4 mb-3 fw-bold" style="color: #F4A637; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
        <i class="fas fa-cog me-2"></i> Administración
    </h4>
    <ul class="nav flex-column">
        <li class="nav-item mb-1">
            {{-- La ruta se corregirá cuando adaptemos el módulo de equipos --}}
            <a class="nav-link text-white d-flex align-items-center" href="#" style="transition: 0.3s;">
                <i class="fas fa-users me-3 w-20px"></i> Equipos de TI
            </a>
        </li>
    </ul>
</div>
<!-- Título / Logo Principal -->
<a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-4 text-white text-decoration-none">
    <i class="fas fa-headset fa-2x me-3" style="color: #F4A637;"></i>
    <span class="fs-5 fw-bold" style="letter-spacing: 1px;">Soporte TI</span>
</a>

<hr style="border-color: rgba(255,255,255,0.1);">

<!-- Navegación Central -->
<ul class="nav nav-pills flex-column mb-auto">
    
    <h6 class="mt-2 mb-3 fw-bold" style="color: #F4A637; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
        Mesa de Ayuda
    </h6>
    
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
            <i class="fas fa-check-circle me-3 w-20px"></i> Cerrados
        </a>
    </li>

    <h6 class="mt-4 mb-3 fw-bold" style="color: #F4A637; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
        Administración
    </h6>
    <li class="nav-item mb-1">
        <a class="nav-link text-white d-flex align-items-center" href="#" style="transition: 0.3s;">
            <i class="fas fa-users me-3 w-20px"></i> Equipos de TI
        </a>
    </li>
</ul>

<hr style="border-color: rgba(255,255,255,0.1);">

<!-- Menú Inferior (Usuario y Salir) -->
<div class="dropdown">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle fa-2x me-2" style="color: #F4A637;"></i>
        <span class="fw-semibold">{{ auth()->user()->name ?? 'Agente de TI' }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-dark text-small shadow border-0 mt-2" aria-labelledby="dropdownUser">
        <li>
            <a class="dropdown-item" href="{{ route('profile.show') }}">
                <i class="fas fa-id-badge me-2 text-muted"></i> Ajustes de Perfil
            </a>
        </li>
        <li><hr class="dropdown-divider border-secondary"></li>
        <li>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="dropdown-item fw-bold text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                </button>
            </form>
        </li>
    </ul>
</div>
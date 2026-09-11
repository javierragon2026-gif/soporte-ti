<ul class="nav nav-pills flex-column mb-auto">

    {{-- ========================================================
         VISTA DE USUARIO FINAL (Visible para TODOS)
         ======================================================== --}}
    <h6 class="mt-2 mb-3 fw-bold"
        style="color: #F4A637; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
        Mi Portal
    </h6>

    <li class="nav-item mb-1">
        <a class="nav-link text-white d-flex align-items-center" href="{{ route('cliente.tickets.crear') }}">
            <i class="fas fa-plus-circle me-3 w-20px"></i> Crear Ticket
        </a>
    </li>
    <li class="nav-item mb-1">
        <a class="nav-link text-white d-flex align-items-center" href="{{ route('cliente.tickets.index') }}">
            <i class="fas fa-history me-3 w-20px"></i> Mis Tickets (Historial)
        </a>
    </li>

    <li class="nav-item mb-1">
        <a class="nav-link text-white d-flex align-items-center" href="{{ route('cliente.home-office.index') }}">
            <i class="fas fa-laptop-house me-3 w-20px"></i> Home Office
        </a>
    </li>

    {{-- ========================================================
         VISTA DE SISTEMAS (Solo visible para los 5 administradores)
         ======================================================== --}}
    @if (auth()->user()?->admin)
        <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">

        <h6 class="mb-3 fw-bold"
            style="color: #F4A637; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
            Panel de Sistemas
        </h6>

        <li class="nav-item mb-1">
            <a class="nav-link text-white d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-3 w-20px"></i> Dashboard TI
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link text-white d-flex align-items-center" href="{{ route('tickets.index') }}">
                <i class="fas fa-inbox me-3 w-20px"></i> Bandeja de Tickets
            </a>
        </li>
        
        <h6 class="mt-4 mb-3 fw-bold"
            style="color: #61b0a5; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
            Control de Activos
        </h6>
        
        <li class="nav-item mb-1">
            <a class="nav-link text-white d-flex align-items-center" href="{{ route('admin.home-office.index') }}">
                <i class="fas fa-boxes me-3 w-20px"></i> Bandeja Home Office
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link text-white d-flex align-items-center" href="{{ route('admin.devices.index') }}">
                <i class="fas fa-laptop me-3 w-20px"></i> Inventario de Equipos
            </a>
        </li>
        
        <h6 class="mt-4 mb-3 fw-bold"
            style="color: #61b0a5; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
            Ajustes de TI
        </h6>
        <li class="nav-item mb-1">
            <a class="nav-link text-white d-flex align-items-center" href="#">
                <i class="fas fa-users me-3 w-20px"></i> Personal
            </a>
        </li>
    @endif
</ul>

<hr style="border-color: rgba(255,255,255,0.1);">
<!-- Botón de Salir -->
<form action="{{ route('logout') }}" method="POST" class="m-0 mt-auto">
    @csrf
    <button type="submit" class="btn w-100 text-white fw-bold" style="background-color: #e74c3c;">
        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
    </button>
</form>

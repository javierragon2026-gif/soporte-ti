<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Soporte TI Ragon</title>

    <link rel="icon" href="{{ asset('img/favicon_grupo_ragon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="bg-light">

    <!-- Barra Superior -->
    <nav class="navbar navbar-dark shadow-sm px-3" style="background-color: #2c3e50;">
        <div class="d-flex align-items-center">
            <!-- BOTÓN DE HAMBURGUESA UNIVERSAL -->
            <button class="btn btn-link text-white text-decoration-none me-3" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                <i class="fas fa-bars fa-lg" style="color: #F4A637;"></i>
            </button>
            <a class="navbar-brand fw-bold mb-0" href="/welcome">
                Soporte TI Ragon
            </a>
        </div>

        <div class="d-flex align-items-center">
            <span class="text-white me-3 fw-semibold d-none d-md-block">
                <i class="fas fa-user-circle me-1" style="color: #F4A637;"></i> {{ auth()->user()?->name }} </span>
        </div>
    </nav>

    <!-- Menú Lateral Oculto (Offcanvas) -->
    <div class="offcanvas offcanvas-start shadow" tabindex="-1" id="sidebarMenu"
        style="width: 280px; background-color: #67768A;">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title text-white fw-bold">Menú Principal</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3 d-flex flex-column">
            @include('layouts.sidebar')
        </div>
    </div>

    <!-- Área de Contenido -->
    <main class="container-fluid p-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>

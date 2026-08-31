<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Mesa de Ayuda TI</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/favicon_grupo_ragon.ico') }}">

    <!-- Fuentes e Iconos (FontAwesome y Bootstrap 5) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-light">
    
    <!-- Contenedor Flexbox (100% del alto de la pantalla) -->
    <div class="d-flex vh-100 overflow-hidden">
        
        <!-- BARRA LATERAL (Fija, agrupa Navegación + Usuario) -->
        <div class="d-flex flex-column flex-shrink-0 p-3 shadow-sm" style="width: 280px; background-color: #2c3e50;">
            @include('layouts.sidebar')
        </div>

        <!-- ÁREA DE CONTENIDO (Tabla de tickets o Dashboard) -->
        <div class="flex-grow-1 p-4 overflow-auto">
            @yield('content')
        </div>
        
    </div>

    <!-- Scripts Esenciales -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Espacio dinámico para inyectar scripts (Ej. Animación del Casino) -->
    @stack('scripts')
</body>
</html>
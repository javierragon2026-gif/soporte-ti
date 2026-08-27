<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Soporte TI') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/favicon_grupo_ragon.ico') }}">

    <!-- Bootstrap 5 y FontAwesome 6 (Moderno) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Tus Estilos CSS Modulares -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background-color: #F4F6F9;">
    
    <!-- Contenedor Flexbox para dividir pantalla -->
    <div id="app" class="d-flex" style="min-height: 100vh;">
        
        <!-- BARRA LATERAL (Fija a la izquierda, 260px de ancho) -->
        <aside class="sidebar-wrapper" style="width: 260px; flex-shrink: 0; background-color: #67768A; box-shadow: 2px 0 10px rgba(0,0,0,0.1); z-index: 10;">
            @include('layouts.sidebar')
        </aside>

        <!-- CONTENIDO PRINCIPAL (Ocupa el resto de la pantalla) -->
        <main class="content-wrapper flex-grow-1 d-flex flex-column" style="overflow-x: hidden;">
            
            {{-- Barra superior (TinyHeader) --}}
            <div style="background-color: #ffffff; border-bottom: 1px solid #e0e4e8; padding: 10px 20px;">
                @include('layouts.tinyHeader')
            </div>
            
            {{-- Área donde se inyecta el Dashboard o los Tickets --}}
            <div class="container-fluid p-4">
                @include('components.errors')
                @yield('content')
            </div>

        </main>
    </div>

    <!-- Scripts de Bootstrap y Sistema -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
    @stack('edit-scripts')

</body>
</html>
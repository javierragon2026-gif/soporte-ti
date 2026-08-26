<!doctype html>
<!-- Detecta el idioma del sistema automáticamente (ya lo pasamos a 'es') -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Token de seguridad de Laravel (Obligatorio para evitar ataques CSRF) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Título de la pestaña: Toma el nombre de tu .env o usa "Sistema de Tickets" por defecto -->
    <title>{{ config('app.name', 'Sistema de Tickets') }}</title>

    <!-- Favicon de tu empresa -->
    <link rel="icon" href="{{ asset('img/favicon_grupo_ragon.ico') }}">

    <!-- Fuentes e Iconos (FontAwesome y Bootstrap) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- 
      Como Handesk no usa Vite por defecto, usamos asset() para cargar 
      tus estilos personalizados. Asegúrate de tener tu archivo app.css en public/css/
    -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div id="app">
        <!-- Contenedor principal donde se inyectará la tarjeta del login que hicimos antes -->
        <main class="py-4 container">
            @yield('content')
        </main>
    </div>

    <!-- Scripts básicos de interactividad (Bootstrap y Alertas) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Espacio dinámico para inyectar scripts adicionales si una vista lo requiere -->
    @stack('scripts')
</body>
</html>
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Lógica de Negocio: Filtro de acceso exclusivo para el equipo de Sistemas.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario tiene sesión activa y su rol es administrador (1), lo dejamos pasar
        if (auth()->check() && auth()->user()->admin == 1) {
            return $next($request);
        }

        // Si es un usuario final intentando husmear, lo pateamos a su propio portal
        return redirect()->route('cliente.tickets.index');
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Si la contraseña es "12345", forzamos el cambio
            if (Hash::check('12345', auth()->user()->password)) {
                // Evitamos loop infinito si ya estamos en la ruta de cambio
                if (! $request->routeIs('password.force-change') && ! $request->routeIs('password.force-update') && ! $request->routeIs('logout')) {
                    return redirect()->route('password.force-change');
                }
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * El usuario ha sido autenticado.
     * Aquí definimos la lógica de roles (Sistemas vs Usuario Normal).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $administradoresTI = [
            'jeduardo@ragon.com.mx',
            'jramirez@ragon.com.mx',
            'analista.datos@ragon.com.mx',
            'ricardo.mancilla@ragon.com.mx',
            'rguzman@ragon.com.mx',
        ];

        // 1. AUTO-CORRECCIÓN DE ROL: 
        // Verifica en tiempo real si el correo está en la lista y actualiza la BD
        $esAdmin = in_array(strtolower($user->email), $administradoresTI) ? 1 : 0;

        if ($user->admin != $esAdmin) {
            $user->admin = $esAdmin;
            $user->save();
        }

        // 2. REDIRECCIÓN INTELIGENTE
        // Si el usuario es parte del equipo de Sistemas (admin = 1)
        if ($user->admin == 1) {
            // Mandamos a la ruta nombrada 'dashboard' (que carga la vista welcome)
            return redirect()->route('dashboard');
        }

        // Si es un usuario normal (admin = 0)
        return redirect()->route('cliente.tickets.crear');
    }
}

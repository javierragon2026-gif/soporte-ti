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
        // Si el usuario es parte del equipo de Sistemas (admin = 1)
        if ($user->admin) {
            return redirect()->route('tickets.index');
        }

        // Si es un usuario normal (admin = 0)
        // Ajusta esta URL a la ruta donde pondrás el catálogo de errores
        return redirect('cliente/tickets/crear'); 
    }
}
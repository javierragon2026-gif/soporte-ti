<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketsAssignController extends Controller
{
    /**
     * Lógica de Negocio: Reasigna un requerimiento a un agente específico
     * y actualiza sus etiquetas.
     */
    public function store(Ticket $ticket, Request $request)
    {
        // 1. Validamos los datos entrantes del formulario
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'tags'    => 'nullable|string'
        ]);

        // 2. Actualizamos al responsable del caso
        $ticket->update([
            'user_id' => $request->user_id
        ]);

        // Nota de desarrollo: Si más adelante reactivamos la tabla de etiquetas (tags), 
        // aquí procesaremos el texto separado por comas que llega en $request->tags.

        // 3. Retornamos a la pantalla del ticket para ver los cambios
        return back();
    }
}
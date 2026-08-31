<?php

namespace App\Http\Controllers;

// Usamos la ruta moderna y limpia de Laravel
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketsAssignController extends Controller
{
    /**
     * Lógica de Negocio: Reasigna el requerimiento a un agente de sistemas
     * y actualiza las etiquetas correspondientes del caso.
     */
    public function store(Ticket $ticket, Request $request)
    {
        // 1. Validamos que los datos enviados cumplan con la estructura requerida
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'tags'    => 'nullable|string'
        ]);

        // 2. Actualizamos el responsable directo del requerimiento
        $ticket->update([
            'user_id' => $request->user_id
        ]);

        // 3. Redireccionamos de vuelta a la vista del detalle del ticket
        return back();
    }
}
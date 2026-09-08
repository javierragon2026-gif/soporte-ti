<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class RequesterCommentsController extends Controller
{
    /**
     * Lógica de Negocio: Guarda un comentario del usuario final.
     */
    public function store($id)
    {
        // Buscamos el ticket directamente por su ID numérico
        $ticket = Ticket::findOrFail($id);
        
        // CORRECCIÓN: Pasamos el usuario autenticado en vez de "null"
        $ticket->addComment(auth()->user(), request('body'), $this->getNewStatus());

        return back();
    }

    /**
     * Determina el estado del ticket basándose en la solicitud.
     */
    private function getNewStatus()
    {
        if (request('solved')) {
            return Ticket::STATUS_SOLVED;
        }
        if (request('reopen')) {
            return Ticket::STATUS_OPEN;
        }
        
        return null;
    }
}
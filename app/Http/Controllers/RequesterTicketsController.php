<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Response;

class RequesterTicketsController extends Controller
{
    public function show($id)
    {
        // Buscamos el ticket por su ID y garantizamos que le pertenezca al usuario logueado
        $ticket = \App\Models\Ticket::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('cliente.tickets.show', ['ticket' => $ticket]);
    }

    public function rate($id)
    {
        // Misma validación de seguridad para la calificación
        $ticket = \App\Models\Ticket::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $rated  = $ticket->rate(request('rating'));

        if (! $rated) {
            app()->abort(\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY, 'No se pudo calificar este ticket');
        }

        return back();
    }


    public function index()
    {
        // Consultamos únicamente mediante user_id
        $tickets = Ticket::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('cliente.tickets.index', compact('tickets'));
    }
}

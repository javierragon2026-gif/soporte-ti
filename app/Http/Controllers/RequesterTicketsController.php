<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Response;

class RequesterTicketsController extends Controller
{
    public function show($id)
    {
        // Buscamos el ticket por su ID y garantizamos que le pertenezca al usuario logueado
        $ticket = Ticket::with(['agent', 'comments.user', 'attachments'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('cliente.tickets.show', ['ticket' => $ticket]);
    }

    public function index()
    {
        // Consultamos con relaciones necesarias
        $tickets = Ticket::with(['agent'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('cliente.tickets.index', compact('tickets'));
    }
}

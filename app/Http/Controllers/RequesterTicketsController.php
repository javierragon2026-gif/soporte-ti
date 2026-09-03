<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Response;

class RequesterTicketsController extends Controller
{
    public function show($public_token)
    {
        $ticket = Ticket::findWithPublicToken($public_token);

        return view('requester.tickets.show', ['ticket' => $ticket]);
    }

    public function rate($public_token)
    {
        $ticket = Ticket::findWithPublicToken($public_token);
        $rated  = $ticket->rate(request('rating'));
        if (! $rated) {
            app()->abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Could not rate this ticket');
        }

        return view('requester.tickets.rated', ['ticket' => $ticket]);
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

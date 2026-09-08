<?php

namespace App\Http\Controllers;

use App\Repositories\TicketsIndexQuery;
use App\Repositories\TicketsRepository;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use BadChoice\Thrust\Controllers\ThrustController;

class TicketsController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'team'])
            ->latest('updated_at')
            ->paginate(25);

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        return view('tickets.show', ['ticket' => $ticket]);
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        // 1. Validar solo lo que el formulario realmente envía
        $request->validate([
            'body'  => 'required|string',
        ]);

        $user = auth()->user();

        // 2. Guardar el AnyDesk permanentemente si es nuevo
        if (empty($user->anydesk) && $request->filled('anydesk')) {
            $user->anydesk = $request->anydesk;
            $user->save();
        }

        // 3. Crear el ticket autogenerando el título con la categoría
        $ticket = \App\Models\Ticket::create([
            'title'   => 'Reporte de módulo: ' . $request->input('categoria', 'General'),
            'body'    => $request->body,
            'status'  => 1, // Nuevo / Abierto
            'user_id' => $user->id,
        ]);

        // 4. Guardar múltiples archivos adjuntos si existen
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $ticket->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'user_id' => $user->id,
                ]);
            }
        }

        // 5. Devolver el ID secuencial a la máquina tragamonedas
        if ($request->ajax() || $request->wantsJson()) {
            $ticketIdFormatted = 'TK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'ticket_id' => $ticketIdFormatted
            ]);
        }

        return redirect()->route('dashboard');
    }

    public function reopen(Ticket $ticket)
    {
        $ticket->updateStatus(Ticket::STATUS_OPEN);
        return back();
    }

    public function update(Ticket $ticket)
    {
        $this->validate(request(), [
            'requester' => 'required|array',
            'priority'  => 'required|integer',
            'type'      => 'integer',
        ]);

        $ticket->updateWith(request('requester'), request('priority'), request('type'))
            ->updateSummary(request('subject'), request('summary'));

        return back();
    }

    public function crearCliente(Request $request)
    {
        if (!$request->has('categoria')) {
            return view('cliente.tickets.crear');
        }

        return view('cliente.tickets.formulario');
    }
}

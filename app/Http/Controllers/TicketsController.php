<?php

namespace App\Http\Controllers;

use App\Repositories\TicketsIndexQuery;
use App\Repositories\TicketsRepository;
use App\Models\Ticket;

use BadChoice\Thrust\Controllers\ThrustController;

class TicketsController extends Controller
{
    public function index()
    {
        // Consulta nativa ordenada por los más recientes con paginación
        $tickets = Ticket::with(['user', 'team'])
            ->latest('updated_at')
            ->paginate(25);

        return view('tickets.index', compact('tickets'));
    }

    /*public function index(TicketsRepository $repository)
    {
        $ticketsQuery = TicketsIndexQuery::get($repository);
        $ticketsQuery = $ticketsQuery->select('tickets.*')->latest('updated_at');

        return view('tickets.index', ['tickets' => $ticketsQuery->paginate(25, ['tickets.user_id'])]);
    }*/

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
        // 1. Validar los datos esenciales
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        $user = auth()->user();

        // 2. Si el usuario no tiene AnyDesk, lo guardamos permanentemente
        if (empty($user->anydesk) && $request->filled('anydesk')) {
            $user->anydesk = $request->anydesk;
            $user->save();
        }

        // 3. Crear el ticket real en la base de datos
        $ticket = \App\Models\Ticket::create([
            'title'   => $request->title,
            'body'    => $request->body,
            'status'  => 1, // Nuevo / Abierto
            'user_id' => $user->id,
            // Si agregas la columna categoría a la BD, descomenta la siguiente línea:
            // 'categoria' => $request->categoria,
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

        // 5. Si la petición es AJAX (nuestra máquina tragamonedas), devolver el ID secuencial
        if ($request->ajax() || $request->wantsJson()) {
            // Formatear el ID real (Ej. Si el id es 15, devolverá TK-0015)
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
            //'subject'   => 'string|nullable',
            //'summary'   => 'string'
            //'title'      => 'required|min:3',
        ]);
        $ticket->updateWith(request('requester'), request('priority'), request('type'))
            ->updateSummary(request('subject'), request('summary'));

        return back();
    }

    public function crearCliente(\Illuminate\Http\Request $request)
    {
        // Si la URL no tiene categoría, mostramos el catálogo de módulos
        if (!$request->has('categoria')) {
            return view('cliente.tickets.crear');
        }

        // Si ya seleccionó una categoría (?categoria=SAP), cargamos el formulario
        return view('cliente.tickets.formulario');
    }
}

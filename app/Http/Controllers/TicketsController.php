<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketsController extends Controller
{
    /**
     * Muestra la bandeja principal de tickets para el equipo de Sistemas.
     * Incluye la lógica para que los filtros de búsqueda funcionen.
     */
    /**
     * Muestra la bandeja principal de tickets para el equipo de Sistemas.
     * Incluye la lógica para que TODOS los filtros de búsqueda funcionen.
     */
    public function index(Request $request)
    {
        // 1. Iniciamos la consulta base (siempre trayendo relaciones para evitar consultas N+1)
        $query = Ticket::with(['user', 'team'])->latest('updated_at');

        // 2. Búsqueda rápida (Asunto o ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // 3. Filtro: Por Estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Filtro: Por Equipo asignado
        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        // 5. NUEVO Filtro: Por Categoría / Módulo
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // 6. NUEVO Filtro: Por Prioridad
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // 7. NUEVO Filtro: Rango de Fechas (Usamos created_at para saber cuándo se abrió)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 8. Paginamos los resultados y los enviamos a tu vista
        // Mantenemos los parámetros en la URL (appends) para que no se pierdan al cambiar de página
        $tickets = $query->paginate(25)->appends($request->query());

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Muestra el panel de control y el hilo de interacciones de un ticket específico.
     */
    public function show(Ticket $ticket)
    {
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Muestra el formulario avanzado para crear un ticket interno (Vista de Sistemas).
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Guarda un nuevo requerimiento en la base de datos.
     * Soporta tanto el formulario del usuario final como el de TI.
     */
    public function store(Request $request)
    {
        // Validamos que venga la descripción obligatoria y archivos seguros
        $request->validate([
            'body'          => 'required|string',
            'attachments.*' => 'nullable|file|max:15360|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt,zip,rar,7z',
        ]);

        return DB::transaction(function () use ($request) {
            $user = auth()->user();

            // Guardamos el AnyDesk permanentemente si es nuevo
            if (empty($user->anydesk) && $request->filled('anydesk')) {
                $user->anydesk = $request->anydesk;
                $user->save();
            }

            // Autogeneramos el título si viene del catálogo del cliente
            $titulo = $request->filled('title')
                ? $request->title
                : 'Reporte de módulo: ' . $request->input('categoria', 'General');

            // Creamos el caso en la base de datos
            $ticket = Ticket::create([
                'title'     => $titulo,
                'body'      => $request->body,
                'status'    => $request->input('status', 1), // Abierto por defecto
                'user_id'   => $user->id,
                'categoria' => $request->input('categoria', 'SOPORTE'),
                'team_id'   => $request->input('team_id', null),
                'priority'  => $request->input('priority', 2), // Normal por defecto
            ]);

            // Guardamos múltiples archivos adjuntos si existen (Evidencia)
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

            // Devolver el ID secuencial real a la máquina tragamonedas (AJAX)
            if ($request->ajax() || $request->wantsJson()) {
                $ticketIdFormatted = 'TK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT);

                return response()->json([
                    'success' => true,
                    'ticket_id' => $ticketIdFormatted
                ]);
            }

            return redirect()->route('dashboard');
        });
    }

    /**
     * Lógica de Negocio Principal: Actualiza los datos operativos del ticket 
     * desde el Panel de Control lateral de Sistemas.
     */
    public function update(Request $request, Ticket $ticket)
    {
        // 1. Validamos la información
        $request->validate([
            'status'    => 'required|integer',
            'categoria' => 'nullable|string',
            'priority'  => 'required|integer',
            'agent_id'  => 'nullable|integer', // <-- Cambiado para validar al agente sin afectar al creador (user_id)
            'agent_id'  => 'nullable|integer', 
            'resolution_comment' => 'required_if:status,4,5|nullable|string|min:10',
        ], [
            'resolution_comment.required_if' => 'Debes escribir un comentario de resolución para poder cerrar o marcar como resuelto el ticket.',
            'resolution_comment.min' => 'El comentario de resolución debe ser un poco más detallado (mínimo 10 caracteres).',
        ]);

        // 2. Generar Bitácora (Audit Trail) de los cambios
        $changes = [];
        if ($ticket->status != $request->status) {
            $ticketFake = clone $ticket;
            $ticketFake->status = $request->status;
            $changes[] = "Estado (" . $ticketFake->statusName() . ")";
        }
        if ($ticket->categoria != $request->categoria) {
            $changes[] = "Categoría (" . ($ticket->categoria ?: 'Sin asignar') . " ➔ " . $request->categoria . ")";
        }
        if ($ticket->priority != $request->priority) {
            $changes[] = "Prioridad cambiada";
        }
        if ($ticket->agent_id != $request->agent_id) {
            $agente = \App\Models\User::find($request->agent_id);
            $nombre = $agente ? $agente->name : 'Sin asignar';
            $changes[] = "Asignado a: " . $nombre;
        }

        if (count($changes) > 0) {
            $msg = "<p>🔧 <strong>Actualización del Sistema:</strong> " . auth()->user()->name . " actualizó los siguientes campos: " . implode(', ', $changes) . "</p>";
            $ticket->addNote(auth()->user(), $msg);
        }
        
        // 2.5 Si se envió un comentario de resolución, agregarlo al hilo público
        if ($request->filled('resolution_comment')) {
            $ticket->comments()->create([
                'user_id' => auth()->user()->id,
                'body' => "<div style='background-color: #d1fae5; padding: 10px; border-radius: 8px; border-left: 4px solid #10b981;'><strong>✅ Notas de Resolución:</strong><br>" . nl2br(e($request->resolution_comment)) . "</div>",
                // assuming Comments don't have a specific 'is_resolution' field, we format it visually.
            ]);
        }

        // 3. Aplicamos los cambios al ticket
        $ticket->update([
            'status'    => $request->status,
            'categoria' => $request->categoria,
            'priority'  => $request->priority,
            'agent_id'  => $request->agent_id, // <-- Guardamos al responsable aquí
        ]);

        return back()->with('success', '¡El ticket ha sido actualizado y asignado!');
    }

    /**
     * Fuerza la reapertura de un ticket cerrado o finalizado.
     */
    public function reopen(Ticket $ticket)
    {
        $ticket->update(['status' => 2]); // Pasa a estado "En Proceso"
        return back();
    }

    /**
     * Muestra el catálogo de módulos o el formulario interactivo 
     * al usuario final (Cliente)
     */
    public function crearCliente(Request $request)
    {
        if (!$request->has('categoria')) {
            return view('cliente.tickets.crear');
        }

        return view('cliente.tickets.formulario');
    }
}
